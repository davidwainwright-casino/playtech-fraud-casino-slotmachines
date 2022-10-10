<?php

namespace Laravel\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Menu\MenuItem;

abstract class Dashboard extends Element implements HasMenu
{
    use AuthorizedToSee;
    use Metable;
    use Makeable;
    use ProxiesCanSeeToGate;

    /**
     * The displayable name of the dashboard.
     *
     * @var string
     */
    public $name;

    /**
     * Get the key value for the dashboard.
     *
     * @return string
     */
    public function key()
    {
        return md5($this->label());
    }

    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Nova::humanize($this);
    }

    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public function label()
    {
        return $this->name();
    }

    /**
     * Get the URI key of the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return Str::singular(Str::snake(class_basename(get_called_class()), '-'));
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    abstract public function cards();

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuItem::dashboard(static::class);
    }
}
