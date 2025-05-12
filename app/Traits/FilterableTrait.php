<?php
// File: app/Traits/FilterableTrait.php

namespace App\Traits;

trait FilterableTrait
{
    /**
     * Apply filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters = [])
    {
        // Get filterable fields from model
        $filterable = $this->filterable ?? [];
        
        foreach ($filters as $field => $value) {
            // Skip empty values
            if (empty($value)) {
                continue;
            }
            
            // Only filter on allowed fields
            if (!in_array($field, $filterable)) {
                continue;
            }
            
            // Apply filter based on field type and value
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                switch ($field) {
                    case 'search':
                        $this->applySearchFilter($query, $value);
                        break;
                        
                    case 'date_from':
                        $query->whereDate('created_at', '>=', $value);
                        break;
                        
                    case 'date_to':
                        $query->whereDate('created_at', '<=', $value);
                        break;
                        
                    case 'year':
                        $query->where('year', $value);
                        break;
                        
                    default:
                        $query->where($field, $value);
                        break;
                }
            }
        }
        
        return $query;
    }
    
    /**
     * Apply search filter to multiple fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return void
     */
    protected function applySearchFilter($query, $value)
    {
        // Get searchable fields from model
        $searchable = $this->searchable ?? ['title', 'name', 'description'];
        
        $query->where(function ($query) use ($searchable, $value) {
            foreach ($searchable as $field) {
                $query->orWhere($field, 'LIKE', "%{$value}%");
            }
        });
    }
}