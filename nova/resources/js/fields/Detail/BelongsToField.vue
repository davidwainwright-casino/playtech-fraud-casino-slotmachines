<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <Link
        v-if="field.viewable && field.value"
        :href="$url(`/resources/${field.resourceName}/${field.belongsToId}`)"
        class="link-default"
      >
        {{ field.value }}
      </Link>
      <p v-else-if="field.value">{{ field.value }}</p>
      <p v-else>&mdash;</p>
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
}
</script>
