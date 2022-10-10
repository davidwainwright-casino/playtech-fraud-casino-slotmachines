<template>
  <LoadingView :loading="loading">
    <template v-if="shouldOverrideMeta && resourceInformation">
      <Head
        :title="
          __('Create :resource', {
            resource: resourceInformation.singularLabel,
          })
        "
      />
    </template>

    <form
      v-if="panels"
      @submit="submitViaCreateResource"
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
          @field-changed="onUpdateFormStatus"
          @file-upload-started="handleFileUploadStarted"
          @file-upload-finished="handleFileUploadFinished"
          :shown-via-new-relation-modal="shownViaNewRelationModal"
          :panel="panel"
          :name="panel.name"
          :resource-name="resourceName"
          :fields="panel.fields"
          :form-unique-id="formUniqueId"
          :mode="mode"
          :validation-errors="validationErrors"
          :via-resource="viaResource"
          :via-resource-id="viaResourceId"
          :via-relationship="viaRelationship"
          :show-help-text="true"
        />
      </div>

      <!-- Create Button -->
      <div
        class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 space-x-3"
      >
        <CancelButton
          tabindex="0"
          dusk="cancel-create-button"
          type="button"
          @click="$emit('create-cancelled')"
        />

        <LoadingButton
          v-if="shouldShowAddAnotherButton"
          dusk="create-and-add-another-button"
          type="button"
          @click="submitViaCreateResourceAndAddAnother"
          :disabled="isWorking"
          :loading="wasSubmittedViaCreateResourceAndAddAnother"
        >
          {{ __('Create & Add Another') }}
        </LoadingButton>

        <LoadingButton
          dusk="create-button"
          type="submit"
          :disabled="isWorking"
          :loading="wasSubmittedViaCreateResource"
        >
          {{ createButtonLabel }}
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
} from '@/mixins'
import { mapActions, mapMutations } from 'vuex'

