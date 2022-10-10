<template>
  <a
    href="https://nova.laravel.com/licenses"
    v-if="!validLicense"
    class="inline-block text-red-500 text-xs font-bold mt-1 text-center uppercase"
  >
    {{ __(`Unregistered`) }}
  </a>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  computed: mapGetters(['validLicense']),
}
</script>
