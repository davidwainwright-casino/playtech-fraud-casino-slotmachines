<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <Link
        v-if="field.viewable && field.value"
        :href="$url(`/resources/${field.resourceName}/${field.morphToId}`)"
        class="no-underline font-bold link-default"
      >
        {{ field.name }}: {{ field.value }} ({{ field.resourceLabel }})
      </Link>
      <p v-else-if="field.morphToId && field.resourceLabel !== null">
        {{ field.name }}: {{ field.morphToId }} ({{ field.resourceLabel }})
      </p>
      <p v-else-if="field.morphToId && field.resourceLabel === null">
        {{ field.morphToType }}: {{ field.morphToId }}
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
