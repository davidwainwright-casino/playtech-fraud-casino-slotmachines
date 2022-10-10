<template>
  <PanelItem :index="index" :field="field" :field-name="field.resourceLabel">
    <template #value>
      <Link
        v-if="field.viewable && field.value"
        :href="$url(`/resources/${field.resourceName}/${field.morphToId}`)"
        class="link-default"
      >
        {{ field.value }}
      </Link>
      <p v-else-if="field.value && field.resourceLabel === null">
        {{ field.morphToType }}: {{ field.value }}
      </p>
      <p v-else-if="field.value && field.resourceLabel !== null">
        {{ field.value }}
      </p>
      <p v-else>&mdash;</p>
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resourceName', 'resourceId', 'field'],
}
</script>
