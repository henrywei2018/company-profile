<?php

namespace App\Traits;

trait HasSortOrderTrait
{
    protected static function bootHasSortOrderTrait()
    {
        static::creating(function ($model) {
            // Set the sort order to be the highest plus one if not set
            if (!isset($model->sort_order)) {
                $model->sort_order = static::max('sort_order') + 1;
            }
        });
    }

    public function moveUp()
    {
        if ($this->sort_order <= 1) {
            return false;
        }
        
        $higherModel = static::where('sort_order', '<', $this->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();
            
        if (!$higherModel) {
            return false;
        }
        
        // Swap positions
        $oldPosition = $this->sort_order;
        $this->sort_order = $higherModel->sort_order;
        $higherModel->sort_order = $oldPosition;
        
        $this->save();
        $higherModel->save();
        
        return true;
    }

    public function moveDown()
    {
        $lowerModel = static::where('sort_order', '>', $this->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
            
        if (!$lowerModel) {
            return false;
        }
        
        // Swap positions
        $oldPosition = $this->sort_order;
        $this->sort_order = $lowerModel->sort_order;
        $lowerModel->sort_order = $oldPosition;
        
        $this->save();
        $lowerModel->save();
        
        return true;
    }

    public function scopeOrdered($query, $direction = 'asc')
    {
        return $query->orderBy('sort_order', $direction);
    }
}