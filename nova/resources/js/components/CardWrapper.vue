<template>
  <component
    :class="[widthClass, heightClass]"
    :key="`${card.component}.${card.uriKey}`"
    class="h-full"
    :is="card.component"
    :card="card"
    :resource="resource"
    :resourceName="resourceName"
    :resourceId="resourceId"
    :lens="lens"
  />
</template>

<script>
export default {
  props: {
    card: {
      type: Object,
      required: true,
    },

    resource: {
      type: Object,
      required: false,
    },

    resourceName: {
      type: String,
      default: '',
    },

    resourceId: {
      type: [Number, String],
      default: '',
    },

    lens: {
      lens: String,
      default: '',
    },
  },

  computed: {
    /**
     * The class given to the card wrappers based on its width
     */
    widthClass() {
      return {
        full: 'md:col-span-12',
        '1/3': 'md:col-span-4',
        '1/2': 'md:col-span-6',
        '1/4': 'md:col-span-3',
        '2/3': 'md:col-span-8',
        '3/4': 'md:col-span-9',
      }[this.card.width]
    },

    heightClass() {
      return this.card.height == 'fixed' ? 'min-h-40' : ''
    },
  },
}
</script>
