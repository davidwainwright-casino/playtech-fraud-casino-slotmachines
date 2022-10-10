<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <div class="flex items-center">
        <SearchInput
          v-if="isSearchable && !isLocked && !currentlyIsReadonly"
          :data-testid="`${field.resourceName}-search-input`"
          @input="performSearch"
          @clear="clearSelection"
          @selected="selectResource"
          :error="hasError"
          :debounce="currentField.debounce"
          :value="selectedResource"
          :data="availableResources"
          :clearable="currentField.nullable"
          trackBy="value"
          class="w-full"
          :mode="mode"
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
                <img :src="option.avatar" class="w-8 h-8 rounded-full block" />
              </div>

              <div class="flex-auto">
                <div
                  class="text-sm font-semibold leading-normal"
                  :class="{ 'text-white dark:text-gray-900': selected }"
                >
                  {{ option.display }}
                </div>

                <div
                  v-if="currentField.withSubtitles"
                  class="text-xs font-semibold leading-normal text-gray-500"
                  :class="{ 'text-white dark:text-gray-700': selected }"
                >
                  <span v-if="option.subtitle">{{ option.subtitle }}</span>
                  <span v-else>{{ __('No additional information...') }}</span>
                </div>
              </div>
            </div>
          </template>
        </SearchInput>

        <SelectControl
          v-if="!isSearchable || isLocked || currentlyIsReadonly"
          class="w-full"
          :select-classes="{ 'form-input-border-error': hasError }"
          :data-testid="`${field.resourceName}-select`"
          :dusk="field.attribute"
          :disabled="isLocked || currentlyIsReadonly"
          :options="availableResources"
          v-model:selected="selectedResourceId"
          @change="selectResourceFromSelectControl"
          label="display"
        >
          <option value="" selected :disabled="!currentField.nullable">
            {{ placeholder }}
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
        v-if="shouldShowTrashed"
        class="mt-3"
        :resource-name="field.resourceName"
        :checked="withTrashed"
        @input="toggleWithTrashed"
      />
    </template>
  </DefaultField>
</template>

<script>
import find from 'lodash/find'
import isNil from 'lodash/isNil'
import storage from '@/storage/BelongsToFieldStorage'
import {
  DependentFormField,
  HandlesValidationErrors,
  PerformsSearches,
  TogglesTrashed,
} from '@/mixins'

