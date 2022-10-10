<template>
  <div
    class="bg-gray-100 dark:bg-gray-800 rounded-t-lg flex border-b border-gray-200 dark:border-gray-700"
  >
    <div
      class="bg-clip w-48 uppercase font-bold text-xxs text-gray-500 tracking-wide px-3 py-2"
    >
      {{ keyLabel }}
    </div>

    <div
      class="bg-clip flex-grow uppercase font-bold text-xxs text-gray-500 tracking-wide px-3 py-2 border-l border-gray-200 dark:border-gray-700"
    >
      {{ valueLabel }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    keyLabel: {
      type: String,
    },
    valueLabel: {
      type: String,
    },
  },
}
</script>
