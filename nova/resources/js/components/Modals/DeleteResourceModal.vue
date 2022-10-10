<template>
  <Modal
    data-testid="delete-resource-modal"
    :show="show"
    role="alertdialog"
    maxWidth="sm"
  >
    <form
      @submit.prevent="$emit('confirm')"
      class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
    >
      <slot>
        <ModalHeader v-text="__(`${uppercaseMode} Resource`)" />
        <ModalContent>
          <p class="leading-normal">
            {{
              __(
                'Are you sure you want to ' + mode + ' the selected resources?'
              )
            }}
          </p>
        </ModalContent>
      </slot>

      <ModalFooter>
        <div class="ml-auto">
          <LinkButton
            type="button"
            data-testid="cancel-button"
            dusk="cancel-delete-button"
            @click.prevent="$emit('close')"
            class="mr-3"
          >
            {{ __('Cancel') }}
          </LinkButton>

          <LoadingButton
            ref="confirmButton"
            dusk="confirm-delete-button"
            :processing="working"
            :disabled="working"
            component="DangerButton"
            type="submit"
          >
            {{ __(uppercaseMode) }}
          </LoadingButton>
        </div>
      </ModalFooter>
    </form>
  </Modal>
</template>

<script>
import startCase from 'lodash/startCase'

export default {
  emits: ['confirm', 'close'],

  props: {
    show: { type: Boolean, default: false },

    mode: {
      type: String,
      default: 'delete',
      validator: function (value) {
        return ['force delete', 'delete', 'detach'].indexOf(value) !== -1
      },
    },
  },

  data: () => ({
    working: false,
  }),

  methods: {
    handleClose() {
      this.$emit('close')
      this.working = false
    },

    handleConfirm() {
      this.$emit('confirm')
      this.working = true
    },
  },

  /**
   * Mount the component.
   */
  mounted() {
    this.$nextTick(() => {
      // this.$refs.confirmButton.button.focus()
    })
  },

  computed: {
    uppercaseMode() {
      return startCase(this.mode)
    },
  },
}
</script>
