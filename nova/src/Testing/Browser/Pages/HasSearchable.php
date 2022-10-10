<?php

namespace Laravel\Nova\Testing\Browser\Pages;

use Illuminate\Support\Arr;
use Laravel\Dusk\Browser;

trait HasSearchable
{
    /**
     * Search for the given value for a searchable field attribute.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  string  $search
     * @return void
     */
    public function searchInput(Browser $browser, $attribute, $search)
    {
        $input = $browser->element('[dusk="'.$attribute.'-search-input"] input');

        if (is_null($input) || ! $input->isDisplayed()) {
            $browser->click("@{$attribute}-search-input")->pause(100);
        }

        $browser->type('[dusk="'.$attribute.'-search-input"] input', $search);
    }

    /**
     * Reset the searchable field.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  int  $resultIndex
     * @return void
     */
    public function resetSearchResult(Browser $browser, $attribute)
    {
        $this->cancelSelectingSearchResult($browser, $attribute);

        $browser->click("@{$attribute}-search-input-clear-button")->pause(150);
    }

    /**
     * Select the searchable field by result index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  int  $resultIndex
     * @return void
     */
    public function selectSearchResult(Browser $browser, $attribute, $resultIndex)
    {
        $browser->click("@{$attribute}-search-input-result-{$resultIndex}")->pause(150);
    }

    /**
     * Select the currently highlighted searchable field.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @return void
     */
    public function selectFirstSearchResult(Browser $browser, $attribute)
    {
        $this->selectSearchResult($browser, $attribute, 0);
    }

    /**
     * Select the currently highlighted searchable field.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @return void
     */
    public function cancelSelectingSearchResult(Browser $browser, $attribute)
    {
        $input = $browser->element('[dusk="'.$attribute.'-search-input"] input');

        if (! is_null($input) && $input->isDisplayed()) {
            $browser->keys('[dusk="'.$attribute.'-search-input"] input', '{escape}')->pause(150);
        }
    }

    /**
     * Search and select the searchable field by result index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  string  $search
     * @param  int  $resultIndex
     * @return void
     */
    public function searchAndSelectResult(Browser $browser, $attribute, $search, $resultIndex)
    {
        $this->searchInput($browser, $attribute, $search);

        $browser->pause(1500)
                ->assertValue('[dusk="'.$attribute.'-search-input"] input', $search);

        $this->selectSearchResult($browser, $attribute, $resultIndex);
    }

    /**
     * Search and select the currently highlighted searchable field.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  string  $search
     * @return void
     */
    public function searchAndSelectFirstResult(Browser $browser, $attribute, $search)
    {
        $this->searchAndSelectResult($browser, $attribute, $search, 0);
    }

    /**
     * Assert on searchable results.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  callable(\Laravel\Nova\Browser, string):void  $fieldCallback
     * @return void
     */
    public function assertSearchResult(Browser $browser, $attribute, callable $fieldCallback)
    {
        $browser->whenAvailable("@{$attribute}-search-input", function ($browser) use ($attribute, $fieldCallback) {
            $browser->click('')
                    ->pause(100)
                    ->elsewhere('', function ($browser) use ($attribute, $fieldCallback) {
                        $fieldCallback($browser, "{$attribute}-search-input");
                    });
        });

        $this->cancelSelectingSearchResult($browser, $attribute);
    }

    /**
     * Assert on searchable results is locked to single result.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  string  $search
     * @return void
     */
    public function assertSelectedSearchResult(Browser $browser, $attribute, $search)
    {
        $browser->whenAvailable("@{$attribute}-search-input", function ($browser) use ($attribute, $search) {
            $browser->assertSeeIn("@{$attribute}-search-input-selected", $search);
        });

        $this->assertSearchResult($browser, $attribute, function ($browser, $attribute) use ($search) {
            $browser->assertSeeIn("@{$attribute}-result-0", $search)
                        ->assertNotPresent("@{$attribute}-result-1")
                        ->assertNotPresent("@{$attribute}-result-2")
                        ->assertNotPresent("@{$attribute}-result-3")
                        ->assertNotPresent("@{$attribute}-result-4");
        });
    }

    /**
     * Assert on searchable results is empty.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @return void
     */
    public function assertEmptySearchResult(Browser $browser, $attribute)
    {
        $this->assertSearchResult($browser, $attribute, function ($browser, $attribute) {
            $browser->assertNotPresent("@{$attribute}-result-0")
                        ->assertNotPresent("@{$attribute}-result-1")
                        ->assertNotPresent("@{$attribute}-result-2")
                        ->assertNotPresent("@{$attribute}-result-3")
                        ->assertNotPresent("@{$attribute}-result-4");
        });
    }

    /**
     * Assert on searchable results is locked to single result.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @param  string|array  $search
     * @return void
     */
    public function assertSearchResultHas(Browser $browser, $attribute, $search)
    {
        $this->assertSearchResult($browser, $attribute, function ($browser, $attribute) use ($search) {
            foreach (Arr::wrap($search) as $keyword) {
                $browser->assertSeeIn("@{$attribute}-results", $keyword);
            }
        });
    }
}
