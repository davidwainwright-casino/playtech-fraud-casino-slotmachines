<template>
  <LoadingView
    :loading="initialLoading"
    :dusk="resourceName + '-index-component'"
    :data-relationship="viaRelationship"
  >
    <template v-if="shouldOverrideMeta && resourceInformation">
      <Head :title="__(`${resourceInformation.label}`)" />
    </template>

    <Cards
      v-if="shouldShowCards"
      :cards="cards"
      :resource-name="resourceName"
    />

    <Heading
      :level="1"
      class="mb-3 flex items-center"
      :class="{ 'mt-6': shouldShowCards && cards.length > 0 }"
    >
      <span v-html="headingTitle" />
      <button
        v-if="!loading && viaRelationship"
        @click="handleCollapsableChange"
        class="rounded border border-transparent h-6 w-6 ml-1 inline-flex items-center justify-center focus:outline-none focus:ring ring-primary-200"
        :aria-label="__('Toggle Collapsed')"
        :aria-expanded="shouldBeCollapsed === false ? 'true' : 'false'"
      >
        <CollapseButton :collapsed="shouldBeCollapsed" />
      </button>
    </Heading>

    <template v-if="!shouldBeCollapsed">
      <div class="flex">
        <IndexSearchInput
          :class="{ 'mb-6': !viaResource }"
          v-if="
            resourceInformation && resourceInformation.searchable && !viaHasOne
          "
          :searchable="
            resourceInformation && resourceInformation.searchable && !viaHasOne
          "
          v-model:keyword="search"
          @update:keyword="search = $event"
        />

        <div class="w-full flex items-center" :class="{ 'mb-6': !viaResource }">
          <!-- Create / Attach Button -->
          <CreateResourceButton
            :label="createButtonLabel"
            :singular-name="singularName"
            :resource-name="resourceName"
            :via-resource="viaResource"
            :via-resource-id="viaResourceId"
            :via-relationship="viaRelationship"
            :relationship-type="relationshipType"
            :authorized-to-create="authorizedToCreate && !resourceIsFull"
            :authorized-to-relate="authorizedToRelate"
            class="flex-shrink-0 ml-auto"
            :class="{ 'mb-6': viaResource }"
          />
        </div>
      </div>

      <Card>
        <ResourceTableToolbar
          :action-query-string="actionQueryString"
          :all-matching-resource-count="allMatchingResourceCount"
          :authorized-to-delete-any-resources="authorizedToDeleteAnyResources"
          :authorized-to-delete-selected-resources="
            authorizedToDeleteSelectedResources
          "
          :authorized-to-force-delete-any-resources="
            authorizedToForceDeleteAnyResources
          "
          :authorized-to-force-delete-selected-resources="
            authorizedToForceDeleteSelectedResources
          "
          :authorized-to-restore-any-resources="authorizedToRestoreAnyResources"
          :authorized-to-restore-selected-resources="
            authorizedToRestoreSelectedResources
          "
          :available-actions="availableActions"
          :clear-selected-filters="clearSelectedFilters"
          :close-delete-modal="closeDeleteModal"
          :currently-polling="currentlyPolling"
          :delete-all-matching-resources="deleteAllMatchingResources"
          :delete-selected-resources="deleteSelectedResources"
          :filter-changed="filterChanged"
          :force-delete-all-matching-resources="forceDeleteAllMatchingResources"
          :force-delete-selected-resources="forceDeleteSelectedResources"
          :get-resources="getResources"
          :has-filters="hasFilters"
          :have-standalone-actions="haveStandaloneActions"
          :lenses="lenses"
          :per-page-options="perPageOptions"
          :per-page="perPage"
          :pivot-actions="pivotActions"
          :pivot-name="pivotName"
          :resources="resources"
          :resource-information="resourceInformation"
          :resource-name="resourceName"
          :restore-all-matching-resources="restoreAllMatchingResources"
          :restore-selected-resources="restoreSelectedResources"
          :select-all-matching-checked="selectAllMatchingResources"
          :selected-resources="selectedResources"
          :selected-resources-for-action-selector="
            selectedResourcesForActionSelector
          "
          :should-show-action-selector="shouldShowActionSelector"
          :should-show-check-boxes="shouldShowCheckBoxes"
          :should-show-delete-menu="shouldShowDeleteMenu"
          :should-show-polling-toggle="shouldShowPollingToggle"
          :soft-deletes="softDeletes"
          @start-polling="startPolling"
          @stop-polling="stopPolling"
          :toggle-select-all-matching="toggleSelectAllMatching"
          :toggle-select-all="toggleSelectAll"
          :trashed-changed="trashedChanged"
          :trashed-parameter="trashedParameter"
          :trashed="trashed"
          :update-per-page-changed="updatePerPageChanged"
          :via-has-one="viaHasOne"
          :via-many-to-many="viaManyToMany"
          :via-resource="viaResource"
        />

        <LoadingView :loading="loading">
          <IndexErrorDialog
            v-if="resourceResponseError != null"
            :resource="resourceInformation"
            @click="getResources"
          />

          <template v-else>
            <IndexEmptyDialog
              v-if="!resources.length"
              :create-button-label="createButtonLabel"
              :singular-name="singularName"
              :resource-name="resourceName"
              :via-resource="viaResource"
              :via-resource-id="viaResourceId"
              :via-relationship="viaRelationship"
              :relationship-type="relationshipType"
              :authorized-to-create="authorizedToCreate"
              :authorized-to-relate="authorizedToRelate"
            />

            <ResourceTable
              :authorized-to-relate="authorizedToRelate"
              :resource-name="resourceName"
              :resources="resources"
              :singular-name="singularName"
              :selected-resources="selectedResources"
              :selected-resource-ids="selectedResourceIds"
              :actions-are-available="allActions.length > 0"
              :should-show-checkboxes="shouldShowCheckBoxes"
              :via-resource="viaResource"
              :via-resource-id="viaResourceId"
              :via-relationship="viaRelationship"
              :relationship-type="relationshipType"
              :update-selection-status="updateSelectionStatus"
              :sortable="sortable"
              @order="orderByField"
              @reset-order-by="resetOrderBy"
              @delete="deleteResources"
              @restore="restoreResources"
              @actionExecuted="getResources"
              ref="resourceTable"
            />

            <ResourcePagination
              :pagination-component="paginationComponent"
              :should-show-pagination="shouldShowPagination"
              :has-next-page="hasNextPage"
              :has-previous-page="hasPreviousPage"
              :load-more="loadMore"
              :select-page="selectPage"
              :total-pages="totalPages"
              :current-page="currentPage"
              :per-page="perPage"
              :resource-count-label="resourceCountLabel"
              :current-resource-count="currentResourceCount"
              :all-matching-resource-count="allMatchingResourceCount"
            />
          </template>
        </LoadingView>
      </Card>
    </template>
  </LoadingView>
