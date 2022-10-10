<?php

namespace Laravel\Nova\Testing\Browser\Pages;

use Laravel\Dusk\Browser;

class Create extends Page
{
    use InteractsWithRelations;

    public $resourceName;

    /**
     * Create a new page instance.
     *
     * @param  string  $resourceName
     * @param  array  $queryParams
     * @return void
     */
    public function __construct($resourceName, $queryParams = [])
    {
        $this->resourceName = $resourceName;
        $this->queryParams = $queryParams;

        $this->setNovaPage("/resources/{$this->resourceName}/new");
    }

    /**
     * Click the create button.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function create(Browser $browser)
    {
        $browser->dismissToasted()
            ->click('@create-button')
            ->pause(500);
    }

    /**
     * Click the create and add another button.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function createAndAddAnother(Browser $browser)
    {
        $browser->dismissToasted()
            ->click('@create-and-add-another-button')
            ->pause(500);
    }

    /**
     * Click the cancel button.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function cancel(Browser $browser)
    {
        $browser->dismissToasted()
            ->click('@cancel-create-button');
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertOk()->waitFor('@nova-form');
    }

    /**
     * Assert that there are no search results.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $resourceName
     * @return void
     */
    public function assertNoRelationSearchResults(Browser $browser, $resourceName)
    {
        $browser->assertMissing('@'.$resourceName.'-search-input-result-0');
    }
}
