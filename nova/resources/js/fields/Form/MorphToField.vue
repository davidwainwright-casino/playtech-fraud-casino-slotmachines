<template>
  <div class="border-b border-gray-100 dark:border-gray-700">
    <DefaultField
      :field="currentField"
      :show-errors="false"
      :field-name="fieldName"
      :show-help-text="showHelpText"
    >
      <template #field>
        <div v-if="hasMorphToTypes" class="flex relative">
          <select
            :disabled="isLocked || currentlyIsReadonly"
            :data-testid="`${field.attribute}-type`"
            :dusk="`${field.attribute}-type`"
            :value="resourceType"
            @change="refreshResourcesForTypeChange"
            class="block w-full form-control form-input form-input-bordered form-select mb-3"
          >
            <option value="" selected :disabled="!currentField.nullable">
              {{ __('Choose Type') }}
            </option>

            <option
              v-for="option in currentField.morphToTypes"
              :key="option.value"
              :value="option.value"
              :selected="resourceType == option.value"
            >
              {{ option.singularLabel }}
            </option>
          </select>

          <IconArrow class="pointer-events-none form-select-arrow" />
        </div>
        <label v-else class="flex items-center select-none mt-2">
          {{ __('There are no available options for this resource.') }}
        </label>
      </template>
    </DefaultField>

    <DefaultField
      :field="currentField"
      :errors="errors"
      :show-help-text="false"
      :field-name="fieldTypeName"
      v-if="hasMorphToTypes"
    >
      <template #field>
        <div class="flex items-center mb-3">
          <SearchInput
            class="w-full"
            v-if="isSearchable && !isLocked && !currentlyIsReadonly"
            :data-testid="`${field.attribute}-search-input`"
            :disabled="!resourceType || isLocked || currentlyIsReadonly"
            @input="performSearch"
            @clear="clearSelection"
            @selected="selectResource"
            :debounce="currentField.debounce"
            :value="selectedResource"
            :data="availableResources"
            :clearable="currentField.nullable"
            trackBy="value"
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
                    v-if="currentField.withSubtitles"
                    class="mt-1 text-xs font-semibold leading-5 text-gray-500"
                    :class="{ 'text-white': selected }"
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
            :class="{ 'form-input-border-error': hasError }"
            :dusk="`${field.attribute}-select`"
            @change="selectResourceFromSelectControl"
            :disabled="!resourceType || isLocked || currentlyIsReadonly"
            :options="availableResources"
            v-model:selected="selectedResourceId"
            label="display"
          >
            <option
              value=""
              :disabled="!currentField.nullable"
              :selected="selectedResourceId == ''"
            >
              {{ __('Choose') }} {{ fieldTypeName }}
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
          v-if="canShowNewRelationModal"
          :show="relationModalOpen"
          @set-resource="handleSetResource"
          @create-cancelled="closeRelationModal"
          :resource-name="resourceType"
          :via-relationship="viaRelationship"
          :via-resource="viaResource"
          :via-resource-id="viaResourceId"
        />

        <TrashedCheckbox
          v-if="shouldShowTrashed"
          class="mt-3"
          :resource-name="field.attribute"
          :checked="withTrashed"
          @input="toggleWithTrashed"
        />
      </template>
    </DefaultField>
  </div>
</template>

