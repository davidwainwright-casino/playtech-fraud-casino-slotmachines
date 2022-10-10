<template>
  <div v-if="shouldShowButtons">
    <!-- Attach Related Models -->
    <component
      :is="component"
      class="flex-shrink-0"
      v-if="shouldShowAttachButton"
      dusk="attach-button"
      :href="
        $url(
          `/resources/${viaResource}/${viaResourceId}/attach/${resourceName}`,
          {
            viaRelationship: viaRelationship,
            polymorphic: relationshipType == 'morphToMany' ? '1' : '0',
          }
        )
      "
    >
      <slot>{{ __('Attach :resource', { resource: singularName }) }}</slot>
    </component>

    <!-- Create Related Models -->
    <component
      :is="component"
      class="flex-shrink-0"
      v-else-if="shouldShowCreateButton"
      dusk="create-button"
      :href="
        $url(`/resources/${resourceName}/new`, {
          viaResource: viaResource,
          viaResourceId: viaResourceId,
          viaRelationship: viaRelationship,
          relationshipType: relationshipType,
        })
      "
    >
      <span class="hidden md:inline-block">
        {{ label }}
      </span>
      <span class="inline-block md:hidden">
        {{ __('Create') }}
      </span>
    </component>
  </div>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: 'button',
      validator: val => ['button', 'outline-button'].includes(val),
    },

    label: {},
    singularName: {},
    resourceName: {},
    viaResource: {},
    viaResourceId: {},
    viaRelationship: {},
    relationshipType: {},
    authorizedToCreate: {},
    authorizedToRelate: {},
    alreadyFilled: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    component() {
      return {
        button: 'ButtonInertiaLink',
        'outline-button': 'OutlineButtonInertiaLink',
      }[this.type]
    },

    /**
     * Determine if any buttons should be displayed.
     */
    shouldShowButtons() {
      return this.shouldShowAttachButton || this.shouldShowCreateButton
    },

    /**
     * Determine if the attach button should be displayed.
     */
    shouldShowAttachButton() {
      return (
        (this.relationshipType == 'belongsToMany' ||
          this.relationshipType == 'morphToMany') &&
        this.authorizedToRelate
      )
    },

    /**
     * Determine if the create button should be displayed.
     */
    shouldShowCreateButton() {
      return (
        this.authorizedToCreate &&
        this.authorizedToRelate &&
        !this.alreadyFilled
      )
    },
  },
}
</script>
