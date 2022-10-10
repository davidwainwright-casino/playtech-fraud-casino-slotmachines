<?php

namespace Laravel\Nova\Testing\Browser\Components;

use Laravel\Dusk\Browser;

class IndexComponent extends Component
{
    public $resourceName;

    public $viaRelationship;

    /**
     * Create a new component instance.
     *
     * @param  string  $resourceName
     * @param  string|null  $viaRelationship
     * @return void
     */
    public function __construct($resourceName, $viaRelationship = null)
    {
        $this->resourceName = $resourceName;

        if (! is_null($viaRelationship) && $resourceName !== $viaRelationship) {
            $this->viaRelationship = $viaRelationship;
        }
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        $selector = '[dusk="'.$this->resourceName.'-index-component"]';

        return sprintf(
            (! is_null($this->viaRelationship) ? '%s[data-relationship="%s"]' : '%s'), $selector, $this->viaRelationship
        );
    }

    /**
     * Wait for table to be ready.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|null  $seconds
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitForTable(Browser $browser, $seconds = null)
    {
        $browser->whenAvailable('table[data-testid="resource-table"]', function ($browser) use ($seconds) {
            $browser->waitFor('> tbody', $seconds);
        }, $seconds);
    }

    /**
     * Wait for empty dialog to be ready.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|null  $seconds
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitForEmptyDialog(Browser $browser, $seconds = null)
    {
        $browser->waitFor('div[dusk="'.$this->resourceName.'-empty-dialog"]', $seconds);
    }

    /**
     * Search for the given string.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $search
     * @return void
     */
    public function searchFor(Browser $browser, $search)
    {
        $browser->type('@search', $search)->pause(1000);
    }

    /**
     * Clear the search field.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function clearSearch(Browser $browser)
    {
        $browser->clear('@search')->type('@search', ' ')->pause(1000);
    }

    /**
     * Click the sortable icon for the given attribute.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $attribute
     * @return void
     */
    public function sortBy(Browser $browser, $attribute)
    {
        $browser->click('@sort-'.$attribute)->waitForTable();
    }

    /**
     * Paginate to the next page of resources.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function nextPage(Browser $browser)
    {
        return $browser->click('@next')->waitForTable();
    }

    /**
     * Paginate to the previous page of resources.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function previousPage(Browser $browser)
    {
        return $browser->click('@previous')->waitForTable();
    }

    /**
     * Select all the the resources on current page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function selectAllOnCurrentPage(Browser $browser)
    {
        $browser->click('[dusk="select-all-dropdown"]')
                        ->elsewhereWhenAvailable('[dusk="select-all-button"]', function ($browser) {
                            $browser->check('input[type="checkbox"]');
                        })
                        ->pause(250)
                        ->closeCurrentDropdown();
    }

    /**
     * Un-select all the the resources on current page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function unselectAllOnCurrentPage(Browser $browser)
    {
        $browser->click('[dusk="select-all-dropdown"]')
                        ->elsewhereWhenAvailable('[dusk="select-all-button"]', function ($browser) {
                            $browser->uncheck('input[type="checkbox"]');
                        })
                        ->pause(250)
                        ->closeCurrentDropdown();
    }

    /**
     * Select all the matching resources.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function selectAllMatching(Browser $browser)
    {
        $browser->click('[dusk="select-all-dropdown"]')
                        ->elsewhereWhenAvailable('[dusk="select-all-matching-button"]', function ($browser) {
                            $browser->check('input[type="checkbox"]');
                        })
                        ->pause(250)
                        ->closeCurrentDropdown();
    }

    /**
     * Un-select all the matching resources.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function unselectAllMatching(Browser $browser)
    {
        $browser->click('[dusk="select-all-dropdown"]')
                        ->elsewhereWhenAvailable('[dusk="select-all-matching-button"]', function ($browser) {
                            $browser->uncheck('input[type="checkbox"]');
                        })
                        ->pause(250)
                        ->closeCurrentDropdown();
    }

    /**
     * Set the given filter and filter value for the index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  callable|null  $fieldCallback
     * @return void
     */
    public function runFilter(Browser $browser, $fieldCallback = null)
    {
        $browser->openFilterSelector()->pause(500);

        if (! is_null($fieldCallback)) {
            $browser->elsewhere('[data-menu-open="true"]', function ($browser) use ($fieldCallback) {
                $fieldCallback($browser);
            });
        }

        $browser->closeCurrentDropdown()->pause(1000);
    }

