<?php

namespace Laravel\Nova\Menu;

use Illuminate\Support\Traits\Macroable;
use Laravel\Nova\AuthorizedToSee;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;

/**
 * @method static static make(string $name, array $items = [])
 */
class MenuGroup implements \JsonSerializable
{
    use AuthorizedToSee;
    use Makeable;
    use Macroable;

    /**
     * The menu's component.
     *
     * @var string
     */
    public $component = 'menu-group';

    /**
     * The menu's name.
     *
     * @var string
     */
    public $name;

    /**
     * The menu's items.
     *
     * @var \Laravel\Nova\Menu\MenuCollection
     */
    public $items;

    /**
     * The menu's collapsable state.
     *
     * @var bool
     */
    public $collapsable = false;

    /**
     * Construct a new Menu Group instance.
     *
     * @param  string  $name
     * @param  array  $items
     */
    public function __construct($name, $items = [])
    {
        $this->name = $name;
        $this->items = new MenuCollection($items);
    }

    /**
     * Set the menu group as collapsable.
     *
     * @return $this
     */
    public function collapsable()
    {
        $this->collapsable = true;

        return $this;
    }

    /**
     * Set the menu group as collapsable.
     *
     * @return $this
     */
    public function collapsible()
    {
        return $this->collapsable();
    }

    /**
     * Get the menu's unique key.
     *
     * @return string
     */
    public function key()
    {
        return md5($this->name.$this->items->reduce(function ($carry, $item) {
            return $carry.'-'.$item->name;
        }, ''));
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
            'name' => $this->name,
            'items' => $this->items->authorized($request)->withoutEmptyItems()->all(),
            'collapsable' => $this->collapsable,
            'key' => $this->key(),
        ];
    }
}
