<?php

namespace Laravel\Nova;

class Style extends Asset
{
    /**
     * Get the Asset URL.
     *
     * @return string
     */
    public function url()
    {
        if (! $this->isRemote()) {
            return "/nova-api/styles/{$this->name}";
        }

        return $this->path;
    }

    /**
     * Get the response headers for the asset.
     *
     * @return array<string, string>
     */
    public function toResponseHeaders()
    {
        return [
            'Content-Type' => 'text/css',
        ];
    }
}
