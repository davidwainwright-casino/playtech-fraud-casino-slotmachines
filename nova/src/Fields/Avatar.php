<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Contracts\Cover;

class Avatar extends Image implements Cover
{
    /**
     * Create a new field.
     *
     * @param  string|null  $name
     * @param  string|null  $attribute
     * @param  string|null  $disk
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):mixed)|null  $storageCallback
     * @return void
     */
    public function __construct($name = 'Avatar', $attribute = null, $disk = null, $storageCallback = null)
    {
        parent::__construct($name, $attribute, $disk, $storageCallback);

        $this->rounded();
    }

    /**
     * Create Avatar field using Gravatar service.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @return \Laravel\Nova\Fields\Gravatar
     */
    public static function gravatar($name = 'Avatar', $attribute = 'email')
    {
        return new Gravatar($name, $attribute);
    }

    /**
     * Create Avatar field using ui-avatars service.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @return \Laravel\Nova\Fields\UiAvatar
     */
    public static function uiavatar($name = 'Avatar', $attribute = 'name')
    {

        return new UiAvatar($name, $attribute);
    }
}