export default {
  mixins: [
    DependentFormField,
    HandlesValidationErrors,
    PerformsSearches,
    TogglesTrashed,
  ],

  props: {
    resourceId: {},
  },

  data: () => ({
    availableResources: [],
    initializingWithExistingResource: false,
    selectedResource: null,
    selectedResourceId: null,
    softDeletes: false,
    withTrashed: false,
    search: '',
    relationModalOpen: false,
  }),

  /**
   * Mount the component.
   */
  mounted() {
    this.initializeComponent()
  },

  methods: {
    initializeComponent() {
      this.withTrashed = false

      this.selectedResourceId = this.currentField.value

      if (this.editingExistingResource) {
        // If a user is editing an existing resource with this relation
        // we'll have a belongsToId on the field, and we should prefill
        // that resource in this field
        this.initializingWithExistingResource = true
        this.selectedResourceId = this.currentField.belongsToId
      } else if (this.creatingViaRelatedResource) {
        // If the user is creating this resource via a related resource's index
        // page we'll have a viaResource and viaResourceId in the params and
        // should prefill the resource in this field with that information
        this.initializingWithExistingResource = true
        this.selectedResourceId = this.viaResourceId
      }

      if (this.shouldSelectInitialResource) {
        if (this.isSearchable || this.creatingViaRelatedResource) {
          // If we should select the initial resource and the field is
          // searchable, we won't load all the resources but we will select
          // the initial option.
          this.getAvailableResources().then(() => this.selectInitialResource())
        } else {
          // If we should select the initial resource but the field is not
          // searchable we should load all of the available resources into the
          // field first and select the initial option.
          this.initializingWithExistingResource = false

          this.getAvailableResources().then(() => this.selectInitialResource())
        }
      } else if (!this.isSearchable) {
        // If we don't need to select an initial resource because the user
        // came to create a resource directly and there's no parent resource,
        // and the field is searchable we'll just load all of the resources.
        this.getAvailableResources()
      }

      this.determineIfSoftDeletes()

      this.field.fill = this.fill
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
     * Fill the forms formData with details from this field
     */
    fill(formData) {
      this.fillIfVisible(
        formData,
        this.field.attribute,
        this.selectedResource ? this.selectedResource.value : ''
      )
      this.fillIfVisible(
        formData,
        `${this.field.attribute}_trashed`,
        this.withTrashed
      )
    },

    /**
     * Get the resources that may be related to this resource.
     */
    getAvailableResources() {
      return storage
        .fetchAvailableResources(
          this.resourceName,
          this.field.attribute,
          this.queryParams
        )
        .then(({ data: { resources, softDeletes, withTrashed } }) => {
          if (this.initializingWithExistingResource || !this.isSearchable) {
            this.withTrashed = withTrashed
          }

          if (this.creatingViaRelatedResource) {
            let selectedResource = find(
              resources,
              r => r.value == this.selectedResourceId
            )
            if (isNil(selectedResource)) {
              return Nova.visit('/404')
            }
          }

          // Turn off initializing the existing resource after the first time
          this.initializingWithExistingResource = false
          this.availableResources = resources
          this.softDeletes = softDeletes
        })
    },

    /**
     * Determine if the relatd resource is soft deleting.
     */
    determineIfSoftDeletes() {
      return storage
        .determineIfSoftDeletes(this.field.resourceName)
        .then(response => {
          this.softDeletes = response.data.softDeletes
        })
    },

    /**
     * Determine if the given value is numeric.
     */
    isNumeric(value) {
      return !isNaN(parseFloat(value)) && isFinite(value)
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

    openRelationModal() {
      Nova.$emit('create-relation-modal-opened')
      this.relationModalOpen = true
    },

    closeRelationModal() {
      this.relationModalOpen = false
      Nova.$emit('create-relation-modal-closed')
    },

    handleSetResource({ id }) {
      this.closeRelationModal()
      this.selectedResourceId = id
      this.initializingWithExistingResource = true
      this.getAvailableResources().then(() => {
        this.selectInitialResource()

        this.emitFieldValueChange(this.field.attribute, this.selectedResourceId)
      })
    },

    onSyncedField() {
      if (this.editingExistingResource || this.creatingViaRelatedResource) {
        return
      }

      let emitChangesEvent = this.selectedResourceId != this.currentField.value

      this.initializeComponent()

      if (emitChangesEvent) {
        this.emitFieldValueChange(this.field.attribute, this.selectedResourceId)
      }
    },
  },

  computed: {
    /**
     * Determine if we are editing and existing resource
     */
    editingExistingResource() {
      return Boolean(this.field.belongsToId)
    },

    /**
     * Determine if we are creating a new resource via a parent relation
     */
    creatingViaRelatedResource() {
      return Boolean(
        this.viaResource == this.field.resourceName &&
          this.field.reverse &&
          this.viaResourceId
      )
    },

    /**
     * Determine if we should select an initial resource when mounting this field
     */
    shouldSelectInitialResource() {
      return Boolean(
        this.editingExistingResource ||
          this.creatingViaRelatedResource ||
          this.currentField.value
      )
    },

    /**
     * Determine if the related resources is searchable
     */
    isSearchable() {
      return Boolean(this.currentField.searchable)
    },

    /**
     * Get the query params for getting available resources
     */
    queryParams() {
      return {
        params: {
          current: this.selectedResourceId,
          first: this.shouldLoadFirstResource,
          search: this.search,
          withTrashed: this.withTrashed,
          resourceId: this.resourceId,
          viaResource: this.viaResource,
          viaResourceId: this.viaResourceId,
          viaRelationship: this.viaRelationship,
          editing: true,
          editMode:
            isNil(this.resourceId) || this.resourceId === ''
              ? 'create'
              : 'update',
        },
      }
    },

    isLocked() {
      return Boolean(
        this.viaResource == this.field.resourceName && this.field.reverse
      )
    },

    shouldLoadFirstResource() {
      return (
        this.initializingWithExistingResource ||
        Boolean(this.currentlyIsReadonly && this.selectedResourceId)
      )
    },

    shouldShowTrashed() {
      return (
        this.softDeletes &&
        !this.isLocked &&
        !this.currentlyIsReadonly &&
        this.currentField.displaysWithTrashed
      )
    },

    authorizedToCreate() {
      return find(Nova.config('resources'), resource => {
        return resource.uriKey == this.field.resourceName
      }).authorizedToCreate
    },

    canShowNewRelationModal() {
      return (
        this.currentField.showCreateRelationButton &&
        !this.shownViaNewRelationModal &&
        !this.isLocked &&
        !this.currentlyIsReadonly &&
        this.authorizedToCreate
      )
    },

    /**
     * Return the placeholder text for the field.
     */
    placeholder() {
      return this.currentField.placeholder || this.__('—')
    },
  },
}
</script>
