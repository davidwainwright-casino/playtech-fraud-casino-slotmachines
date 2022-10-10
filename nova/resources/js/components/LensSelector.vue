<template>
  <Dropdown
    v-if="lenses.length > 0"
    class="hover:bg-gray-100 dark:hover:bg-gray-600 rounded"
  >
    <span class="sr-only">{{ __('Lens Dropdown') }}</span>
    <DropdownTrigger class="toolbar-button px-2">
      <Icon class="dark:text-gray-400" height="23" type="video-camera" />
    </DropdownTrigger>

    <template #menu>
      <DropdownMenu
        class="divide-y divide-gray-100 dark:divide-gray-800 divide-solid"
        width="auto"
      >
        <DropdownMenuItem
          v-for="lens in lenses"
          :key="lens.uriKey"
          :href="$url(`/resources/${resourceName}/lens/${lens.uriKey}`)"
          as="link"
          class="px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800"
        >
          {{ lens.name }}
        </DropdownMenuItem>
      </DropdownMenu>
    </template>
  </Dropdown>
</template>

<script>
export default {
  props: ['resourceName', 'lenses'],
}
</script>
