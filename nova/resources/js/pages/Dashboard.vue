<template>
  <DashboardView :name="name" />
</template>

<script>
import DashboardView from '@/views/Dashboard'

export default {
  name: 'Dashboard',

  components: {
    DashboardView,
  },

  props: {
    name: {
      type: String,
      required: false,
      default: 'main',
    },
  },
}
</script>
