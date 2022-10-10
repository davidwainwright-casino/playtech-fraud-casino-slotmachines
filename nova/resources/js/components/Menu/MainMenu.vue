<template>
  <div
    v-if="hasItems"
    class="sidebar-menu pb-24 space-y-6"
    dusk="sidebar-menu"
    role="navigation"
  >
    <component
      :key="item.key"
      :is="item.component"
      v-for="(item, index) in mainMenu"
      :item="item"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'MainMenu',

  computed: {
    ...mapGetters(['mainMenu']),

    hasItems() {
      return this.mainMenu.length > 0
    },
  },
}
</script>
