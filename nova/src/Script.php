<?php

namespace Laravel\Nova;

class Script extends Asset
{
    /**
     * Get the Asset URL.
     *
     * @return string
     */
    public function url()
    {
        if (! $this->isRemote()) {
            return "/nova-api/scripts/{$this->name}";
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
            'Content-Type' => 'application/javascript',
        ];
    }
}
