<script>
import DropdownComponent from './Dropdown'
import { mapGetters } from 'vuex'

export default {
  extends: DropdownComponent,

  watch: {
    mainMenuShown(value) {
      if (value === false && this.show === true) {
        this.hideMenu()
      }
    },
  },

  computed: {
    ...mapGetters(['mainMenuShown']),

    extraClasses() {
      return ['z-[60]']
    },
  },
}
</script>
