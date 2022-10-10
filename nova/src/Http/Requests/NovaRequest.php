<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string|null $resource
 * @property-read mixed|null $resourceId
 * @property-read string|null $relatedResource
 * @property-read mixed|null $relatedResourceId
 * @property-read string|null $viaResource
 * @property-read mixed|null $viaResourceId
 * @property-read string|null $viaRelationship
 * @property-read string|null $relationshipType
 */
class NovaRequest extends FormRequest
{
    use InteractsWithResources;
    use InteractsWithRelatedResources;
    use InteractsWithResourcesSelection;

    /**
     * Determine if this request is via a many-to-many relationship.
     *
     * @return bool
     */
    public function viaManyToMany()
    {
        return in_array(
            $this->relationshipType,
            ['belongsToMany', 'morphToMany']
        );
    }

    /**
     * Determine if this request is an inline create or attach request.
     *
     * @return bool
     */
    public function isInlineCreateRequest()
    {
        return $this->isCreateOrAttachRequest() && $this->inline === 'true';
    }

    /**
     * Determine if this request is a create or attach request.
     *
     * @return bool
     */
    public function isCreateOrAttachRequest()
    {
        return $this instanceof ResourceCreateOrAttachRequest
            || ($this->editing === 'true' && in_array($this->editMode, ['create', 'attach']));
    }

    /**
     * Determine if this request is an update or update-attached request.
     *
     * @return bool
     */
    public function isUpdateOrUpdateAttachedRequest()
    {
        return $this instanceof ResourceUpdateOrUpdateAttachedRequest
            || ($this->editing === 'true' && in_array($this->editMode, ['update', 'update-attached']));
    }

    /**
     * Determine if this request is a resource index request.
     *
     * @return bool
     */
    public function isResourceIndexRequest()
    {
        return $this instanceof ResourceIndexRequest;
    }

    /**
     * Determine if this request is a resource detail request.
     *
     * @return bool
     */
    public function isResourceDetailRequest()
    {
        return $this instanceof ResourceDetailRequest;
    }

    /**
     * Determine if this request is an action request.
     *
     * @return bool
     */
    public function isActionRequest()
    {
        return $this->segment(3) == 'actions';
    }

    /**
     * Determine if this request is either create, attach, update, update-attached or action request.
     *
     * @return bool
     */
    public function isFormRequest()
    {
        return $this->isCreateOrAttachRequest()
            || $this->isUpdateOrUpdateAttachedRequest()
            || $this->isActionRequest();
    }
}
