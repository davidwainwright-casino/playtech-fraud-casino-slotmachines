<template>
  <div class="py-6 px-1 md:px-2 lg:px-6">
    <div class="mx-auto py-8 max-w-sm flex justify-center text-black">
      <AppLogo class="h-8" />
    </div>

    <slot />
  </div>
</template>

<script>
export default {
  name: 'Auth',
}
</script>
