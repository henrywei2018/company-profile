<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'is_json',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_json' => 'boolean',
    ];
    
    /**
     * Get setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return Cache::remember('setting.' . $key, 60 * 24, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            if ($setting->is_json) {
                return json_decode($setting->value, true);
            }
            
            return $setting->value;
        });
    }
    
    /**
     * Set setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return Setting
     */
    public static function set($key, $value, $group = 'general')
    {
        $isJson = is_array($value) || is_object($value);
        
        $valueToStore = $isJson ? json_encode($value) : $value;
        
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $valueToStore,
                'group' => $group,
                'is_json' => $isJson,
            ]
        );
        
        Cache::forget('setting.' . $key);
        
        return $setting;
    }
    
    /**
     * Get all settings as an array.
     *
     * @return array
     */
    public static function getAllSettings()
    {
        return Cache::remember('settings.all', 60 * 24, function () {
            $settings = self::all();
            $result = [];
            
            foreach ($settings as $setting) {
                $value = $setting->is_json ? json_decode($setting->value, true) : $setting->value;
                $result[$setting->key] = $value;
            }
            
            return $result;
        });
    }
    
    /**
     * Get all settings for a specific group.
     *
     * @param string $group
     * @return array
     */
    public static function getGroup($group)
    {
        return Cache::remember('settings.group.' . $group, 60 * 24, function () use ($group) {
            $settings = self::where('group', $group)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $value = $setting->is_json ? json_decode($setting->value, true) : $setting->value;
                $result[$setting->key] = $value;
            }
            
            return $result;
        });
    }
    
    /**
     * Clear settings cache.
     */
    public static function clearCache()
    {
        Cache::forget('settings.all');
        
        $settings = self::all();
        
        foreach ($settings as $setting) {
            Cache::forget('setting.' . $setting->key);
        }
        
        $groups = self::select('group')->distinct()->pluck('group');
        
        foreach ($groups as $group) {
            Cache::forget('settings.group.' . $group);
        }
    }
}