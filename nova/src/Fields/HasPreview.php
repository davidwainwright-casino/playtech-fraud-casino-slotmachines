<?php

namespace Laravel\Nova\Fields;

trait HasPreview
{
    /**
     * The callback used to retrieve the preview URL.
     *
     * @var (callable(mixed, ?string, mixed):?string)|null
     */
    public $previewUrlCallback;

    /**
     * Specify the callback that should be used to retrieve the preview URL.
     *
     * @param  callable(mixed, ?string, mixed):?string  $previewUrlCallback
     * @return $this
     */
    public function preview(callable $previewUrlCallback)
    {
        $this->previewUrlCallback = $previewUrlCallback;

        return $this;
    }

    /**
     * Resolve the preview URL for the field.
     *
     * @return string|null
     */
    public function resolvePreviewUrl()
    {
        return is_callable($this->previewUrlCallback)
                    ? call_user_func($this->previewUrlCallback, $this->value, $this->getStorageDisk(), $this->resource)
                    : null;
    }
}
