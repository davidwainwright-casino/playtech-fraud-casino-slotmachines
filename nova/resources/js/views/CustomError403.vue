<template>
  <ErrorLayout status="403">
    <Head title="Forbidden" />
    <h1 class="text-[5rem] md:text-[4rem] font-normal leading-none">403</h1>
    <p class="text-2xl">{{ __('Hold Up!') }}</p>
    <p class="text-lg leading-normal">
      {{
        __("The government won't let us show you what's behind these doors")
      }}&hellip;
    </p>
  </ErrorLayout>
</template>

<script>
import ErrorLayout from '@/layouts/ErrorLayout'

export default {
  components: { ErrorLayout },
}
</script>
