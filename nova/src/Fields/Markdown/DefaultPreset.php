<?php

namespace Laravel\Nova\Fields\Markdown;

use Illuminate\Support\Str;

class DefaultPreset implements MarkdownPreset
{
    /**
     * Convert the given content from markdown to HTML.
     *
     * @param  string  $content
     * @return string
     */
    public function convert(string $content)
    {
        return Str::markdown($content);
    }
}
