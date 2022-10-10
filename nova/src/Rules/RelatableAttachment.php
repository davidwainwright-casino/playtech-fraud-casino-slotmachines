<?php

namespace Laravel\Nova\Rules;

use Laravel\Nova\Nova;

class RelatableAttachment extends Relatable
{
    /**
     * Authorize that the user is allowed to relate this resource.
     *
     * @param  string  $resource
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    protected function authorize($resource, $model)
    {
        $parentResource = Nova::newResourceFromModel($this->request->findModelOrFail());

        return $parentResource->authorizedToAttachAny(
            $this->request, $model
        ) || $parentResource->authorizedToAttach(
            $this->request, $model
        );
    }

    /**
     * Determine if the relationship is "full".
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    protected function relationshipIsFull($model, $attribute, $value)
    {
        return false;
    }
}
