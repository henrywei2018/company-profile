<?php
namespace App\View\Components;
use Illuminate\View\Component;

class Icon extends Component
{
    public string $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function render()
    {
        return view('components.icon');
    }
}