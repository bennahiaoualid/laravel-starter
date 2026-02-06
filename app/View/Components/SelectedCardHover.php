<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectedCardHover extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $value = '',
        public string $model = 'selectedValue',
        public string $grid = 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 items-stretch',
        public string $gap = 'gap-4'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.selected-card-hover');
    }
}
