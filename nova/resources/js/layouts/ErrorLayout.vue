<template>
  <div class="flex justify-center h-screen">
    <div
      class="z-50 flex items-center justify-center p-6"
      :dusk="`${status}-error-page`"
    >
      <div
        class="flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0 md:space-x-20"
        role="alert"
      >
        <ErrorPageIcon class="flex-shrink-0 md:w-[20rem]" />

        <div class="md:w-[20rem] md:flex-shrink-0 space-y-2 md:space-y-4">
          <slot />

          <Link
            :href="$url('/')"
            class="inline-flex items-center focus:outline-none focus:ring rounded border-2 border-primary-300 dark:border-gray-500 hover:border-primary-500 active:border-primary-400 dark:hover:border-gray-400 dark:active:border-gray-300 bg-white dark:bg-transparent text-primary-500 dark:text-gray-400 px-3 py-2 h-9 font-bold tracking-wide uppercase"
            tabindex="0"
            replace
          >
            {{ __('Go Home') }}
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    status: {
      type: String,
      default: '403',
    },
  },
}
</script>
