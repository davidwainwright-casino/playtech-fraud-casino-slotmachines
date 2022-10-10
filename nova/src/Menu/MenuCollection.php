<?php

namespace Laravel\Nova\Menu;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use JsonSerializable;

/**
 * @template TKey of int
 * @template TValue of \Laravel\Nova\Menu\MenuGroup|\Laravel\Nova\Menu\MenuItem|\Laravel\Nova\Menu\MenuList|array
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
class MenuCollection extends Collection
{
    /**
     * Filter menus should be displayed for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static<int, \Laravel\Nova\Menu\MenuGroup|\Laravel\Nova\Menu\MenuItem|\Laravel\Nova\Menu\MenuList|array>
     */
    public function authorized(Request $request)
    {
        return $this->reject(function ($menu) use ($request) {
            return method_exists($menu, 'authorizedToSee') && ! $menu->authorizedToSee($request);
        })->values();
    }

    /**
     * Resolves menus and remove empty group or lists.
     *
     * @return static<int, \Laravel\Nova\Menu\MenuItem|array>
     */
    public function withoutEmptyItems()
    {
        return $this->transform(function ($menu) {
            if ($menu instanceof JsonSerializable) {
                $payload = $menu->jsonSerialize();

                if (($menu instanceof MenuGroup || $menu instanceof MenuList) && count($payload['items']) === 0) {
                    return null;
                }

                return $payload;
            }

            return $menu;
        })->filter()->values();
    }
}
