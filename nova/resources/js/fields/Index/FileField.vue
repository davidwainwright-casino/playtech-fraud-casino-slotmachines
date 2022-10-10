<template>
  <div class="flex items-center w-8 h-8 mx-auto">
    <Loader v-if="loading && !missing" class="text-gray-300" width="30" />
    <Icon
      v-if="!loading && missing == true"
      type="exclamation-circle"
      class="text-red-500"
      v-tooltip="__('The image could not be loaded.')"
    />
    <div
      ref="image"
      class="overflow-hidden"
      :class="{ 'rounded-full': field.rounded, rounded: !field.rounded }"
    />
  </div>
</template>

<script>
import { minimum } from '@/util'

export default {
  props: ['viaResource', 'viaResourceId', 'resourceName', 'field'],

  data: () => ({
    loading: true,
    missing: false,
  }),

  mounted() {
    minimum(
      new Promise((resolve, reject) => {
        let image = new Image()
        image.addEventListener('load', () => resolve(image))
        image.addEventListener('error', () => reject())
        image.src = this.imageUrl
        image.className = 'object-cover h-8 w-8'
      })
    )
      .then(image => {
        image.draggable = false
        this.$refs.image.appendChild(image)
      })
      .catch(err => {
        console.log(err)
        this.missing = true
      })
      .finally(() => {
        this.loading = false
      })
  },

  computed: {
    imageUrl() {
      if (this.field.previewUrl && !this.field.thumbnailUrl) {
        return this.field.previewUrl
      }

      return this.field.thumbnailUrl
    },
  },
}
</script>
