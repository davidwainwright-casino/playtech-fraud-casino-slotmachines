<?php

namespace Laravel\Nova\Testing\Browser\Pages;

use Laravel\Dusk\Browser;

class Dashboard extends Page
{
    public $dashboardName;

    /**
     * Create a new page instance.
     *
     * @param  string  $dashboardName
     * @return void
     */
    public function __construct($dashboardName = 'main')
    {
        $this->dashboardName = $dashboardName;

        $this->setNovaPage("/dashboards/{$this->dashboardName}");
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertOk()->waitFor('@nova-dashboard');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@nova-dashboard' => "[dusk='dashboard-{$this->dashboardName}']",
        ];
    }
}
