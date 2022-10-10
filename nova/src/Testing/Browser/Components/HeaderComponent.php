<?php

namespace Laravel\Nova\Testing\Browser\Components;

use Laravel\Dusk\Browser;

class HeaderComponent extends Component
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return 'div#app header';
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
            $browser->scrollIntoView($selector);
        });
    }

    /**
     * Open notification panel.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  callable|null  $notificationCallback
     * @return void
     */
    public function showNotificationPanel(Browser $browser, $notificationCallback = null)
    {
        $browser->closeCurrentDropdown()
                ->click('@notifications-dropdown')
                ->elsewhereWhenAvailable('@notifications-content', $notificationCallback ?? function ($browser) {
                });
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [];
    }
}
