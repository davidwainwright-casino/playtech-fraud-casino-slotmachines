<?php

namespace Laravel\Nova;

trait Metable
{
    /**
     * The meta data for the element.
     *
     * @var array<string, mixed>
     */
    public $meta = [];

    /**
     * Get additional meta information to merge with the element payload.
     *
     * @return array<string, mixed>
     */
    public function meta()
    {
        return $this->meta;
    }

    /**
     * Set additional meta information for the element.
     *
     * @param  array<string, mixed>  $meta
     * @return $this
     */
    public function withMeta(array $meta)
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }
}
