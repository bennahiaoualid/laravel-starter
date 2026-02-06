<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectedCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $value,
        public bool $selected = false,
        public string $border = 'primary',
        public string $model = 'selectedValue'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.selected-card');
    }
}
