<?php

namespace Laravel\Nova\Menu;

use Laravel\Nova\AuthorizedToSee;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;

/**
 * @method static static make(array $items)
 */
class MenuList implements \JsonSerializable
{
    use AuthorizedToSee;
    use Makeable;

    /**
     * The menu's component.
     *
     * @var string
     */
    public $component = 'menu-list';

    /**
     * The menu's items.
     *
     * @var \Laravel\Nova\Menu\MenuCollection
     */
    public $items;

    /**
     * Construct a new Menu List instance.
     *
     * @param  array  $items
     */
    public function __construct($items)
    {
        $this->items($items);
    }

    /**
     * Set menu's items.
     *
     * @param  array  $items
     * @return $this
     */
    public function items($items = [])
    {
        $this->items = new MenuCollection($items);

        return $this;
    }

    /**
     * Prepare the menu for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(NovaRequest::class);

        return [
            'component' => $this->component,
            'items' => $this->items->authorized($request)->withoutEmptyItems()->all(),
        ];
    }
}
