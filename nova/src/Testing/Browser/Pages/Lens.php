<?php

namespace Laravel\Nova\Testing\Browser\Pages;

use Laravel\Dusk\Browser;

class Lens extends Index
{
    public $lens;

    /**
     * Create a new page instance.
     *
     * @param  string  $resourceName
     * @param  string  $lens
     * @return void
     */
    public function __construct($resourceName, $lens)
    {
        $this->lens = $lens;
        $this->resourceName = $resourceName;

        $this->setNovaPage("/resources/{$this->resourceName}/lens/{$this->lens}");
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertOk()->waitFor('@nova-resource-lens');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@nova-resource-lens' => '#app [data-testid="content"] [dusk="'.$this->lens.'-lens-component"]',
        ];
    }
}
