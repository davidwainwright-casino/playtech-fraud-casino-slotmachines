<template>
  <!-- Pagination -->
  <div class="border-t border-gray-200 dark:border-gray-700">
    <component
      :is="paginationComponent"
      v-if="shouldShowPagination"
      :next="hasNextPage"
      :previous="hasPreviousPage"
      @load-more="loadMore"
      @page="selectPage"
      :pages="totalPages"
      :page="currentPage"
      :per-page="perPage"
      :resource-count-label="resourceCountLabel"
      :current-resource-count="currentResourceCount"
      :all-matching-resource-count="allMatchingResourceCount"
    >
      <span
        v-if="resourceCountLabel"
        class="text-xs px-4"
        :class="{
          'ml-auto': paginationComponent == 'pagination-links',
        }"
      >
        {{ resourceCountLabel }}
      </span>
    </component>
  </div>
</template>

<script>
export default {
  props: [
    'paginationComponent',
    'shouldShowPagination',
    'hasNextPage',
    'hasPreviousPage',
    'loadMore',
    'selectPage',
    'totalPages',
    'currentPage',
    'perPage',
    'resourceCountLabel',
    'currentResourceCount',
    'allMatchingResourceCount',
  ],
}
</script>
