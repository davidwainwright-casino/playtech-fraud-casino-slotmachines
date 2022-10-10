<?php

namespace Laravel\Nova\Fields;

trait HasThumbnail
{
    /**
     * The callback used to retrieve the thumbnail URL.
     *
     * @var (callable(mixed, string, mixed):?string)|null
     */
    public $thumbnailUrlCallback;

    /**
     * Specify the callback that should be used to retrieve the thumbnail URL.
     *
     * @param  callable(mixed, string, mixed):?string  $thumbnailUrlCallback
     * @return $this
     */
    public function thumbnail(callable $thumbnailUrlCallback)
    {
        $this->thumbnailUrlCallback = $thumbnailUrlCallback;

        return $this;
    }

    /**
     * Resolve the thumbnail URL for the field.
     *
     * @return string|null
     */
    public function resolveThumbnailUrl()
    {
        return is_callable($this->thumbnailUrlCallback)
                    ? call_user_func($this->thumbnailUrlCallback, $this->value, $this->getStorageDisk(), $this->resource)
                    : null;
    }
}