</template>

<script>
import debounce from 'lodash/debounce'
import { CancelToken, Cancel } from 'axios'
import {
  HasCards,
  Paginatable,
  PerPageable,
  Deletable,
  Collapsable,
  LoadsResources,
  IndexConcerns,
  InteractsWithResourceInformation,
  InteractsWithQueryString,
  SupportsPolling,
} from '@/mixins'
import { minimum } from '@/util'

export default {
  name: 'ResourceIndex',

  mixins: [
    Collapsable,
    Deletable,
    HasCards,
    Paginatable,
    PerPageable,
    LoadsResources,
    IndexConcerns,
    InteractsWithResourceInformation,
    InteractsWithQueryString,
    SupportsPolling,
  ],

  props: {
    shouldOverrideMeta: {
      type: Boolean,
      default: false,
    },

    shouldEnableShortcut: {
      type: Boolean,
      default: false,
    },
  },

  data: () => ({
    debouncer: null,
    search: '',
    lenses: [],
    sortable: true,
    actionCanceller: null,
  }),

  setup() {
    //
  },

  /**
   * Mount the component and retrieve its initial data.
   */
  async created() {
    if (!this.resourceInformation) {
      return
    }

    this.debouncer = debounce(
      callback => callback(),
      this.resourceInformation.debounce
    )

    this.$watch('search', newValue => {
      this.search = newValue
      this.performSearch()
    })

    // Bind the keydown event listener when the router is visited if this
    // component is not a relation on a Detail page
    if (this.shouldEnableShortcut === true) {
      Nova.addShortcut('c', this.handleKeydown)
    }

    this.getLenses()

    Nova.$on('refresh-resources', this.getResources)

    if (this.actionCanceller !== null) this.actionCanceller()
  },

  /**
   * Unbind the keydown even listener when the before component is destroyed
   */
  beforeUnmount() {
    if (this.shouldEnableShortcut) {
      Nova.disableShortcut('c')
    }

    Nova.$off('refresh-resources', this.getResources)

    if (this.actionCanceller !== null) this.actionCanceller()
  },

  methods: {
    /**
     * Handle the keydown event
     */
    handleKeydown(e) {
      // `c`
      if (
        this.authorizedToCreate &&
        e.target.tagName != 'INPUT' &&
        e.target.tagName != 'TEXTAREA' &&
        e.target.contentEditable != 'true'
      ) {
        Nova.visit(`/resources/${this.resourceName}/new`)
      }
    },

    /**
     * Get the resources based on the current page, search, filters, etc.
     */
    getResources() {
      if (this.shouldBeCollapsed) {
        this.loading = false
        return
      }

      this.loading = true
      this.resourceResponseError = null

      this.$nextTick(() => {
        this.clearResourceSelections()

        return minimum(
          Nova.request().get('/nova-api/' + this.resourceName, {
            params: this.resourceRequestQueryString,
            cancelToken: new CancelToken(canceller => {
              this.canceller = canceller
            }),
          }),
          300
        )
          .then(({ data }) => {
            this.resources = []

            this.resourceResponse = data
            this.resources = data.resources
            this.softDeletes = data.softDeletes
            this.perPage = data.per_page
            this.sortable = data.sortable

            this.handleResourcesLoaded()
          })
          .catch(e => {
            if (e instanceof Cancel) {
              return
            }

            this.loading = false
            this.resourceResponseError = e

            throw e
          })
      })
    },

    /**
     * Get the relatable authorization status for the resource.
     */
    getAuthorizationToRelate() {
      if (
        this.shouldBeCollapsed ||
        (!this.authorizedToCreate &&
          this.relationshipType != 'belongsToMany' &&
          this.relationshipType != 'morphToMany')
      ) {
        return
      }

      if (!this.viaResource) {
        return (this.authorizedToRelate = true)
      }

      return Nova.request()
        .get(
          '/nova-api/' +
            this.resourceName +
            '/relate-authorization' +
            '?viaResource=' +
            this.viaResource +
            '&viaResourceId=' +
            this.viaResourceId +
            '&viaRelationship=' +
            this.viaRelationship +
            '&relationshipType=' +
            this.relationshipType
        )
        .then(response => {
          this.authorizedToRelate = response.data.authorized
        })
    },

    /**
     * Get the lenses available for the current resource.
     */
    getLenses() {
      this.lenses = []

      if (this.viaResource) {
        return
      }

      return Nova.request()
        .get('/nova-api/' + this.resourceName + '/lenses')
        .then(response => {
          this.lenses = response.data
        })
    },

    /**
     * Get the actions available for the current resource.
     */
    getActions() {
      if (this.actionCanceller !== null) this.actionCanceller()

      this.actions = []
      this.pivotActions = null

      if (this.shouldBeCollapsed) {
        return
      }

      return Nova.request()
        .get(`/nova-api/${this.resourceName}/actions`, {
          params: {
            viaResource: this.viaResource,
            viaResourceId: this.viaResourceId,
            viaRelationship: this.viaRelationship,
            relationshipType: this.relationshipType,
            display: 'index',
            resources: this.selectedResourcesForActionSelector,
          },
          cancelToken: new CancelToken(canceller => {
            this.actionCanceller = canceller
          }),
        })
        .then(response => {
          this.actions = response.data.actions
          this.pivotActions = response.data.pivotActions
          this.resourceHasActions = response.data.counts.resource > 0
        })
        .catch(e => {
          if (e instanceof Cancel) {
            return
          }

          throw e
        })
    },

    /**
     * Get the count of all of the matching resources.
     */
    getAllMatchingResourceCount() {
      Nova.request()
        .get('/nova-api/' + this.resourceName + '/count', {
          params: this.resourceRequestQueryString,
        })
        .then(response => {
          this.allMatchingResourceCount = response.data.count
        })
    },

    /**
     * Load more resources.
     */
    loadMore() {
      if (this.currentPageLoadMore === null) {
        this.currentPageLoadMore = this.currentPage
      }

      this.currentPageLoadMore = this.currentPageLoadMore + 1

      return minimum(
        Nova.request().get('/nova-api/' + this.resourceName, {
          params: {
            ...this.resourceRequestQueryString,
            page: this.currentPageLoadMore, // We do this to override whatever page number is in the URL
          },
        }),
        300
      ).then(({ data }) => {
        this.resourceResponse = data
        this.resources = [...this.resources, ...data.resources]

        if (data.total !== null) {
          this.allMatchingResourceCount = data.total
        } else {
          this.getAllMatchingResourceCount()
        }

        Nova.$emit('resources-loaded', {
          resourceName: this.resourceName,
          mode: this.isRelation ? 'related' : 'index',
        })
      })
    },

    async handleCollapsableChange() {
      this.loading = true

      this.toggleCollapse()

      if (!this.collapsed) {
        await this.getResources()
        await this.getAuthorizationToRelate()
        await this.getActions()
        this.restartPolling()
      } else {
        this.loading = false
      }
    },
  },

  computed: {
    actionQueryString() {
      return {
        currentSearch: this.currentSearch,
        encodedFilters: this.encodedFilters,
        currentTrashed: this.currentTrashed,
        viaResource: this.viaResource,
        viaResourceId: this.viaResourceId,
        viaRelationship: this.viaRelationship,
      }
    },

    /**
     * Determine if the index view should be collapsed.
     */
    shouldBeCollapsed() {
      return this.collapsed && this.viaRelationship != null
    },

    /**
     * Get the endpoint for this resource's metrics.
     */
    cardsEndpoint() {
      return `/nova-api/${this.resourceName}/cards`
    },

    /**
     * Build the resource request query string.
     */
    resourceRequestQueryString() {
      return {
        search: this.currentSearch,
        filters: this.encodedFilters,
        orderBy: this.currentOrderBy,
        orderByDirection: this.currentOrderByDirection,
        perPage: this.currentPerPage,
        trashed: this.currentTrashed,
        page: this.currentPage,
        viaResource: this.viaResource,
        viaResourceId: this.viaResourceId,
        viaRelationship: this.viaRelationship,
        viaResourceRelationship: this.viaResourceRelationship,
        relationshipType: this.relationshipType,
      }
    },

    /**
     * Determine whether the user is authorized to perform actions on the delete menu
     */
    canShowDeleteMenu() {
      return Boolean(
        this.authorizedToDeleteSelectedResources ||
          this.authorizedToForceDeleteSelectedResources ||
          this.authorizedToRestoreSelectedResources ||
          this.selectAllMatchingChecked
      )
    },

    /**
     * Return the heading for the view
     */
    headingTitle() {
      if (this.initialLoading) {
        return '&nbsp;'
      } else {
        if (this.isRelation && this.field) {
          return this.field.name
        } else {
          if (this.resourceResponse !== null) {
            return this.resourceResponse.label
          } else {
            return this.resourceInformation.label
          }
        }
      }
    },
  },
}
</script>
