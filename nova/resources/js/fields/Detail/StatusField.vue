<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <Badge
        v-if="field.value"
        class="whitespace-nowrap inline-flex items-center"
        :class="field.typeClass"
      >
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

      <span v-else>&mdash;</span>
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
}
</script>
