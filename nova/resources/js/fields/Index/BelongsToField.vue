<template>
  <div :class="`text-${field.textAlign}`">
    <span>
      <span v-if="field.viewable && field.value">
        <Link
          @click.stop
          :href="$url(`/resources/${field.resourceName}/${field.belongsToId}`)"
          class="link-default"
        >
          {{ field.value }}
        </Link>
      </span>
      <span v-else-if="field.value">{{ field.value }}</span>
      <span v-else>&mdash;</span>
    </span>
  </div>
</template>

<script>
export default {
  props: ['resourceName', 'field'],
}
</script>
