<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Facades\Storage;

class VaporImage extends VaporFile
{
    use PresentsImages;

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = true;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):mixed)|null  $storageCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $storageCallback = null)
    {
        parent::__construct($name, $attribute, $storageCallback);

        $this->acceptedTypes('image/*');

        $this->thumbnail(function () {
            return $this->value ? Storage::disk($this->getStorageDisk())->temporaryUrl($this->value, now()->addMinutes(5)) : null;
        })->preview(function () {
            return $this->value ? Storage::disk($this->getStorageDisk())->temporaryUrl($this->value, now()->addMinutes(5)) : null;
        });
    }

    /**
     * Prepare the field element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), $this->imageAttributes());
    }
}
