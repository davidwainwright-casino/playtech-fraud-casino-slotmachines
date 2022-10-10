<?php

namespace Laravel\Nova\Fields;

/**
 * @method static static make(mixed $name, string|null $attribute = null, callable|null $resolveCallback = null)
 */
class Heading extends Field
{
    use SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'heading-field';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  (callable(mixed, mixed, ?string):mixed)|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct(null, $attribute ?? '', $resolveCallback);

        $this->withMeta(['value' => $name]);
        $this->hideFromIndex();
        $this->withMeta(['asHtml' => false]);
    }

    /**
     * Display the field as raw HTML using Vue.
     *
     * @return $this
     */
    public function asHtml()
    {
        return $this->withMeta(['asHtml' => true]);
    }
}
