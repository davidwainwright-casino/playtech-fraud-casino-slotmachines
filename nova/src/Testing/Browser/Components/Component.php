<?php

namespace Laravel\Nova\Testing\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    /**
     * Close current dropdown.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function closeCurrentDropdown(Browser $browser)
    {
        $browser->elsewhere('', function ($browser) {
            $overlay = $browser->element('[dusk="dropdown-overlay"]');

            if (! is_null($overlay) && $overlay->isDisplayed()) {
                $browser->click('@dropdown-overlay')->pause(250);
            }
        });
    }
}
