<?php

namespace Laravel\Nova\Testing\Browser\Pages;

use Laravel\Dusk\Browser;

class UpdateAttached extends Page
{
    public $resourceName;

    public $resourceId;

    public $relation;

    public $relatedId;

    public $viaRelationship;

    public $viaPivotId;

    /**
     * Create a new page instance.
     *
     * @param  string  $resourceName
     * @param  string  $resourceId
     * @param  string  $relation
     * @param  string  $relatedId
     * @param  string|null  $viaRelationship
     * @param  string|null  $viaPivotId
     * @return void
     */
    public function __construct($resourceName, $resourceId, $relation, $relatedId, $viaRelationship = null, $viaPivotId = null)
    {
        $this->relation = $relation;
        $this->relatedId = $relatedId;
        $this->resourceId = $resourceId;
        $this->resourceName = $resourceName;
        $this->viaRelationship = $viaRelationship;
        $this->viaPivotId = $viaPivotId;

        $this->setNovaPage("/resources/{$this->resourceName}/{$this->resourceId}/edit-attached/{$this->relation}/{$this->relatedId}");
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return $this->novaPageUrl.'?'.http_build_query(array_filter([
            'viaRelationship' => $this->viaRelationship ?? $this->relation,
            'viaPivotId' => $this->viaPivotId,
        ]));
    }

    /**
     * Click the update button.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function update(Browser $browser)
    {
        $browser->dismissToasted()
            ->click('@update-button')
            ->pause(750);
    }

    /**
     * Click the update and continue editing button.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function updateAndContinueEditing(Browser $browser)
    {
        $browser->dismissToasted()
            ->click('@update-and-continue-editing-button')
            ->pause(750);
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
            ->click('@cancel-update-attached-button');
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function assert(Browser $browser)
    {
        $browser->assertOk()->waitFor('@nova-form');
    }
}
