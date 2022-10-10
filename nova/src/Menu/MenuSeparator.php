<?php

namespace Laravel\Nova\Menu;

use Laravel\Nova\Makeable;

class MenuSeparator implements \JsonSerializable
{
    use Makeable;

    /**
     * The menu's component.
     *
     * @var string
     */
    public $component = 'menu-separator';

    /**
     * Prepare the menu for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'component' => $this->component,
        ];
    }
}