<script>
import find from 'lodash/find'
import isNil from 'lodash/isNil'
import storage from '@/storage/MorphToFieldStorage'
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

  data: () => ({
    resourceType: '',
    initializingWithExistingResource: false,
    softDeletes: false,
    selectedResourceId: null,
    selectedResource: null,
    search: '',
    relationModalOpen: false,
    withTrashed: false,
  }),

  /**
   * Mount the component.
   */
  mounted() {
    this.selectedResourceId = this.field.value

    if (this.editingExistingResource) {
      this.initializingWithExistingResource = true
      this.resourceType = this.field.morphToType
      this.selectedResourceId = this.field.morphToId
    } else if (this.creatingViaRelatedResource) {
      this.initializingWithExistingResource = true
      this.resourceType = this.viaResource
      this.selectedResourceId = this.viaResourceId
    }

    if (this.shouldSelectInitialResource) {
      if (!this.resourceType && this.field.defaultResource) {
        this.resourceType = this.field.defaultResource
      }
      this.getAvailableResources().then(() => this.selectInitialResource())
    }

    if (this.resourceType) {
      this.determineIfSoftDeletes()
    }

    this.field.fill = this.fill
  },

  methods: {
    /**
     * Select a resource using the <select> control
     */
    selectResourceFromSelectControl(value) {
      this.selectedResourceId = value
      this.selectInitialResource()

      if (this.field) {
        this.emitFieldValueChange(
          `${this.field.attribute}_type`,
          this.resourceType
        )
        this.emitFieldValueChange(this.field.attribute, this.selectedResourceId)
      }
    },

    /**
     * Fill the forms formData with details from this field
     */
    fill(formData) {
      if (this.selectedResource && this.resourceType) {
        this.fillIfVisible(
          formData,
          this.field.attribute,
          this.selectedResource.value
        )
        this.fillIfVisible(
          formData,
          `${this.field.attribute}_type`,
          this.resourceType
        )
      } else {
        this.fillIfVisible(formData, this.field.attribute, '')
        this.fillIfVisible(formData, `${this.field.attribute}_type`, '')
      }

      this.fillIfVisible(
        formData,
        `${this.field.attribute}_trashed`,
        this.withTrashed
      )
    },

    /**
     * Get the resources that may be related to this resource.
     */
    getAvailableResources(search = '') {
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

          this.initializingWithExistingResource = false
          this.availableResources = resources
          this.softDeletes = softDeletes
        })
    },

    onSyncedField() {
      if (this.resourceType !== this.currentField.morphToType) {
        this.refreshResourcesForTypeChange(this.currentField.morphToType)
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
     * Determine if the selected resource type is soft deleting.
     */
    determineIfSoftDeletes() {
      return storage
        .determineIfSoftDeletes(this.resourceType)
        .then(({ data: { softDeletes } }) => (this.softDeletes = softDeletes))
    },

    /**
     * Handle the changing of the resource type.
     */
    async refreshResourcesForTypeChange(event) {
      this.resourceType = event?.target?.value ?? event
      this.availableResources = []
      this.selectedResource = ''
      this.selectedResourceId = ''
      this.withTrashed = false

      // if (this.resourceType == '') {
      this.softDeletes = false
      // } else if (this.field.searchable) {
      this.determineIfSoftDeletes()
      // }

      if (!this.isSearchable && this.resourceType) {
        this.getAvailableResources().then(() => {
          this.emitFieldValueChange(
            `${this.field.attribute}_type`,
            this.resourceType
          )
          this.emitFieldValueChange(this.field.attribute, null)
        })
      }
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
      this.getAvailableResources().then(() => {
        this.selectInitialResource()

        this.emitFieldValueChange(
          `${this.field.attribute}_type`,
          this.resourceType
        )
        this.emitFieldValueChange(this.field.attribute, this.selectedResourceId)
      })
    },
  },

  computed: {
    /**
     * Determine if an existing resource is being updated.
     */
    editingExistingResource() {
      return Boolean(this.field.morphToId && this.field.morphToType)
    },

    /**
     * Determine if we are creating a new resource via a parent relation
     */
    creatingViaRelatedResource() {
      return Boolean(
        find(
          this.currentField.morphToTypes,
          type => type.value == this.viaResource
        ) &&
          this.viaResource &&
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
          Boolean(this.field.value && this.field.defaultResource)
      )
    },

    /**
     * Determine if the related resources is searchable
     */
    isSearchable() {
      return Boolean(this.currentField.searchable)
    },

    shouldLoadFirstResource() {
      return (
        (this.isSearchable || this.creatingViaRelatedResource) &&
        this.shouldSelectInitialResource &&
        this.initializingWithExistingResource
      )
    },

    /**
     * Get the query params for getting available resources
     */
    queryParams() {
      return {
        params: {
          type: this.resourceType,
          current: this.selectedResourceId,
          first: this.shouldLoadFirstResource,
          search: this.search,
          withTrashed: this.withTrashed,
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

    /**
     * Determine if the field is locked
     */
    isLocked() {
      return Boolean(this.viaResource && this.field.reverse)
    },

    /**
     * Return the morphable type label for the field
     */
    fieldName() {
      return this.field.name
    },

    /**
     * Return the selected morphable type's label
     */
    fieldTypeName() {
      if (this.resourceType) {
        return (
          find(this.currentField.morphToTypes, type => {
            return type.value == this.resourceType
          })?.singularLabel || ''
        )
      }

      return ''
    },

    /**
     * Determine whether there are any morph to types.
     */
    hasMorphToTypes() {
      return this.currentField.morphToTypes.length > 0
    },

    authorizedToCreate() {
      return find(Nova.config('resources'), resource => {
        return resource.uriKey == this.resourceType
      }).authorizedToCreate
    },

    canShowNewRelationModal() {
      return (
        this.currentField.showCreateRelationButton &&
        this.resourceType &&
        !this.shownViaNewRelationModal &&
        !this.isLocked &&
        !this.currentlyIsReadonly &&
        this.authorizedToCreate
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
  },
}
</script>
