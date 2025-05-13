<?php


namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Modal extends Component
{
    /**
     * Modal ID
     */
    public string $id;
    
    /**
     * Modal size (sm, md, lg, xl)
     */
    public string $size;
    
    /**
     * Modal title
     */
    public ?string $title;
    
    /**
     * Modal footer
     */
    public bool $footer;
    
    /**
     * Close button text
     */
    public string $closeText;
    
    /**
     * Submit button text
     */
    public ?string $submitText;
    
    /**
     * Submit button color
     */
    public string $submitColor;
    
    /**
     * Static backdrop (prevents closing when clicking outside)
     */
    public bool $staticBackdrop;
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id, 
        string $size = 'md', 
        ?string $title = null, 
        bool $footer = true,
        string $closeText = 'Close',
        ?string $submitText = 'Save',
        string $submitColor = 'primary',
        bool $staticBackdrop = false
    ) {
        $this->id = $id;
        $this->size = $size;
        $this->title = $title;
        $this->footer = $footer;
        $this->closeText = $closeText;
        $this->submitText = $submitText;
        $this->submitColor = $submitColor;
        $this->staticBackdrop = $staticBackdrop;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.modal');
    }
}