<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <Badge class="mt-1" :label="field.label" :extra-classes="field.typeClass">
        <template #icon>
          <span v-if="field.icon" class="mr-1 -ml-1">
            <Icon :solid="true" :type="field.icon" />
          </span>
        </template>
      </Badge>
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
}
</script>
