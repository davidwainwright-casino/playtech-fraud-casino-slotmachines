<?php

namespace Laravel\Nova\Testing\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as Dusk;
use Laravel\Nova\Nova;
use Laravel\Nova\Testing\Browser\Concerns\InteractsWithElements;

class Page extends Dusk
{
    use InteractsWithElements;

    public $novaPageUrl;

    public $queryParams;

    /**
     * Create a new page instance.
     *
     * @param  string  $path
     * @return void
     */
    public function __construct($path = '/')
    {
        $this->setNovaPage($path);
    }

    /**
     * Dismiss toasted messages.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function dismissToasted(Browser $browser)
    {
        $browser->script('Nova.$toasted.clear()');
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        if ($this->queryParams) {
            return $this->novaPageUrl.'?'.http_build_query($this->queryParams);
        }

        return $this->novaPageUrl;
    }

    /**
     * Assert page not found.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assertOk(Browser $browser)
    {
        $browser->waitForLocation($this->novaPageUrl)
                ->assertPathIs($this->novaPageUrl)
                ->waitFor('@nova-content');
    }

    /**
     * Assert page not found.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assertNotFound(Browser $browser)
    {
        $browser->on(new NotFound());
    }

    /**
     * Assert page not forbidden.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assertForbidden(Browser $browser)
    {
        $browser->on(new Forbidden());
    }

    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements(): array
    {
        return [
            '@nova-content' => '#app [data-testid="content"]',
            '@nova-form' => '#app [data-testid="content"] form:not([data-testid="form-button"])',
        ];
    }

    /**
     * Set Nova Page URL.
     *
     * @param  string  $path
     * @return void
     */
    protected function setNovaPage(string $path)
    {
        $this->novaPageUrl = Nova::path().'/'.trim($path, '/');
    }
}
