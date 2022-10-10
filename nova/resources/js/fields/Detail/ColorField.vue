<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <div
        class="rounded-lg inline-flex items-center justify-center border border-60 p-1"
      >
        <span
          class="block w-6 h-6"
          :style="{ borderRadius: '5px', backgroundColor: field.value }"
        />
      </div>
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
}
</script>
