<?php

namespace Laravel\Nova\Testing\Browser\Concerns;

use Carbon\CarbonInterface;
use Laravel\Dusk\Browser;

trait InteractsWithElements
{
    /**
     * Type on "date" input.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $selector
     * @param  \Carbon\CarbonInterface  $carbon
     * @return void
     */
    public function typeOnDate(Browser $browser, string $selector, CarbonInterface $carbon)
    {
        $browser->type($selector, $carbon->format('dmY'));
    }

    /**
     * Type on "datetime-local" input.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $selector
     * @param  \Carbon\CarbonInterface  $carbon
     * @return void
     */
    public function typeOnDateTimeLocal(Browser $browser, string $selector, CarbonInterface $carbon)
    {
        $browser->type($selector, $carbon->format('dmY'));
        $browser->keys($selector, ['{tab}']);
        $browser->type($selector, $carbon->format('hisa'));
    }
}
