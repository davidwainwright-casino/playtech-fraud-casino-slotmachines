<template>
  <div :class="`text-${field.textAlign}`">
    <span>
      <span v-if="field.viewable && field.value">
        <Link
          @click.stop
          :href="$url(`/resources/${field.resourceName}/${field.morphToId}`)"
          class="link-default"
        >
          {{ field.resourceLabel }}: {{ field.value }}
        </Link>
      </span>
      <span v-else-if="field.value">
        {{ field.resourceLabel || field.morphToType }}: {{ field.value }}
      </span>
      <span v-else>-</span>
    </span>
  </div>
</template>

<script>
export default {
  props: ['resourceName', 'field'],
}
</script>
