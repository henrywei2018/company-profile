<?php

namespace App\Traits;

trait HasActiveTrait
{
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function markActive()
    {
        $this->is_active = true;
        $this->save();
        
        return $this;
    }

    public function markInactive()
    {
        $this->is_active = false;
        $this->save();
        
        return $this;
    }

    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        $this->save();
        
        return $this;
    }
}