<template>
  <ErrorLayout>
    <Head title="Error" />
    <h1 class="text-[5rem] md:text-[4rem] font-normal leading-none">
      {{ __(':-(') }}
    </h1>
    <p class="text-2xl">{{ __('Whoops') }}&hellip;</p>
    <p class="text-lg leading-normal">
      {{ __('Nova experienced an unrecoverable error.') }}
    </p>
  </ErrorLayout>
</template>

<script>
import ErrorLayout from '@/layouts/ErrorLayout'

export default {
  components: { ErrorLayout },
}
</script>
