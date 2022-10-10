<template>
  <LoadingView :loading="loading">
    <template v-if="resourceInformation && title">
      <Head
        :title="
          __('Update :resource: :title', {
            resource: resourceInformation.singularLabel,
            title: title,
          })
        "
      />
    </template>

    <form
      v-if="panels"
      @submit="submitViaUpdateResource"
      @change="onUpdateFormStatus"
      :data-form-unique-id="formUniqueId"
      autocomplete="off"
      ref="form"
    >
      <div class="mb-8 space-y-4">
        <component
          v-for="panel in panels"
          :key="panel.id"
          :is="'form-' + panel.component"
          @update-last-retrieved-at-timestamp="updateLastRetrievedAtTimestamp"
          @field-changed="onUpdateFormStatus"
          @file-upload-started="handleFileUploadStarted"
          @file-upload-finished="handleFileUploadFinished"
          :panel="panel"
          :name="panel.name"
          :resource-id="resourceId"
          :resource-name="resourceName"
          :fields="panel.fields"
          :form-unique-id="formUniqueId"
          mode="form"
          :validation-errors="validationErrors"
          :via-resource="viaResource"
          :via-resource-id="viaResourceId"
          :via-relationship="viaRelationship"
          :show-help-text="true"
        />
      </div>

      <!-- Update Button -->
      <div
        class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 space-x-3"
      >
        <CancelButton
          dusk="cancel-update-button"
          type="button"
          align="center"
          @click="cancelUpdatingResource"
        />

        <LoadingButton
          dusk="update-and-continue-editing-button"
          @click="submitViaUpdateResourceAndContinueEditing"
          :disabled="isWorking"
          align="center"
          :processing="wasSubmittedViaUpdateResourceAndContinueEditing"
        >
          {{ __('Update & Continue Editing') }}
        </LoadingButton>

        <LoadingButton
          dusk="update-button"
          type="submit"
          :disabled="isWorking"
          align="center"
          :processing="wasSubmittedViaUpdateResource"
        >
          {{ updateButtonLabel }}
        </LoadingButton>
      </div>
    </form>
  </LoadingView>
</template>

<script>
import each from 'lodash/each'
import tap from 'lodash/tap'
import {
  Errors,
  HandlesFormRequest,
  HandlesUploads,
  InteractsWithResourceInformation,
  mapProps,
  PreventsFormAbandonment,
} from '@/mixins'
import { mapActions } from 'vuex'

