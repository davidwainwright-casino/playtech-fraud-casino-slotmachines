<?php

namespace Laravel\Nova\Testing\Browser\Pages;

use Laravel\Dusk\Browser;

class Attach extends Page
{
    use InteractsWithRelations;

    public $resourceName;

    public $resourceId;

    public $relation;

    public $viaRelationship;

    public $polymorphic = false;

    /**
     * Create a new page instance.
     *
     * @param  string  $resourceName
     * @param  string  $resourceId
     * @param  string  $relation
     * @param  string|null  $viaRelationship
     * @param  bool  $polymorphic
     * @return void
     */
    public function __construct($resourceName, $resourceId, $relation, $viaRelationship = null, $polymorphic = false)
    {
        $this->relation = $relation;
        $this->resourceId = $resourceId;
        $this->resourceName = $resourceName;
        $this->viaRelationship = $viaRelationship;
        $this->polymorphic = $polymorphic;

        $this->setNovaPage("/resources/{$this->resourceName}/{$this->resourceId}/attach/{$this->relation}");
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return $this->novaPageUrl.'?'.http_build_query([
            'viaRelationship' => $this->viaRelationship ?? $this->relation,
            'polymorphic' => $this->polymorphic === true ? 1 : 0,
        ]);
    }

    /**
     * Select the attachable resource with the given ID.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string|int  $id
     * @return void
     */
    public function selectAttachable(Browser $browser, $id)
    {
        $this->selectRelation($browser, 'attachable-select', $id);
    }

    /**
     * Click the attach button.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function create(Browser $browser)
    {
        $browser->dismissToasted()
            ->click('@attach-button')
            ->pause(750);
    }

    /**
     * Click the update and continue editing button.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function createAndAttachAnother(Browser $browser)
    {
        $browser->dismissToasted()
            ->click('@attach-and-attach-another-button')
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
            ->click('@cancel-attach-button');
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
}
