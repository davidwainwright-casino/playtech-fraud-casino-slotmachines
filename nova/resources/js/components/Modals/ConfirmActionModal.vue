<template>
  <Modal
    :show="show"
    @showing="handleShowingModal"
    @close-via-escape="handlePreventModalAbandonmentOnClose"
    data-testid="confirm-action-modal"
    tabindex="-1"
    role="dialog"
  >
    <form
      ref="theForm"
      autocomplete="off"
      @change="onUpdateFormStatus"
      @submit.prevent.stop="$emit('confirm')"
      :data-form-unique-id="formUniqueId"
      class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
    >
      <div>
        <ModalHeader v-text="action.name" />

        <p v-if="action.fields.length == 0" class="px-8 my-8">
          {{ action.confirmText }}
        </p>

        <div v-else>
          <!-- Validation Errors -->
          <validation-errors :errors="errors" />

          <!-- Action Fields -->
          <div
            class="action"
            v-for="field in action.fields"
            :key="field.attribute"
          >
            <component
              :is="'form-' + field.component"
              :errors="errors"
              :resource-name="resourceName"
              :field="field"
              :show-help-text="true"
              :form-unique-id="formUniqueId"
              mode="modal"
              :sync-endpoint="syncEndpoint"
              @field-changed="onUpdateFormStatus"
            />
          </div>
        </div>
      </div>

      <ModalFooter>
        <div class="flex items-center ml-auto">
          <CancelButton
            component="button"
            type="button"
            dusk="cancel-action-button"
            class="ml-auto mr-3"
            @click="$emit('close')"
          >
            {{ action.cancelButtonText }}
          </CancelButton>

          <LoadingButton
            type="submit"
            ref="runButton"
            dusk="confirm-action-button"
            :disabled="working"
            :loading="working"
            :component="action.destructive ? 'DangerButton' : 'DefaultButton'"
          >
            {{ action.confirmButtonText }}
          </LoadingButton>
        </div>
      </ModalFooter>
    </form>
  </Modal>
</template>

<script>
import { PreventsModalAbandonment } from '@/mixins'
import { uid } from 'uid/single'

export default {
  emits: ['confirm', 'close'],

  mixins: [PreventsModalAbandonment],

  props: {
    show: { type: Boolean, default: false },
    working: Boolean,
    resourceName: { type: String, required: true },
    action: { type: Object, required: true },
    selectedResources: { type: [Array, String], required: true },
    errors: { type: Object, required: true },
    endpoint: { type: String, required: false },
  },

  data: () => ({
    formUniqueId: uid(),
  }),

  created() {
    document.addEventListener('keydown', this.handleKeydown)
  },

  beforeUnmount() {
    document.removeEventListener('keydown', this.handleKeydown)
  },

  methods: {
    /**
     * Prevent accidental abandonment only if form was changed.
     */
    onUpdateFormStatus() {
      this.updateModalStatus()
    },

    /**
     * Handle focus when modal being shown.
     */
    handleShowingModal(e) {
      // If the modal has inputs, let's highlight the first one, otherwise
      // let's highlight the submit button
      this.$nextTick(() => {
        if (this.$refs.theForm) {
          let formFields = this.$refs.theForm.querySelectorAll(
            'input, textarea, select'
          )

          formFields.length > 0
            ? formFields[0].focus()
            : this.$refs.runButton.focus()
        } else {
          this.$refs.runButton.focus()
        }
      })
    },

    handlePreventModalAbandonmentOnClose() {
      this.handlePreventModalAbandonment(
        () => {
          this.$emit('close')
        },
        () => {
          e.stopPropagation()
        }
      )
    },
  },

  computed: {
    syncEndpoint() {
      let searchParams = new URLSearchParams({ action: this.action.uriKey })

      if (this.selectedResources === 'all') {
        searchParams.append('resources', 'all')
      } else {
        this.selectedResources.forEach(resourceId => {
          searchParams.append('resources[]', resourceId)
        })
      }

      return (
        (this.endpoint || `/nova-api/${this.resourceName}/action`) +
        '?' +
        searchParams.toString()
      )
    },
  },
}
</script>
