<template>
  <ErrorLayout status="404">
    <Head title="Page Not Found" />
    <h1 class="text-[5rem] md:text-[4rem] font-normal leading-none">404</h1>
    <p class="text-2xl">{{ __('Whoops') }}&hellip;</p>
    <p class="text-lg leading-normal">
      {{
        __(
          "We're lost in space. The page you were trying to view does not exist."
        )
      }}
    </p>
  </ErrorLayout>
</template>

<script>
import ErrorLayout from '@/layouts/ErrorLayout'

export default {
  components: { ErrorLayout },
}
</script>
