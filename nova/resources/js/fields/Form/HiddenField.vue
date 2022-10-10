<template>
  <div class="hidden" :errors="errors">
    <input type="hidden" :value="value" />
  </div>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [DependentFormField, HandlesValidationErrors],
}
</script>
