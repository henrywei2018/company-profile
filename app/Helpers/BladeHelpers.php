<?php
namespace App\Helpers;

class BladeHelpers 
{
    public static function safeString($value, $default = '', $context = 'unknown')
    {
        if (is_array($value)) {
            \Log::warning("Array passed where string expected in context: {$context}", [
                'value' => $value,
                'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
            ]);
            
            // Try to convert array to meaningful string
            if (empty($value)) {
                return $default;
            }
            
            // If it's an associative array, try to get a meaningful value
            if (isset($value['name'])) return (string) $value['name'];
            if (isset($value['title'])) return (string) $value['title'];
            if (isset($value['text'])) return (string) $value['text'];
            
            // Otherwise join string values
            $stringValues = array_filter($value, function($item) {
                return is_string($item) || is_numeric($item);
            });
            
            return !empty($stringValues) ? implode(' ', $stringValues) : $default;
        }
        
        if (is_null($value)) {
            return $default;
        }
        
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }
            
            \Log::warning("Object without __toString passed where string expected in context: {$context}", [
                'class' => get_class($value)
            ]);
            return $default;
        }
        
        return (string) $value;
    }
    
    public static function safeAttribute($model, $attribute, $default = '')
    {
        if (!$model || !is_object($model)) {
            return $default;
        }
        
        try {
            $value = $model->{$attribute} ?? $default;
            return self::safeString($value, $default, get_class($model) . '.' . $attribute);
        } catch (\Exception $e) {
            \Log::error("Error accessing model attribute: {$attribute}", [
                'model' => get_class($model),
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }
}