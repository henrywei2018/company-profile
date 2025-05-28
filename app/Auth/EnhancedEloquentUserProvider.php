<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Arrayable;

class EnhancedEloquentUserProvider extends EloquentUserProvider
{
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (!($user instanceof User)) {
            return false;
        }

        $valid = $this->hasher->check($credentials['password'], $user->getAuthPassword());

        if (!$valid) {
            return false;
        }

        return $this->additionalValidationChecks($user, $credentials);
    }

    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->with(['roles', 'permissions'])
            ->first();
    }

    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->with(['roles', 'permissions'])
            ->first();

        if (!$retrievedModel) {
            return null;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
            ? $retrievedModel : null;
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || (count($credentials) === 1 && Str::contains($this->firstCredentialKey($credentials), 'password'))) {
            return null;
        }

        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        $user = $query->with(['roles', 'permissions'])->first();

        if ($user) {
            \Log::info('User credentials lookup successful', [
                'user_id' => $this->getUserId($user),
                'lookup_field' => array_keys(array_diff_key($credentials, ['password' => ''])),
                'ip' => request()->ip(),
            ]);
        } else {
            \Log::warning('User credentials lookup failed', [
                'lookup_data' => array_diff_key($credentials, ['password' => '']),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return $user;
    }

    protected function additionalValidationChecks(Authenticatable $user, array $credentials): bool
    {
        if (!($user instanceof User)) {
            return false;
        }

        if ($this->hasProperty($user, 'is_active') && !$user->is_active) {
            \Log::warning('Authentication attempt on inactive account', [
                'user_id' => $this->getUserId($user),
                'email' => $this->getUserEmail($user),
                'ip' => request()->ip(),
            ]);
            return false;
        }

        if ($this->hasProperty($user, 'locked_at') && $user->locked_at) {
            if ($user->locked_at->isFuture()) {
                \Log::warning('Authentication attempt on locked account', [
                    'user_id' => $this->getUserId($user),
                    'email' => $this->getUserEmail($user),
                    'locked_until' => $user->locked_at->toDateTimeString(),
                    'ip' => request()->ip(),
                ]);
                return false;
            } else {
                $this->unlockAccount($user);
            }
        }

        if ($this->hasProperty($user, 'expires_at') && $user->expires_at && $user->expires_at->isPast()) {
            \Log::warning('Authentication attempt on expired account', [
                'user_id' => $this->getUserId($user),
                'email' => $this->getUserEmail($user),
                'expired_at' => $user->expires_at->toDateTimeString(),
                'ip' => request()->ip(),
            ]);
            return false;
        }

        if (config('auth.verification.required', false)) {
            if ($this->hasProperty($user, 'email_verified_at') && !$user->email_verified_at) {
                \Log::info('Authentication attempt on unverified email', [
                    'user_id' => $this->getUserId($user),
                    'email' => $this->getUserEmail($user),
                    'ip' => request()->ip(),
                ]);
            }
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('client')) {
            if ($this->hasProperty($user, 'is_verified') && !$user->is_verified) {
                \Log::info('Authentication attempt on unverified client', [
                    'user_id' => $this->getUserId($user),
                    'email' => $this->getUserEmail($user),
                    'ip' => request()->ip(),
                ]);
            }
        }

        if ($this->shouldCheckPasswordStrength($user, $credentials)) {
            if (!$this->validatePasswordStrength($credentials['password'])) {
                \Log::warning('Weak password detected during authentication', [
                    'user_id' => $this->getUserId($user),
                    'email' => $this->getUserEmail($user),
                    'ip' => request()->ip(),
                ]);
                $this->flagForPasswordChange($user);
            }
        }

        return true;
    }

    protected function unlockAccount(Authenticatable $user): void
    {
        if (!($user instanceof User)) {
            return;
        }

        if (method_exists($user, 'update')) {
            $user->update([
                'locked_at' => null,
                'failed_login_attempts' => 0,
            ]);

            \Log::info('User account automatically unlocked', [
                'user_id' => $this->getUserId($user),
                'email' => $this->getUserEmail($user),
                'ip' => request()->ip(),
            ]);
        }
    }

    protected function shouldCheckPasswordStrength(Authenticatable $user, array $credentials): bool
    {
        if (!($user instanceof User)) {
            return false;
        }

        return $this->hasProperty($user, 'password_changed_at') &&
               (!$user->password_changed_at || $user->password_changed_at->lt(now()->subMonths(6)));
    }

    protected function validatePasswordStrength(string $password): bool
    {
        return strlen($password) >= 8 &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[0-9]/', $password) &&
               preg_match('/[^A-Za-z0-9]/', $password);
    }

    protected function flagForPasswordChange(Authenticatable $user): void
    {
        if (!($user instanceof User)) {
            return;
        }

        if (method_exists($user, 'update') && $this->hasProperty($user, 'requires_password_change')) {
            $user->update(['requires_password_change' => true]);
        }
    }

    protected function firstCredentialKey(array $credentials): string
    {
        foreach ($credentials as $key => $value) {
            return $key;
        }
        return '';
    }

    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');
        $model = new $class;

        if (method_exists($model, 'setConnection') && isset($this->connection)) {
            $model->setConnection($this->connection);
        }

        return $model;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel($model): void
    {
        $this->model = $model;
    }

    protected function newModelQuery($model = null)
    {
        $model = $model ?: $this->createModel();
        return $model->newQuery();
    }

    public function needsPasswordChange(Authenticatable $user): bool
    {
        if (!($user instanceof User)) {
            return false;
        }

        if ($this->hasProperty($user, 'requires_password_change') && $user->requires_password_change) {
            return true;
        }

        if ($this->hasProperty($user, 'password_changed_at')) {
            $maxPasswordAge = config('app.max_password_age_days', 90);
            if (!$user->password_changed_at || $user->password_changed_at->lt(now()->subDays($maxPasswordAge))) {
                return true;
            }
        }

        return false;
    }

    public function getLoginStats(Authenticatable $user): array
    {
        if (!($user instanceof User)) {
            return [];
        }

        return [
            'last_login_at' => $this->hasProperty($user, 'last_login_at') ? $user->last_login_at : null,
            'last_login_ip' => $this->hasProperty($user, 'last_login_ip') ? $user->last_login_ip : null,
            'login_count' => $this->hasProperty($user, 'login_count') ? $user->login_count : 0,
            'failed_login_attempts' => $this->hasProperty($user, 'failed_login_attempts') ? $user->failed_login_attempts : 0,
            'is_locked' => $this->hasProperty($user, 'locked_at') && $user->locked_at && $user->locked_at->isFuture(),
            'locked_until' => $this->hasProperty($user, 'locked_at') && $user->locked_at && $user->locked_at->isFuture() ? $user->locked_at : null,
        ];
    }

    protected function hasProperty(Authenticatable $user, string $property): bool
    {
        return property_exists($user, $property) || 
               (method_exists($user, 'getAttribute') && !is_null($user->getAttribute($property)));
    }

    protected function getUserId(Authenticatable $user)
    {
        return method_exists($user, 'getKey') ? $user->getKey() : ($this->hasProperty($user, 'id') ? $user->id : 'unknown');
    }

    protected function getUserEmail(Authenticatable $user): string
    {
        return $this->hasProperty($user, 'email') ? $user->email : 'unknown';
    }
}
