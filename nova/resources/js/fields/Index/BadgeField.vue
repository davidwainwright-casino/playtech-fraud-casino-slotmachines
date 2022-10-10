<template>
  <div>
    <Badge :label="field.label" :extra-classes="field.typeClass">
      <template #icon>
        <span v-if="field.icon" class="mr-1 -ml-1">
          <Icon :solid="true" :type="field.icon" />
        </span>
      </template>
    </Badge>
  </div>
</template>

<script>
export default {
  props: ['resourceName', 'viaResource', 'viaResourceId', 'field'],
}
</script>
