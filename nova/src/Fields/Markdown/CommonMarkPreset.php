<?php

namespace Laravel\Nova\Fields\Markdown;

use League\CommonMark\CommonMarkConverter;

class CommonMarkPreset implements MarkdownPreset
{
    /**
     * Convert the given content from markdown to HTML.
     *
     * @param  string  $content
     * @return string
     */
    public function convert(string $content)
    {
        return (string) (new CommonMarkConverter())->convert($content);
    }
}
