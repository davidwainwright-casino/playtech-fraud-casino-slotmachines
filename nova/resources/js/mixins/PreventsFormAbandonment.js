import { mapGetters, mapMutations } from 'vuex'
import { Inertia } from '@inertiajs/inertia'

export default {
  created() {
    this.removeOnNavigationChangesEvent = Inertia.on('before', event => {
      this.removeOnNavigationChangesEvent()
      this.handlePreventFormAbandonmentOnInertia(event)
    })

    window.addEventListener(
      'beforeunload',
      this.handlePreventFormAbandonmentOnInertia
    )

    this.removeOnBeforeUnloadEvent = () => {
      window.removeEventListener(
        'beforeunload',
        this.handlePreventFormAbandonmentOnInertia
      )

      this.removeOnBeforeUnloadEvent = () => {}
    }
  },

  mounted() {
    window.onpopstate = event => {
      this.handlePreventFormAbandonmentOnPopState(event)
    }
  },

  beforeUnmount() {
    this.removeOnBeforeUnloadEvent()
  },

  unmounted() {
    this.removeOnNavigationChangesEvent()
    this.resetPushState()
  },

  data() {
    return {
      removeOnNavigationChangesEvent: null,
      removeOnBeforeUnloadEvent: null,
    }
  },

  methods: {
    ...mapMutations([
      'allowLeavingForm',
      'preventLeavingForm',
      'triggerPushState',
      'resetPushState',
    ]),

    /**
     * Prevent accidental abandonment only if form was changed.
     */
    updateFormStatus() {
      if (this.canLeaveForm == true) {
        this.triggerPushState()
      }

      this.preventLeavingForm()
    },

    handlePreventFormAbandonment(proceed, revert) {
      if (this.canLeaveForm) {
        proceed()
        return
      }

      const answer = window.confirm(
        this.__('Do you really want to leave? You have unsaved changes.')
      )

      if (answer) {
        proceed()
        return
      }

      revert()
    },

    handlePreventFormAbandonmentOnInertia(event) {
      this.handlePreventFormAbandonment(
        () => {
          this.handleProceedingToNextPage()
          this.allowLeavingForm()
        },
        () => {
          Inertia.ignoreHistoryState = true
          event.preventDefault()
          event.returnValue = ''

          this.removeOnNavigationChangesEvent = Inertia.on('before', event => {
            this.removeOnNavigationChangesEvent()
            this.handlePreventFormAbandonmentOnInertia(event)
          })
        }
      )
    },

    handlePreventFormAbandonmentOnPopState(event) {
      event.stopImmediatePropagation()
      event.stopPropagation()

      this.handlePreventFormAbandonment(
        () => {
          this.handleProceedingToPreviousPage()
          this.allowLeavingForm()
        },
        () => {
          this.triggerPushState()
        }
      )
    },

    handleProceedingToPreviousPage() {
      window.onpopstate = null
      Inertia.ignoreHistoryState = false

      this.removeOnBeforeUnloadEvent()

      if (!this.canLeaveFormToPreviousPage) {
        window.history.back()
      }
    },

    handleProceedingToNextPage() {
      window.onpopstate = null
      Inertia.ignoreHistoryState = false

      this.removeOnBeforeUnloadEvent()
    },
  },

  computed: {
    ...mapGetters(['canLeaveForm', 'canLeaveFormToPreviousPage']),
  },
}