    /**
     * Set the per page value for the index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function setPerPage(Browser $browser, $value)
    {
        $this->runFilter($browser, function ($browser) use ($value) {
            $browser->whenAvailable('select[dusk="per-page-select"]', function ($browser) use ($value) {
                $browser->select('', $value);
            });
        });
    }

    /**
     * Set the given filter and filter value for the index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $name
     * @param  string  $value
     * @return void
     */
    public function selectFilter(Browser $browser, $name, $value)
    {
        $this->runFilter($browser, function ($browser) use ($name, $value) {
            $browser->whenAvailable('select[dusk="'.$name.'-select-filter"]', function ($browser) use ($value) {
                $browser->select('', $value);
            });
        });
    }

    /**
     * Indicate that trashed records should not be displayed.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function withoutTrashed(Browser $browser)
    {
        $this->runFilter($browser, function ($browser) {
            $browser->whenAvailable('[dusk="filter-soft-deletes"]', function ($browser) {
                $browser->select('select[dusk="trashed-select"]', '');
            });
        });
    }

    /**
     * Indicate that only trashed records should be displayed.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function onlyTrashed(Browser $browser)
    {
        $this->runFilter($browser, function ($browser) {
            $browser->whenAvailable('[dusk="filter-soft-deletes"]', function ($browser) {
                $browser->select('select[dusk="trashed-select"]', 'only');
            });
        });
    }

    /**
     * Indicate that trashed records should be displayed.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function withTrashed(Browser $browser)
    {
        $this->runFilter($browser, function ($browser) {
            $browser->whenAvailable('[dusk="filter-soft-deletes"]', function ($browser) {
                $browser->select('select[dusk="trashed-select"]', 'with');
            });
        });
    }

    /**
     * Open the action selector.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function openActionSelector(Browser $browser)
    {
        $browser->whenAvailable('@action-select', function ($browser) {
            $browser->click('')->pause(100);
        });
    }

    /**
     * Open the filter selector.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function openFilterSelector(Browser $browser)
    {
        $browser->whenAvailable('@filter-selector', function ($browser) {
            $browser->click('')->pause(100);
        });
    }

    /**
     * Open the action selector.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function openControlSelectorById(Browser $browser, $id)
    {
        $browser->closeCurrentDropdown()
                ->whenAvailable('@'.$id.'-control-selector', function ($browser) {
                    $browser->click('')->pause(300);
                });
    }

    /**
     * Select the action with the given URI key.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $uriKey
     * @param  callable  $fieldCallback
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function selectAction(Browser $browser, $uriKey, $fieldCallback)
    {
        $browser->whenAvailable('select[dusk="action-select"]', function ($browser) use ($uriKey) {
            $browser->select('', $uriKey);
        });

        $browser->elsewhereWhenAvailable('.modal[data-modal-open=true]', function ($browser) use ($fieldCallback) {
            $fieldCallback($browser);
        });
    }

    /**
     * Run the action with the given URI key.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $uriKey
     * @param  callable|null  $fieldCallback
     * @return void
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function runAction(Browser $browser, $uriKey, $fieldCallback = null)
    {
        $this->selectAction($browser, $uriKey, function ($browser) use ($fieldCallback) {
            if ($fieldCallback) {
                $fieldCallback($browser);
            }

            $browser->waitForText('Run Action')->click('[dusk="confirm-action-button"]')->pause(250);
        });
    }

    /**
     * Select the action with the given URI key.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @param  string  $uriKey
     * @param  callable  $fieldCallback
     * @return void
     */
    public function selectInlineAction(Browser $browser, $id, $uriKey, $fieldCallback)
    {
        $browser->openControlSelectorById($id)
            ->elsewhereWhenAvailable("@{$id}-inline-action-{$uriKey}", function ($browser) {
                $browser->click('');
            })->pause(500);

        $browser->elsewhereWhenAvailable('.modal[data-modal-open=true]', function ($browser) use ($fieldCallback) {
            $fieldCallback($browser);
        });
    }

    /**
     * Run the action with the given URI key.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @param  string  $uriKey
     * @param  callable|null  $fieldCallback
     * @return void
     */
    public function runInlineAction(Browser $browser, $id, $uriKey, $fieldCallback = null)
    {
        $this->selectInlineAction($browser, $id, $uriKey, function ($browser) use ($fieldCallback) {
            if ($fieldCallback) {
                $fieldCallback($browser);
            }

            $browser->click('[dusk="confirm-action-button"]')->pause(250);
        });
    }

