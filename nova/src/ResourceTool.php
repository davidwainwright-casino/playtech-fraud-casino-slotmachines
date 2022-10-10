<?php

namespace Laravel\Nova;

use Closure;
use Illuminate\Support\Str;

/**
 * @method static static make()
 */
class ResourceTool extends Panel
{
    use Makeable, ProxiesCanSeeToGate;

    /**
     * The resource tool element.
     *
     * @var \Laravel\Nova\Element
     */
    public $element;

    /**
     * The resource tool's component.
     *
     * @var string|null
     */
    public $toolComponent;

    /**
     * Create a new resource tool instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct($this->name(), [new ResourceToolElement($this->toolComponent())]);

        $this->element = $this->data[0];
    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Str::title(Str::snake(class_basename(get_class($this)), ' '));
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function toolComponent()
    {
        return $this->toolComponent ?? Str::kebab(class_basename(get_class($this)));
    }

    /**
     * Set the callback to be run to authorize viewing the card.
     *
     * @param  \Closure(\Illuminate\Http\Request):bool  $callback
     * @return $this
     */
    public function canSee(Closure $callback)
    {
        $this->element->canSee($callback);

        return $this;
    }

    /**
     * Set additional meta information for the resource tool.
     *
     * @param  array<string, mixed>  $meta
     * @return $this
     */
    public function withMeta(array $meta)
    {
        $this->element->withMeta($meta);

        return $this;
    }

    /**
     * Dynamically proxy method calls to meta information.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        return $this->withMeta([$method => ($parameters[0] ?? true)]);
    }
}