export default {
  emits: ['resource-created', 'create-cancelled', 'update-form-status'],

  mixins: [
    HandlesFormRequest,
    HandlesUploads,
    InteractsWithResourceInformation,
  ],

  props: {
    mode: {
      type: String,
      default: 'form',
      validator: val => ['modal', 'form'].includes(val),
    },

    fromResourceId: {
      default: null,
    },

    ...mapProps([
      'resourceName',
      'viaResource',
      'viaResourceId',
      'viaRelationship',
      'shouldOverrideMeta',
    ]),
  },

  data: () => ({
    relationResponse: null,
    loading: true,
    submittedViaCreateResourceAndAddAnother: false,
    submittedViaCreateResource: false,
    fields: [],
    panels: [],
  }),

  async created() {
    if (Nova.missingResource(this.resourceName)) return Nova.visit('/404')

    // If this create is via a relation index, then let's grab the field
    // and use the label for that as the one we use for the title and buttons
    if (this.isRelation) {
      const { data } = await Nova.request().get(
        '/nova-api/' + this.viaResource + '/field/' + this.viaRelationship,
        {
          params: {
            resourceName: this.resourceName,
            viaResource: this.viaResource,
            viaResourceId: this.viaResourceId,
            viaRelationship: this.viaRelationship,
          },
        }
      )
      this.relationResponse = data

      if (this.isHasOneRelationship && this.alreadyFilled) {
        Nova.error(this.__('The HasOne relationship has already been filled.'))

        Nova.visit(`/resources/${this.viaResource}/${this.viaResourceId}`)
      }

      if (this.isHasOneThroughRelationship && this.alreadyFilled) {
        Nova.error(
          this.__('The HasOneThrough relationship has already been filled.')
        )

        Nova.visit(`/resources/${this.viaResource}/${this.viaResourceId}`)
      }
    }

    this.getFields()

    this.mode == 'form' ? this.allowLeavingForm() : this.allowLeavingModal()
  },

  methods: {
    ...mapMutations([
      'allowLeavingForm',
      'preventLeavingForm',
      'allowLeavingModal',
      'preventLeavingModal',
    ]),
    ...mapActions(['fetchPolicies']),

    /**
     * Handle resource loaded event.
     */
    handleResourceLoaded() {
      this.loading = false

      Nova.$emit('resource-loaded', {
        resourceName: this.resourceName,
        resourceId: null,
        mode: 'create',
      })
    },

    /**
     * Get the available fields for the resource.
     */
    async getFields() {
      this.panels = []
      this.fields = []

      const {
        data: { panels, fields },
      } = await Nova.request().get(
        `/nova-api/${this.resourceName}/creation-fields`,
        {
          params: {
            editing: true,
            editMode: 'create',
            inline: this.shownViaNewRelationModal,
            fromResourceId: this.fromResourceId,
            viaResource: this.viaResource,
            viaResourceId: this.viaResourceId,
            viaRelationship: this.viaRelationship,
          },
        }
      )

      this.panels = panels
      this.fields = fields

      this.handleResourceLoaded()
    },

    async submitViaCreateResource(e) {
      e.preventDefault()
      this.submittedViaCreateResource = true
      this.submittedViaCreateResourceAndAddAnother = false
      await this.createResource()
    },

    async submitViaCreateResourceAndAddAnother() {
      this.submittedViaCreateResourceAndAddAnother = true
      this.submittedViaCreateResource = false
      await this.createResource()
    },

    /**
     * Create a new resource instance using the provided data.
     */
    async createResource() {
      this.isWorking = true

      if (this.$refs.form.reportValidity()) {
        try {
          const {
            data: { redirect, id },
          } = await this.createRequest()

          this.mode === 'form'
            ? this.allowLeavingForm()
            : this.allowLeavingModal()

          // Reload the policies for Nova in case the user has new permissions
          await this.fetchPolicies()

          Nova.success(
            this.__('The :resource was created!', {
              resource: this.resourceInformation.singularLabel.toLowerCase(),
            })
          )

          if (this.submittedViaCreateResource) {
            this.$emit('resource-created', { id, redirect })
          } else {
            // Reset the form by refetching the fields
            this.getFields()
            this.validationErrors = new Errors()
            this.submittedViaCreateAndAddAnother = false
            this.submittedViaCreateResource = false
            this.isWorking = false

            return
          }
        } catch (error) {
          window.scrollTo(0, 0)

          this.submittedViaCreateAndAddAnother = false
          this.submittedViaCreateResource = true
          this.isWorking = false

          this.mode === 'form'
            ? this.preventLeavingForm()
            : this.preventLeavingModal()

          this.handleOnCreateResponseError(error)
        }
      }

      this.submittedViaCreateAndAddAnother = false
      this.submittedViaCreateResource = true
      this.isWorking = false
    },

    /**
     * Send a create request for this resource
     */
    createRequest() {
      return Nova.request().post(
        `/nova-api/${this.resourceName}`,
        this.createResourceFormData(),
        {
          params: {
            editing: true,
            editMode: 'create',
          },
        }
      )
    },

    /**
     * Create the form data for creating the resource.
     */
    createResourceFormData() {
      return tap(new FormData(), formData => {
        each(this.panels, panel => {
          each(panel.fields, field => {
            field.fill(formData)
          })
        })

        formData.append('viaResource', this.viaResource)
        formData.append('viaResourceId', this.viaResourceId)
        formData.append('viaRelationship', this.viaRelationship)
      })
    },

    /**
     * Prevent accidental abandonment only if form was changed.
     */
    onUpdateFormStatus() {
      this.$emit('update-form-status')
    },
  },

  computed: {
    wasSubmittedViaCreateResource() {
      return this.isWorking && this.submittedViaCreateResource
    },

    wasSubmittedViaCreateResourceAndAddAnother() {
      return this.isWorking && this.submittedViaCreateResourceAndAddAnother
    },

    singularName() {
      if (this.relationResponse) {
        return this.relationResponse.singularLabel
      }

      return this.resourceInformation.singularLabel
    },

    createButtonLabel() {
      return this.resourceInformation.createButtonLabel
    },

    isRelation() {
      return Boolean(this.viaResourceId && this.viaRelationship)
    },

    shownViaNewRelationModal() {
      return this.mode == 'modal'
    },

    inFormMode() {
      return this.mode == 'form'
    },

    canAddMoreResources() {
      return this.authorizedToCreate
    },

    alreadyFilled() {
      return this.relationResponse && this.relationResponse.alreadyFilled
    },

    isHasOneRelationship() {
      return this.relationResponse && this.relationResponse.hasOneRelationship
    },

    isHasOneThroughRelationship() {
      return (
        this.relationResponse && this.relationResponse.hasOneThroughRelationship
      )
    },

    shouldShowAddAnotherButton() {
      return (
        Boolean(this.inFormMode && !this.alreadyFilled) &&
        !Boolean(this.isHasOneRelationship || this.isHasOneThroughRelationship)
      )
    },
  },
}
</script>
