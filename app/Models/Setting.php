<?php
// File: app/Models/Setting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'is_json',
    ];

    protected $casts = [
        'is_json' => 'boolean',
    ];

    public function getValueAttribute($value)
    {
        if ($this->is_json) {
            return json_decode($value, true);
        }
        return $value;
    }

    public function setValueAttribute($value)
    {
        if ($this->is_json && is_array($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->value;
    }

    public static function set($key, $value, $group = 'general', $isJson = false)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return self::create([
                'key' => $key,
                'value' => $value,
                'group' => $group,
                'is_json' => $isJson,
            ]);
        }
        
        $setting->update([
            'value' => $value,
            'group' => $group,
            'is_json' => $isJson,
        ]);
        
        return $setting;
    }
}