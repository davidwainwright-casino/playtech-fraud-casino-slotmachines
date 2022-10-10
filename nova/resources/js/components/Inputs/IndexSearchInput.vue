<template>
  <div class="relative h-9 w-full md:w-1/3 md:flex-shrink-0 mb-6">
    <Icon
      type="search"
      width="20"
      class="absolute ml-2 text-gray-400"
      :style="{ top: '4px' }"
    />

    <RoundInput
      data-testid="search-input"
      dusk="search"
      class="appearance-none bg-white dark:bg-gray-800 shadow rounded-full h-8 w-full dark:focus:bg-gray-800"
      :placeholder="__('Search')"
      type="search"
      :value="keyword"
      @input="handleChange"
      spellcheck="false"
      :aria-label="__('Search')"
    />
  </div>
</template>

<script>
export default {
  emits: ['update:keyword'],

  props: {
    keyword: {
      type: String,
    },
  },

  methods: {
    /**
     * Update the field's internal value
     */
    handleChange(event) {
      this.$emit('update:keyword', event?.target?.value || '')
    },
  },
}
</script>
