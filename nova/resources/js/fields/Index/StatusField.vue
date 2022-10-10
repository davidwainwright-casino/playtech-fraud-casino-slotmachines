<template>
  <div class="flex items-center">
    <Badge class="whitespace-nowrap flex items-center" :class="field.typeClass">
      <span class="mr-1 -ml-1">
        <Loader v-if="field.type == 'loading'" width="20" class="mr-1" />
        <Icon
          v-if="field.type == 'failed'"
          :solid="true"
          type="exclamation-circle"
        />
        <Icon
          v-if="field.type == 'success'"
          :solid="true"
          type="check-circle"
        />
      </span>
      {{ field.value }}
    </Badge>
  </div>
</template>

<script>
export default {
  props: ['resourceName', 'field'],
}
</script>
