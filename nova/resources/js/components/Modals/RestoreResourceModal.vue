<template>
  <Modal data-testid="restore-resource-modal" :show="show" maxWidth="sm">
    <form
      @submit.prevent="$emit('confirm')"
      class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
      style="width: 460px"
    >
      <slot>
        <ModalHeader v-text="__('Restore Resource')" />
        <ModalContent>
          <p class="leading-normal">
            {{ __('Are you sure you want to restore the selected resources?') }}
          </p>
        </ModalContent>
      </slot>

      <ModalFooter>
        <div class="ml-auto">
          <LinkButton
            type="button"
            data-testid="cancel-button"
            dusk="cancel-restore-button"
            @click.prevent="$emit('close')"
            class="mr-3"
          >
            {{ __('Cancel') }}
          </LinkButton>

          <DefaultButton
            ref="confirmButton"
            data-testid="confirm-button"
            dusk="confirm-restore-button"
            type="submit"
          >
            {{ __('Restore') }}
          </DefaultButton>
        </div>
      </ModalFooter>
    </form>
  </Modal>
</template>

<script>
export default {
  emits: ['confirm', 'close'],

  props: {
    show: { type: Boolean, default: false },
  },

  /**
   * Mount the component.
   */
  mounted() {
    this.$nextTick(() => {
      // this.$refs.confirmButton.focus()
    })
  },
}
</script>
