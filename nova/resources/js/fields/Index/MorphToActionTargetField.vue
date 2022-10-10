<template>
  <Link
    @click.stop
    v-if="field.viewable && field.value && !isResourceBeingViewed"
    :href="$url(`/resources/${field.resourceName}/${field.morphToId}`)"
    class="no-underline text-primary-500 font-bold"
    :class="`text-${field.textAlign}`"
  >
    {{ field.resourceLabel }}: {{ field.value }}
  </Link>

  <span v-else-if="field.value">
    {{ field.resourceLabel || field.morphToType }}: {{ field.value }}
  </span>
  <span v-else> - </span>
</template>

<script>
export default {
  props: ['resourceName', 'viaResource', 'viaResourceId', 'field'],

  computed: {
    /**
     * Determine if the resource being viewed matches the field's value.
     */
    isResourceBeingViewed() {
      return (
        this.field.morphToType == this.viaResource &&
        this.field.morphToId == this.viaResourceId
      )
    },
  },
}
</script>
