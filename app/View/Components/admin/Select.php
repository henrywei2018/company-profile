<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Select extends Component
{
    // Properties and constructor
    
    /**
     * Determine if an option is selected.
     *
     * @param mixed $optionValue
     * @return bool
     */
    public function isSelected($optionValue)
    {
        return $this->value !== null && $optionValue == $this->value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.admin.select');
    }
}