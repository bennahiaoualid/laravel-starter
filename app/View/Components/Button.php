<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $colorType;

    public ?string $customColorClass;

    public string $size;

    public string $ariaLabel;

    public $icon;

    public bool $islink = false;

    public bool $outline;

    public bool $disabled;

    public ?string $tooltip;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $tooltip,
        ?bool $islink,
        string $colorType = 'primary',
        bool $outline = false,
        bool $disabled = false,
        string $size = 'md',
        string $ariaLabel = '',
        $icon = null,
        ?string $custom_color_class = null
    ) {
        $this->tooltip = $tooltip;
        $this->islink = $islink ?? false;
        $this->colorType = $colorType;
        $this->outline = $outline;
        $this->disabled = $disabled;
        $this->size = $size;
        $this->ariaLabel = $ariaLabel;
        $this->icon = $icon;
        $this->customColorClass = $custom_color_class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
