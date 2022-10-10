<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <Excerpt :content="excerpt" :should-show="field.shouldShow" />
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  computed: {
    excerpt() {
      return this.field.previewFor
    },
  },
}
</script>
