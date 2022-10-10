<template>
  <Modal
    data-testid="preview-resource-modal"
    :show="show"
    @close-via-escape="$emit('close')"
    role="alertdialog"
    maxWidth="2xl"
  >
    <LoadingCard
      :loading="loading"
      class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
    >
      <slot>
        <ModalHeader class="flex items-center">
          {{ modalTitle }}
          <span
            v-if="resource && resource.softDeleted"
            class="ml-auto bg-red-50 text-red-500 py-0.5 px-2 rounded-full text-xs"
            >Soft-deleted</span
          >
        </ModalHeader>
        <ModalContent class="px-8">
          <template v-if="resource">
            <component
              :key="index"
              v-for="(field, index) in resource.fields"
              :index="index"
              :is="`detail-${field.component}`"
              :resource-name="resourceName"
              :resource-id="resourceId"
              :resource="resource"
              :field="field"
            />

            <div v-if="resource.fields.length == 0">
              {{ __('There are no fields to display.') }}
            </div>
          </template>
        </ModalContent>
      </slot>

      <ModalFooter>
        <div class="ml-auto">
          <LoadingButton
            @click.prevent="$emit('close')"
            ref="confirmButton"
            dusk="confirm-preview-button"
          >
            {{ __('Close') }}
          </LoadingButton>
        </div>
      </ModalFooter>
    </LoadingCard>
  </Modal>
</template>

<script>
import { mapProps } from '@/mixins'
import { minimum } from '@/util'

export default {
  emits: ['confirm', 'close'],

  props: {
    show: { type: Boolean, default: false },

    ...mapProps(['resourceName', 'resourceId']),
  },

  data: () => ({
    loading: true,
    title: null,
    resource: null,
  }),

  async created() {
    await this.getResource()
  },

  mounted() {
    Nova.$emit('close-dropdowns')
  },

  methods: {
    getResource() {
      this.resource = null

      return minimum(
        Nova.request().get(
          `/nova-api/${this.resourceName}/${this.resourceId}/preview`
        )
      )
        .then(({ data: { title, resource } }) => {
          this.title = title
          this.resource = resource
          this.loading = false
        })
        .catch(error => {
          if (error.response.status >= 500) {
            Nova.$emit('error', error.response.data.message)
            return
          }

          if (error.response.status === 404) {
            Nova.visit('/404')
            return
          }

          if (error.response.status === 403) {
            Nova.visit('/403')
            return
          }

          if (error.response.status === 401) return Nova.redirectToLogin()

          Nova.error(this.__('This resource no longer exists'))

          Nova.visit(`/resources/${this.resourceName}`)
        })
    },

    handleClose() {
      this.$emit('close')
    },

    handleConfirm() {
      this.$emit('confirm')
    },
  },

  computed: {
    modalTitle() {
      return `${this.__('Previewing')} ${this.title}`
    },
  },
}
</script>
