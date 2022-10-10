<template>
  <div class="pt-2 pb-3">
    <h3 class="px-3 text-xs uppercase font-bold tracking-wide">
      <slot />
    </h3>

    <div class="mt-1 px-3">
      <slot name="filter" />
    </div>
  </div>
</template>