export default {
  mixins: [
    HandlesFormRequest,
    HandlesUploads,
    InteractsWithResourceInformation,
    PreventsFormAbandonment,
  ],

  props: mapProps([
    'resourceName',
    'resourceId',
    'viaResource',
    'viaResourceId',
    'viaRelationship',
  ]),

  data: () => ({
    relationResponse: null,
    loading: true,
    submittedViaUpdateResourceAndContinueEditing: false,
    submittedViaUpdateResource: false,
    title: null,
    fields: [],
    panels: [],
    lastRetrievedAt: null,
  }),

  async created() {
    if (Nova.missingResource(this.resourceName)) return Nova.visit('/404')

    // If this update is via a relation index, then let's grab the field
    // and use the label for that as the one we use for the title and buttons
    if (this.isRelation) {
      const { data } = await Nova.request(
        `/nova-api/${this.viaResource}/field/${this.viaRelationship}`
      )
      this.relationResponse = data
    }

    this.getFields()
    this.updateLastRetrievedAtTimestamp()
    this.allowLeavingForm()
  },

  methods: {
    ...mapActions(['fetchPolicies']),

    /**
     * Handle resource loaded event.
     */
    handleResourceLoaded() {
      this.loading = false

      Nova.$emit('resource-loaded', {
        resourceName: this.resourceName,
        resourceId: this.resourceId.toString(),
        mode: 'update',
      })
    },

    /**
     * Get the available fields for the resource.
     */
    async getFields() {
      this.loading = true

      this.panels = []
      this.fields = []

      const {
        data: { title, panels, fields },
      } = await Nova.request()
        .get(
          `/nova-api/${this.resourceName}/${this.resourceId}/update-fields`,
          {
            params: {
              editing: true,
              editMode: 'update',
              viaResource: this.viaResource,
              viaResourceId: this.viaResourceId,
              viaRelationship: this.viaRelationship,
            },
          }
        )
        .catch(error => {
          if (error.response.status == 404) {
            Nova.visit('/404')
            return
          }
        })

      this.title = title
      this.panels = panels
      this.fields = fields

      this.handleResourceLoaded()
    },

    async submitViaUpdateResource(e) {
      e.preventDefault()
      this.submittedViaUpdateResource = true
      this.submittedViaUpdateResourceAndContinueEditing = false
      this.allowLeavingForm()
      await this.updateResource()
    },

    async submitViaUpdateResourceAndContinueEditing(e) {
      e.preventDefault()
      this.submittedViaUpdateResourceAndContinueEditing = true
      this.submittedViaUpdateResource = false
      this.allowLeavingForm()
      await this.updateResource()
    },

    cancelUpdatingResource() {
      this.handleProceedingToPreviousPage()
      this.allowLeavingForm()

      if (window.history.length > 1) {
        window.history.back()
      } else {
        Nova.visit('/')
      }
    },

    /**
     * Update the resource using the provided data.
     */
    async updateResource() {
      this.isWorking = true

      if (this.$refs.form.reportValidity()) {
        try {
          const {
            data: { redirect, id },
          } = await this.updateRequest()

          await this.fetchPolicies()

          Nova.success(
            this.__('The :resource was updated!', {
              resource: this.resourceInformation.singularLabel.toLowerCase(),
            })
          )

          Nova.$emit('resource-updated', {
            resourceName: this.resourceName,
            resourceId: id,
          })

          await this.updateLastRetrievedAtTimestamp()

          if (this.submittedViaUpdateResource) {
            Nova.visit(redirect)
          } else {
            if (id != this.resourceId) {
              Nova.visit(`/resources/${this.resourceName}/${id}/edit`)
            } else {
              // Reset the form by refetching the fields
              this.getFields()

              this.validationErrors = new Errors()
              this.submittedViaUpdateResource = false
              this.submittedViaUpdateResourceAndContinueEditing = false
              this.isWorking = false
            }

            return
          }
        } catch (error) {
          window.scrollTo(0, 0)

          this.submittedViaUpdateResource = false
          this.submittedViaUpdateResourceAndContinueEditing = false

          this.preventLeavingForm()

          this.handleOnUpdateResponseError(error)
        }
      }

      this.submittedViaUpdateResource = false
      this.submittedViaUpdateResourceAndContinueEditing = false
      this.isWorking = false
    },

    /**
     * Send an update request for this resource
     */
    updateRequest() {
      return Nova.request().post(
        `/nova-api/${this.resourceName}/${this.resourceId}`,
        this.updateResourceFormData(),
        {
          params: {
            viaResource: this.viaResource,
            viaResourceId: this.viaResourceId,
            viaRelationship: this.viaRelationship,
            editing: true,
            editMode: 'update',
          },
        }
      )
    },

    /**
     * Create the form data for creating the resource.
     */
    updateResourceFormData() {
      return tap(new FormData(), formData => {
        each(this.panels, panel => {
          each(panel.fields, field => {
            field.fill(formData)
          })
        })

        formData.append('_method', 'PUT')
        formData.append('_retrieved_at', this.lastRetrievedAt)
      })
    },

    /**
     * Update the last retrieved at timestamp to the current UNIX timestamp.
     */
    updateLastRetrievedAtTimestamp() {
      this.lastRetrievedAt = Math.floor(new Date().getTime() / 1000)
    },

    /**
     * Prevent accidental abandonment only if form was changed.
     */
    onUpdateFormStatus() {
      this.updateFormStatus()
    },
  },

  computed: {
    wasSubmittedViaUpdateResourceAndContinueEditing() {
      return this.isWorking && this.submittedViaUpdateResourceAndContinueEditing
    },

    wasSubmittedViaUpdateResource() {
      return this.isWorking && this.submittedViaUpdateResource
    },

    singularName() {
      if (this.relationResponse) {
        return this.relationResponse.singularLabel
      }

      return this.resourceInformation.singularLabel
    },

    updateButtonLabel() {
      return this.resourceInformation.updateButtonLabel
    },

    isRelation() {
      return Boolean(this.viaResourceId && this.viaRelationship)
    },
  },
}
</script>
