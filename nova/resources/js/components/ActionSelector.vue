<template>
  <SelectControl
    v-bind="$attrs"
    v-if="actions.length > 0 || availablePivotActions.length > 0"
    size="xs"
    @change="handleSelectionChange"
    :options="actionsForSelect"
    data-testid="action-select"
    dusk="action-select"
    selected=""
    :class="{ 'max-w-[6rem]': width == 'auto', 'w-full': width == 'full' }"
    :aria-label="__('Select Action')"
    ref="selectControl"
  >
    <option value="" disabled selected>{{ __('Actions') }}</option>
  </SelectControl>

  <!-- Confirm Action Modal -->
  <component
    v-if="confirmActionModalOpened"
    class="text-left"
    :show="confirmActionModalOpened"
    :is="selectedAction.component"
    :working="working"
    :selected-resources="selectedResources"
    :resource-name="resourceName"
    :action="selectedAction"
    :errors="errors"
    @confirm="executeAction"
    @close="closeConfirmationModal"
  />

  <component
    :is="actionResponseData.modal"
    @close="closeActionResponseModal"
    v-if="showActionResponseModal"
    :show="showActionResponseModal"
    :data="actionResponseData"
  />
</template>

<script>
import { HandlesActions, InteractsWithResourceInformation } from '@/mixins'

export default {
  inheritAttrs: false,

  mixins: [InteractsWithResourceInformation, HandlesActions],

  props: {
    width: {
      type: String,
      default: 'auto',
    },

    selectedResources: {
      type: [Array, String],
      default: () => [],
    },
    pivotActions: {},
    pivotName: String,

    endpoint: {
      default: null,
    },

    actionQueryString: {
      type: Object,
      default: () => ({
        currentSearch: '',
        encodedFilters: '',
        currentTrashed: '',
        viaResource: '',
        viaResourceId: '',
        viaRelationship: '',
      }),
    },
  },

  data: () => ({
    showActionResponseModal: false,
    actionResponseData: {},
  }),

  watch: {
    /**
     * Watch the actions property for changes.
     */
    availableActions() {
      this.initializeActionFields()
    },

    /**
     * Watch the pivot actions property for changes.
     */
    availablePivotActions() {
      this.initializeActionFields()
    },

    /**
     * Watch the pivot actions property for changes.
     */
    availableStandaloneActions() {
      this.initializeActionFields()
    },
  },

  computed: {
    currentSearch() {
      return this.actionQueryString.currentSearch
    },

    encodedFilters() {
      return this.actionQueryString.encodedFilters
    },

    currentTrashed() {
      return this.actionQueryString.currentTrashed
    },

    viaResource() {
      return this.actionQueryString.viaResource
    },

    viaResourceId() {
      return this.actionQueryString.viaResourceId
    },

    viaRelationship() {
      return this.actionQueryString.viaRelationship
    },

    actionsForSelect() {
      return [
        ...this.availableActions.map(a => ({
          value: a.uriKey,
          label: a.name,
        })),

        ...this.availablePivotActions.map(a => {
          return {
            group: this.pivotName,
            value: a.uriKey,
            label: a.name,
          }
        }),

        ...this.availableStandaloneActions.map(a => ({
          group: this.__('Standalone Actions'),
          value: a.uriKey,
          label: a.name,
        })),
      ]
    },
  },
}
</script>
