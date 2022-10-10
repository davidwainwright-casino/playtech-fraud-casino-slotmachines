<?php

namespace Laravel\Nova\Testing\Browser\Components;

use Laravel\Dusk\Browser;

class FormComponent extends Component
{
    protected $selector;

    protected $formUniqueId;

    /**
     * Create a new component instance.
     *
     * @param  string|null  $selector
     * @return void
     */
    public function __construct($selector = null)
    {
        $this->selector = $selector;
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return $this->selector ?? '#app [data-testid="content"] form:not([data-testid="form-button"])';
    }

    /**
     * Set field value using JavaScript.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  mixed  $value
     * @return void
     */
    public function fieldValue(Browser $browser, $attribute, $value)
    {
        $browser->script("Nova.\$emit('{$this->formUniqueId}-{$attribute}-value', '{$value}')");
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function assert(Browser $browser)
    {
        tap($this->selector(), function ($selector) use ($browser) {
            $browser->pause(500)
                    ->waitFor($selector)
                    ->assertVisible($selector)
                    ->scrollIntoView($selector);

            $this->formUniqueId = $browser->attribute($selector, 'data-form-unique-id');
        });
    }
}
