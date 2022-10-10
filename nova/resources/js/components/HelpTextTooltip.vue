<template>
  <div v-if="text" class="absolute right-0 bottom-0 p-2 z-20">
    <span class="sr-only" v-html="text" />
    <Tooltip :triggers="['click']" placement="top-start">
      <Icon
        :solid="true"
        type="question-mark-circle"
        class="cursor-pointer text-gray-400 dark:text-gray-500"
      />

      <template #content>
        <TooltipContent v-html="text" :max-width="width" />
      </template>
    </Tooltip>
  </div>
</template>

<script>
export default {
  props: ['text', 'width'],
}
</script>
