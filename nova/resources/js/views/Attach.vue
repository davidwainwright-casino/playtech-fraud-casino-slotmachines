<template>
  <LoadingView :loading="initialLoading">
    <template v-if="relatedResourceLabel">
      <Head
        :title="
          __('Attach :resource', {
            resource: relatedResourceLabel,
          })
        "
      />
    </template>

    <Heading
      class="mb-3"
      v-text="__('Attach :resource', { resource: relatedResourceLabel })"
    />

    <form
      v-if="field"
      @submit.prevent="attachResource"
      @change="onUpdateFormStatus"
      :data-form-unique-id="formUniqueId"
      autocomplete="off"
    >
      <Card class="overflow-hidden mb-8">
        <!-- Related Resource -->
        <div
          v-if="parentResource"
          dusk="via-resource-field"
          class="field-wrapper flex flex-col md:flex-row border-b border-gray-100 dark:border-gray-700"
        >
          <div class="w-1/5 px-8 py-6">
            <label
              :for="parentResource.name"
              class="inline-block text-gray-500 pt-2 leading-tight"
            >
              {{ parentResource.name }}
            </label>
          </div>
          <div class="py-6 px-8 w-1/2">
            <span class="inline-block font-bold text-gray-500 pt-2">
              {{ parentResource.display }}
            </span>
          </div>
        </div>
        <DefaultField
          :field="field"
          :errors="validationErrors"
          :show-help-text="true"
        >
          <template #field>
            <div class="flex items-center">
              <SearchInput
                v-if="field.searchable"
                :data-testid="`${field.resourceName}-search-input`"
                @input="performSearch"
                @clear="clearSelection"
                @selected="selectResource"
                :debounce="field.debounce"
                :value="selectedResource"
                :data="availableResources"
                trackBy="value"
                class="w-full"
              >
                <div v-if="selectedResource" class="flex items-center">
                  <div v-if="selectedResource.avatar" class="mr-3">
                    <img
                      :src="selectedResource.avatar"
                      class="w-8 h-8 rounded-full block"
                    />
                  </div>

                  {{ selectedResource.display }}
                </div>

                <template #option="{ selected, option }">
                  <div class="flex items-center">
                    <div v-if="option.avatar" class="flex-none mr-3">
                      <img
                        :src="option.avatar"
                        class="w-8 h-8 rounded-full block"
                      />
                    </div>

                    <div class="flex-auto">
                      <div
                        class="text-sm font-semibold leading-5 text-90"
                        :class="{ 'text-white': selected }"
                      >
                        {{ option.display }}
                      </div>

                      <div
                        v-if="field.withSubtitles"
                        class="mt-1 text-xs font-semibold leading-5 text-gray-500"
                        :class="{ 'text-white': selected }"
                      >
                        <span v-if="option.subtitle">{{
                          option.subtitle
                        }}</span>
                        <span v-else>{{
                          __('No additional information...')
                        }}</span>
                      </div>
                    </div>
                  </div>
                </template>
              </SearchInput>

              <SelectControl
                v-else
                dusk="attachable-select"
                class="w-full"
                :class="{
                  'form-input-border-error': validationErrors.has(
                    field.attribute
                  ),
                }"
                :data-testid="`${field.resourceName}-select`"
                v-model:selected="selectedResourceId"
                @change="selectResourceFromSelectControl"
                :options="availableResources"
                :label="'display'"
              >
                <option value="" disabled selected>
                  {{
                    __('Choose :resource', {
                      resource: relatedResourceLabel,
                    })
                  }}
                </option>
              </SelectControl>

              <CreateRelationButton
                v-if="canShowNewRelationModal"
                @click="openRelationModal"
                class="ml-2"
                :dusk="`${field.attribute}-inline-create`"
              />
            </div>

            <CreateRelationModal
              :show="canShowNewRelationModal && relationModalOpen"
              @set-resource="handleSetResource"
              @create-cancelled="closeRelationModal"
              :resource-name="field.resourceName"
              :resource-id="resourceId"
              :via-relationship="viaRelationship"
              :via-resource="viaResource"
              :via-resource-id="viaResourceId"
            />

            <TrashedCheckbox
              v-if="softDeletes"
              class="mt-3"
              :resource-name="field.resourceName"
              :checked="withTrashed"
              @input="toggleWithTrashed"
            />
          </template>
        </DefaultField>

        <LoadingView :loading="loading">
          <!-- Pivot Fields -->
          <div v-for="field in fields" :key="field.uniqueKey">
            <component
              :is="`form-${field.component}`"
              :resource-name="resourceName"
              :resource-id="resourceId"
              :related-resource-name="relatedResourceName"
              :field="field"
              :form-unique-id="formUniqueId"
              :errors="validationErrors"
              :via-resource="viaResource"
              :via-resource-id="viaResourceId"
              :via-relationship="viaRelationship"
              :show-help-text="true"
            />
          </div>
        </LoadingView>
      </Card>

      <!-- Attach Button -->
      <div
        class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 space-x-3"
      >
        <CancelButton
          dusk="cancel-attach-button"
          type="button"
          @click="cancelAttachingResource"
        />

        <LoadingButton
          dusk="attach-and-attach-another-button"
          @click.native.prevent="attachAndAttachAnother"
          :disabled="isWorking"
          :processing="submittedViaAttachAndAttachAnother"
        >
          {{ __('Attach & Attach Another') }}
        </LoadingButton>

        <LoadingButton
          dusk="attach-button"
          type="submit"
          :disabled="isWorking"
          :processing="submittedViaAttachResource"
        >
          {{
            __('Attach :resource', {
              resource: relatedResourceLabel,
            })
          }}
        </LoadingButton>
      </div>
    </form>
  </LoadingView>
