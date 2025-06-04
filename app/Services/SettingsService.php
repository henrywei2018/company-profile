<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class SettingsService
{
    protected const CACHE_PREFIX = 'setting_';
    protected const CACHE_TTL = 3600; // 1 hour
    protected const ALL_SETTINGS_CACHE_KEY = 'all_settings';

    public function get(string $key, $default = null)
    {
        return Cache::remember(self::CACHE_PREFIX . $key, self::CACHE_TTL, function() use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return $setting->is_json ? json_decode($setting->value, true) : $setting->value;
        });
    }

    public function set(string $key, $value, string $group = 'general'): bool
    {
        try {
            $isJson = is_array($value) || is_object($value);
            $valueToStore = $isJson ? json_encode($value) : $value;
            
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $valueToStore,
                    'group' => $group,
                    'is_json' => $isJson,
                ]
            );

            // Clear caches
            Cache::forget(self::CACHE_PREFIX . $key);
            Cache::forget(self::ALL_SETTINGS_CACHE_KEY);

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to set setting '{$key}': " . $e->getMessage());
            return false;
        }
    }

    public function setMany(array $settings, string $group = 'general'): bool
    {
        try {
            foreach ($settings as $key => $value) {
                $isJson = is_array($value) || is_object($value);
                $valueToStore = $isJson ? json_encode($value) : $value;
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $valueToStore,
                        'group' => $group,
                        'is_json' => $isJson,
                    ]
                );
                
                Cache::forget(self::CACHE_PREFIX . $key);
            }

            Cache::forget(self::ALL_SETTINGS_CACHE_KEY);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to set multiple settings: ' . $e->getMessage());
            return false;
        }
    }

    public function getAll(): array
    {
        return Cache::remember(self::ALL_SETTINGS_CACHE_KEY, self::CACHE_TTL, function() {
            $settings = Setting::all();
            $result = [];
            
            foreach ($settings as $setting) {
                $value = $setting->is_json ? json_decode($setting->value, true) : $setting->value;
                $result[$setting->key] = $value;
            }
            
            return $result;
        });
    }

    public function getGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", self::CACHE_TTL, function() use ($group) {
            $settings = Setting::where('group', $group)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $value = $setting->is_json ? json_decode($setting->value, true) : $setting->value;
                $result[$setting->key] = $value;
            }
            
            return $result;
        });
    }

    public function forget(string $key): bool
    {
        try {
            Setting::where('key', $key)->delete();
            Cache::forget(self::CACHE_PREFIX . $key);
            Cache::forget(self::ALL_SETTINGS_CACHE_KEY);
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to delete setting '{$key}': " . $e->getMessage());
            return false;
        }
    }

    public function handleFileUpload(string $settingKey, UploadedFile $file, string $directory = 'settings'): ?string
    {
        try {
            // Delete old file if exists
            $oldFile = $this->get($settingKey);
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }

            // Store new file
            $path = $file->store($directory, 'public');
            $this->set($settingKey, $path);

            return $path;
        } catch (\Exception $e) {
            \Log::error("Failed to upload file for setting '{$settingKey}': " . $e->getMessage());
            return null;
        }
    }

    public function clearCache(): void
    {
        Cache::forget(self::ALL_SETTINGS_CACHE_KEY);
        
        // Clear individual setting caches
        $keys = Setting::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
        
        // Clear group caches
        $groups = Setting::select('group')->distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("settings_group_{$group}");
        }
    }

    // Quick access methods for common settings
    public function getSiteSettings(): array
    {
        return [
            'site_name' => $this->get('site_name', config('app.name')),
            'site_description' => $this->get('site_description', ''),
            'site_logo' => $this->get('site_logo'),
            'site_favicon' => $this->get('site_favicon'),
            'contact_email' => $this->get('contact_email', config('mail.from.address')),
            'contact_phone' => $this->get('contact_phone'),
            'footer_text' => $this->get('footer_text'),
        ];
    }

    public function getSeoSettings(): array
    {
        return [
            'meta_keywords' => $this->get('meta_keywords'),
            'google_analytics_id' => $this->get('google_analytics_id'),
            'google_site_verification' => $this->get('google_site_verification'),
        ];
    }

    public function getEmailSettings(): array
    {
        return [
            'admin_notification_email' => $this->get('admin_notification_email'),
            'email_from_name' => $this->get('email_from_name', config('app.name')),
            'notify_new_message' => $this->get('notify_new_message', true),
            'notify_new_quotation' => $this->get('notify_new_quotation', true),
            'notify_client_registration' => $this->get('notify_client_registration', true),
        ];
    }
}