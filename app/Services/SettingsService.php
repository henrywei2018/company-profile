<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

class SettingsService
{
    public function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function() use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public function set(string $key, $value): bool
    {
        try {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            Cache::forget("setting_{$key}");
            Cache::forget('all_settings');

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function setMany(array $settings): bool
    {
        try {
            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
                Cache::forget("setting_{$key}");
            }

            Cache::forget('all_settings');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAll(): array
    {
        return Cache::remember('all_settings', 3600, function() {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    public function forget(string $key): bool
    {
        try {
            Setting::where('key', $key)->delete();
            Cache::forget("setting_{$key}");
            Cache::forget('all_settings');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function handleFileUpload(string $settingKey, UploadedFile $file, string $directory = 'settings'): string
    {
        // Delete old file if exists
        $oldFile = $this->get($settingKey);
        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        // Store new file
        $path = $file->store($directory, 'public');
        $this->set($settingKey, $path);

        return $path;
    }

    public function updateEmailSettings(array $data): bool
    {
        $emailSettings = [
            'mail_from_address' => $data['mail_from_address'],
            'mail_from_name' => $data['mail_from_name'],
            'admin_email' => $data['admin_email'],
            'support_email' => $data['support_email'] ?? '',
            'message_email_enabled' => $data['message_email_enabled'] ?? false ? '1' : '0',
            'quotation_email_enabled' => $data['quotation_email_enabled'] ?? false ? '1' : '0',
        ];

        return $this->setMany($emailSettings);
    }

    public function updateCompanySettings(array $data): bool
    {
        $companySettings = [
            'company_name' => $data['company_name'],
            'company_tagline' => $data['company_tagline'] ?? '',
            'company_email' => $data['company_email'],
            'company_phone' => $data['company_phone'],
            'company_address' => $data['company_address'],
            'company_description' => $data['company_description'] ?? '',
        ];

        return $this->setMany($companySettings);
    }

    public function clearCache(): void
    {
        Cache::forget('all_settings');
        
        // Clear individual setting caches
        $keys = Setting::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting_{$key}");
        }
    }

    public function getEmailSettings(): array
    {
        return [
            'mail_from_address' => $this->get('mail_from_address', config('mail.from.address')),
            'mail_from_name' => $this->get('mail_from_name', config('mail.from.name')),
            'admin_email' => $this->get('admin_email'),
            'support_email' => $this->get('support_email'),
            'message_email_enabled' => $this->get('message_email_enabled', true),
            'quotation_email_enabled' => $this->get('quotation_email_enabled', true),
            'message_auto_reply_enabled' => $this->get('message_auto_reply_enabled', true),
            'quotation_client_confirmation_enabled' => $this->get('quotation_client_confirmation_enabled', true),
        ];
    }
}