    /**
     * Check the user at the given resource table row index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @return void
     */
    public function clickCheckboxForId(Browser $browser, $id)
    {
        $browser->click('[dusk="'.$id.'-row"] input.checkbox')
                        ->pause(175);
    }

    /**
     * Replicate the given resource table row index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @return void
     */
    public function replicateResourceById(Browser $browser, $id)
    {
        $browser->openControlSelectorById($id)
                        ->elsewhereWhenAvailable('@'.$id.'-replicate-button', function ($browser) {
                            $browser->click('');
                        })->pause(500);
    }

    /**
     * Preview the given resource table row index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @return void
     */
    public function previewResourceById(Browser $browser, $id)
    {
        $browser->openControlSelectorById($id)
                        ->elsewhereWhenAvailable("@{$id}-preview-button", function ($browser) {
                            $browser->click('');
                        })->pause(500);
    }

    /**
     * Delete the user at the given resource table row index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @return void
     */
    public function deleteResourceById(Browser $browser, $id)
    {
        $browser->click('@'.$id.'-delete-button')
                        ->elsewhereWhenAvailable('.modal[data-modal-open=true]', function ($browser) {
                            $browser->click('@confirm-delete-button');
                        })->pause(500);
    }

    /**
     * Restore the user at the given resource table row index.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @return void
     */
    public function restoreResourceById(Browser $browser, $id)
    {
        $browser->click('@'.$id.'-restore-button')
                        ->elsewhereWhenAvailable('.modal[data-modal-open=true]', function ($browser) {
                            $browser->click('@confirm-restore-button');
                        })->pause(500);
    }

    /**
     * Delete the resources selected via checkboxes.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function deleteSelected(Browser $browser)
    {
        $browser->click('@delete-menu')
                    ->pause(300)
                    ->elsewhere('', function ($browser) {
                        $browser->click('[dusk="delete-selected-button"]')
                            ->whenAvailable('.modal[data-modal-open=true]', function ($browser) {
                                $browser->click('@confirm-delete-button');
                            });
                    })->pause(1000);
    }

    /**
     * Restore the resources selected via checkboxes.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function restoreSelected(Browser $browser)
    {
        $browser->click('@delete-menu')
                    ->pause(300)
                    ->elsewhere('', function ($browser) {
                        $browser->click('[dusk="restore-selected-button"]')
                            ->whenAvailable('.modal[data-modal-open=true]', function ($browser) {
                                $browser->click('@confirm-restore-button');
                            });
                    })->pause(1000);
    }

    /**
     * Restore the resources selected via checkboxes.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function forceDeleteSelected(Browser $browser)
    {
        $browser->click('@delete-menu')
                    ->pause(300)
                    ->elsewhere('', function ($browser) {
                        $browser->click('[dusk="force-delete-selected-button"]')
                            ->whenAvailable('.modal[data-modal-open=true]', function ($browser) {
                                $browser->click('@confirm-delete-button');
                            });
                    })->pause(1000);
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
        $browser->pause(500);

        tap($this->selector(), function ($selector) use ($browser) {
            $browser->waitFor($selector)
                    ->assertVisible($selector)
                    ->scrollIntoView($selector);
        });
    }

    /**
     * Assert that the given resource is visible.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @param  int|string|null  $pivotId
     * @return void
     */
    public function assertSeeResource(Browser $browser, $id, $pivotId = null)
    {
        if (! is_null($pivotId)) {
            $browser->assertVisible('[dusk="'.$id.'-row"][data-pivot-id="'.$pivotId.'"]');
        } else {
            $browser->assertVisible('@'.$id.'-row');
        }
    }

    /**
     * Assert that the given resource is not visible.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int|string  $id
     * @param  int|string|null  $pivotId
     * @return void
     */
    public function assertDontSeeResource(Browser $browser, $id, $pivotId = null)
    {
        if (! is_null($pivotId)) {
            $browser->assertMissing('[dusk="'.$id.'-row"][data-pivot-id="'.$pivotId.'"]');
        } else {
            $browser->assertMissing('@'.$id.'-row');
        }
    }

    /**
     * Assert on the matching total matching count text.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int  $count
     * @return void
     */
    public function assertSelectAllMatchingCount(Browser $browser, $count)
    {
        $browser->click('@select-all-dropdown')
                        ->elsewhereWhenAvailable('@select-all-matching-button', function (Browser $browser) use ($count) {
                            $browser->assertSeeIn('span:nth-child(2)', $count);
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
