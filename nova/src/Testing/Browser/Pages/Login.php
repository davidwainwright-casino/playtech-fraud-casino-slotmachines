<?php

namespace Laravel\Nova\Testing\Browser\Pages;

use Laravel\Dusk\Browser;

class Login extends Page
{
    /**
     * Create a new page instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('/login');
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertOk();
    }

    /**
     * Assert page not found.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assertOk(Browser $browser)
    {
        $browser->waitForLocation($this->novaPageUrl)->assertPathIs($this->novaPageUrl);
    }
}
