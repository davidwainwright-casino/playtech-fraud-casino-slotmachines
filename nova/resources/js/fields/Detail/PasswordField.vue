<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <p class="text-90">
        &middot;&middot;&middot;&middot;&middot;&middot;&middot;&middot;&middot;
      </p>
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
}
</script>