</template>

<script>
import each from 'lodash/each'
import find from 'lodash/find'
import isNil from 'lodash/isNil'
import tap from 'lodash/tap'
import {
  PerformsSearches,
  TogglesTrashed,
  Errors,
  FormEvents,
  HandlesFormRequest,
  PreventsFormAbandonment,
} from '@/mixins'
import { mapActions } from 'vuex'

export default {
  mixins: [
    FormEvents,
    HandlesFormRequest,
    PerformsSearches,
    TogglesTrashed,
    PreventsFormAbandonment,
  ],

  props: {
    resourceName: {
      type: String,
      required: true,
    },
    resourceId: {
      required: true,
    },
    relatedResourceName: {
      type: String,
      required: true,
    },
    viaResource: {
      default: '',
    },
    viaResourceId: {
      default: '',
    },
    parentResource: {
      type: Object,
    },
    viaRelationship: {
      default: '',
    },
    polymorphic: {
      default: false,
    },
  },

  data: () => ({
    initialLoading: true,
    loading: true,
    submittedViaAttachAndAttachAnother: false,
    submittedViaAttachResource: false,

    field: null,
    softDeletes: false,
    fields: [],
    selectedResource: null,
    selectedResourceId: null,
    relationModalOpen: false,
  }),

  created() {
    if (Nova.missingResource(this.resourceName)) return Nova.visit('/404')
  },

  /**
   * Mount the component.
   */
  mounted() {
    this.initializeComponent()
  },

  methods: {
    ...mapActions(['fetchPolicies']),

    /**
     * Initialize the component's data.
     */
    initializeComponent() {
      this.softDeletes = false
      this.disableWithTrashed()
      this.clearSelection()
      this.getField()
      this.getPivotFields()
      this.resetErrors()
      this.allowLeavingForm()
    },

    /**
     * Handle pivot fields loaded event.
     */
    handlePivotFieldsLoaded() {
      this.loading = false

      each(this.fields, field => {
        field.fill = () => ''
      })
    },

    /**
     * Get the many-to-many relationship field.
     */
    getField() {
      this.field = null

      Nova.request()
        .get(
          '/nova-api/' + this.resourceName + '/field/' + this.viaRelationship,
          {
            params: {
              relatable: true,
            },
          }
        )
        .then(({ data }) => {
          this.field = data
          this.field.searchable
            ? this.determineIfSoftDeletes()
            : this.getAvailableResources()
          this.initialLoading = false
        })
    },

    /**
     * Get all of the available pivot fields for the relationship.
     */
    getPivotFields() {
      this.fields = []
      this.loading = true

      Nova.request()
        .get(
          '/nova-api/' +
            this.resourceName +
            '/' +
            this.resourceId +
            '/creation-pivot-fields/' +
            this.relatedResourceName,
          {
            params: {
              editing: true,
              editMode: 'attach',
              viaRelationship: this.viaRelationship,
            },
          }
        )
        .then(({ data }) => {
          this.fields = data

          this.handlePivotFieldsLoaded()
        })
    },

    resetErrors() {
      this.validationErrors = new Errors()
    },

    /**
     * Get all of the available resources for the current search / trashed state.
     */
    getAvailableResources(search = '') {
      return Nova.request()
        .get(
          `/nova-api/${this.resourceName}/${this.resourceId}/attachable/${this.relatedResourceName}`,
          {
            params: {
              search,
              current: this.selectedResourceId,
              withTrashed: this.withTrashed,
              viaRelationship: this.viaRelationship,
            },
          }
        )
        .then(response => {
          this.availableResources = response.data.resources
          this.withTrashed = response.data.withTrashed
          this.softDeletes = response.data.softDeletes
        })
    },

    /**
     * Determine if the related resource is soft deleting.
     */
    determineIfSoftDeletes() {
      Nova.request()
        .get('/nova-api/' + this.relatedResourceName + '/soft-deletes')
        .then(response => {
          this.softDeletes = response.data.softDeletes
        })
    },

    /**
     * Attach the selected resource.
     */
    async attachResource() {
      this.submittedViaAttachResource = true

      try {
        await this.attachRequest()

        this.submittedViaAttachResource = false
        this.allowLeavingForm()

        await this.fetchPolicies(),
          Nova.success(this.__('The resource was attached!'))

        Nova.visit(`/resources/${this.resourceName}/${this.resourceId}`)
      } catch (error) {
        window.scrollTo(0, 0)

        this.submittedViaAttachResource = false

        this.preventLeavingForm()

        this.handleOnCreateResponseError(error)
      }
    },

    /**
     * Attach a new resource and reset the form
     */
    async attachAndAttachAnother() {
      this.submittedViaAttachAndAttachAnother = true

      try {
        await this.attachRequest()

        this.allowLeavingForm()

        this.submittedViaAttachAndAttachAnother = false

        await this.fetchPolicies()

        // Reset the form by refetching the fields
        this.initializeComponent()
      } catch (error) {
        this.submittedViaAttachAndAttachAnother = false

        this.handleOnCreateResponseError(error)
      }
    },

    cancelAttachingResource() {
      this.handleProceedingToPreviousPage()
      this.allowLeavingForm()

      if (window.history.length > 1) {
        window.history.back()
      } else {
        Nova.visit('/')
      }
    },

    /**
     * Send an attach request for this resource
     */
    attachRequest() {
      return Nova.request().post(
        this.attachmentEndpoint,
        this.attachmentFormData(),
        {
          params: {
            editing: true,
            editMode: 'attach',
          },
        }
      )
    },

    /**
     * Get the form data for the resource attachment.
     */
    attachmentFormData() {
      return tap(new FormData(), formData => {
        each(this.fields, field => {
          field.fill(formData)
        })

        if (!this.selectedResource) {
          formData.append(this.relatedResourceName, '')
        } else {
          formData.append(this.relatedResourceName, this.selectedResource.value)
        }

        formData.append(this.relatedResourceName + '_trashed', this.withTrashed)
        formData.append('viaRelationship', this.viaRelationship)
      })
    },

    /**
     * Select a resource using the <select> control
     */
    selectResourceFromSelectControl(value) {
      this.selectedResourceId = value
      this.selectInitialResource()

      if (this.field) {
        this.emitFieldValueChange(this.field.attribute, this.selectedResourceId)
      }
    },

    /**
     * Select the initial selected resource
     */
    selectInitialResource() {
      this.selectedResource = find(
        this.availableResources,
        r => r.value == this.selectedResourceId
      )
    },

    /**
     * Toggle the trashed state of the search
     */
    toggleWithTrashed() {
      this.withTrashed = !this.withTrashed

      // Reload the data if the component doesn't support searching
      if (!this.isSearchable) {
        this.getAvailableResources()
      }
    },

    /**
     * Prevent accidental abandonment only if form was changed.
     */
    onUpdateFormStatus() {
      this.updateFormStatus()
    },

    handleSetResource({ id }) {
      this.closeRelationModal()
      this.selectedResourceId = id
      this.initializingWithExistingResource = true
      this.getAvailableResources().then(() => this.selectInitialResource())
    },

    openRelationModal() {
      Nova.$emit('create-relation-modal-opened')
      this.relationModalOpen = true
    },

    closeRelationModal() {
      this.relationModalOpen = false
      Nova.$emit('create-relation-modal-closed')
    },
  },

  computed: {
    /**
     * Get the attachment endpoint for the relationship type.
     */
    attachmentEndpoint() {
      return this.polymorphic
        ? '/nova-api/' +
            this.resourceName +
            '/' +
            this.resourceId +
            '/attach-morphed/' +
            this.relatedResourceName
        : '/nova-api/' +
            this.resourceName +
            '/' +
            this.resourceId +
            '/attach/' +
            this.relatedResourceName
    },

    /**
     * Get the label for the related resource.
     */
    relatedResourceLabel() {
      if (this.field) {
        return this.field.singularLabel
      }
    },

    /**
     * Determine if the related resources is searchable
     */
    isSearchable() {
      return this.field.searchable
    },

    /**
     * Determine if the form is being processed
     */
    isWorking() {
      return (
        this.submittedViaAttachResource ||
        this.submittedViaAttachAndAttachAnother
      )
    },

    /**
     * Return the heading for the view
     */
    headingTitle() {
      return this.__('Attach :resource', {
        resource: this.relatedResourceLabel,
      })
    },

    shouldShowTrashed() {
      return Boolean(this.softDeletes)
    },

    authorizedToCreate() {
      return find(Nova.config('resources'), resource => {
        return resource.uriKey == this.field.resourceName
      }).authorizedToCreate
    },

    canShowNewRelationModal() {
      return this.field.showCreateRelationButton && this.authorizedToCreate
    },
  },
}
</script>
