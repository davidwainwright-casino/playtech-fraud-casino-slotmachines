<template>
  <div v-if="hasDropdownItems">
    <Dropdown>
      <span class="sr-only">{{ __('Resource Row Dropdown') }}</span>
      <DropdownTrigger
        :dusk="`${resource.id.value}-control-selector`"
        :show-arrow="false"
        class="rounded hover:bg-gray-200 dark:hover:bg-gray-800 focus:outline-none focus:ring"
      >
        <BasicButton component="span">
          <Icon :solid="true" type="dots-horizontal" />
        </BasicButton>
      </DropdownTrigger>

      <template #menu>
        <DropdownMenu width="auto" class="px-1">
          <ScrollWrap
            :height="250"
            class="divide-y divide-gray-100 dark:divide-gray-800 divide-solid"
          >
            <div class="py-1" v-if="canModifyResource">
              <!-- Replicate Resource Link -->
              <DropdownMenuItem
                v-if="resource.authorizedToReplicate"
                :dusk="`${resource.id.value}-replicate-button`"
                :href="
                  $url(
                    `/resources/${resourceName}/${resource.id.value}/replicate`,
                    { viaResource, viaResourceId, viaRelationship }
                  )
                "
                :title="__('Replicate')"
              >
                {{ __('Replicate') }}
              </DropdownMenuItem>

              <!-- Impersonate Resource Button -->
              <DropdownMenuItem
                as="button"
                v-if="canBeImpersonated"
                :dusk="`${resource.id.value}-impersonate-button`"
                @click.prevent="
                  startImpersonating({
                    resource: resourceName,
                    resourceId: resource.id.value,
                  })
                "
                :title="__('Impersonate')"
              >
                {{ __('Impersonate') }}
              </DropdownMenuItem>

              <DropdownMenuItem
                v-if="resource.authorizedToDelete && !resource.softDeleted"
                data-testid="open-delete-modal"
                dusk="open-delete-modal-button"
                @click.prevent="openDeleteModal"
                :destructive="true"
              >
                {{ __('Delete Resource') }}
              </DropdownMenuItem>

              <DropdownMenuItem
                as="button"
                v-if="resource.authorizedToRestore && resource.softDeleted"
                class="block text-sm text-left w-full px-3 py-1 font-semibold text-red-400 hover:text-red-300 focus:text-red-600 focus:outline-none focus:ring ring-inset"
                data-testid="open-restore-modal"
                dusk="open-restore-modal-button"
                @click.prevent="openRestoreModal"
              >
                {{ __('Restore Resource') }}
              </DropdownMenuItem>

              <DropdownMenuItem
                as="button"
                v-if="resource.authorizedToForceDelete"
                class="block text-sm text-left w-full px-3 py-1 font-semibold text-red-400 hover:text-red-300 focus:text-red-600 focus:outline-none focus:ring ring-inset"
                data-testid="open-force-delete-modal"
                dusk="open-force-delete-modal-button"
                @click.prevent="openForceDeleteModal"
                :destructive="true"
              >
                {{ __('Force Delete Resource') }}
              </DropdownMenuItem>
            </div>

            <div
              v-if="actions.length > 0"
              :dusk="`${resource.id.value}-inline-actions`"
              class="py-1"
            >
              <!-- User Actions -->
              <DropdownMenuItem
                as="button"
                v-for="action in actions"
                :key="action.uriKey"
                :dusk="`${resource.id.value}-inline-action-${action.uriKey}`"
                @click="() => handleActionClick(action.uriKey)"
                :title="action.name"
                :destructive="action.destructive"
              >
                {{ action.name }}
              </DropdownMenuItem>
            </div>
          </ScrollWrap>
        </DropdownMenu>
      </template>
    </Dropdown>

    <!-- Action Confirmation Modal -->
    <component
      v-if="confirmActionModalOpened"
      :show="confirmActionModalOpened"
      :is="selectedAction.component"
      :working="working"
      :selected-resources="selectedResources"
      :resource-name="resourceName"
      :action="selectedAction"
      :endpoint="endpoint"
      :errors="errors"
      @confirm="executeAction"
      @close="closeConfirmationModal"
    />

    <!-- Action Response Modal -->
    <component
      v-if="selectedAction"
      :is="actionResponseData.modal"
      @close="closeActionResponseModal"
      :show="showActionResponseModal"
      :data="actionResponseData"
    />

    <DeleteResourceModal
      :show="deleteModalOpen"
      mode="delete"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />

    <RestoreResourceModal
      :show="restoreModalOpen"
      @close="closeRestoreModal"
      @confirm="confirmRestore"
    />

    <DeleteResourceModal
      :show="forceDeleteModalOpen"
      mode="force delete"
      @close="closeForceDeleteModal"
      @confirm="confirmForceDelete"
    />
  </div>
</template>

<script>
import {
  Deletable,
  HandlesActions,
  InteractsWithResourceInformation,
  mapProps,
} from '@/mixins'
import { mapGetters, mapActions } from 'vuex'

export default {
  emits: ['resource-deleted', 'resource-restored'],

  inheritAttrs: false,

  mixins: [Deletable, HandlesActions, InteractsWithResourceInformation],

  props: {
    resource: { type: Object },
    actions: { type: Array },
    viaManyToMany: { type: Boolean },

    ...mapProps([
      'resourceName',
      'viaResource',
      'viaResourceId',
      'viaRelationship',
    ]),
  },

  data: () => ({
    showActionResponseModal: false,
    actionResponseData: {},
    deleteModalOpen: false,
    restoreModalOpen: false,
    forceDeleteModalOpen: false,
  }),

  methods: {
    ...mapActions(['startImpersonating']),

    openPreviewModal() {
      this.previewModalOpen = true
    },

    closePreviewModal() {
      this.previewModalOpen = false
    },

    /**
     * Show the confirmation modal for deleting or detaching a resource
     */
    async confirmDelete() {
      this.deleteResources([this.resource], response => {
        Nova.success(
          this.__('The :resource was deleted!', {
            resource: this.resourceInformation.singularLabel.toLowerCase(),
          })
        )

        if (response && response.data && response.data.redirect) {
          Nova.visit(response.data.redirect)
          return
        }

        if (!this.resource.softDeletes) {
          Nova.visit(`/resources/${this.resourceName}`)
          return
        }

        this.closeDeleteModal()
        this.$emit('resource-deleted')
      })
    },

    /**
     * Open the delete modal
     */
    openDeleteModal() {
      this.deleteModalOpen = true
    },

    /**
     * Close the delete modal
     */
    closeDeleteModal() {
      this.deleteModalOpen = false
    },

    /**
     * Show the confirmation modal for restoring a resource
     */
    async confirmRestore() {
      this.restoreResources([this.resource], () => {
        Nova.success(
          this.__('The :resource was restored!', {
            resource: this.resourceInformation.singularLabel.toLowerCase(),
          })
        )

        this.closeRestoreModal()
        this.$emit('resource-restored')
      })
    },

    /**
     * Open the restore modal
     */
    openRestoreModal() {
      this.restoreModalOpen = true
    },

    /**
     * Close the restore modal
     */
    closeRestoreModal() {
      this.restoreModalOpen = false
    },

    /**
     * Show the confirmation modal for force deleting
     */
    async confirmForceDelete() {
      this.forceDeleteResources([this.resource], response => {
        Nova.success(
          this.__('The :resource was deleted!', {
            resource: this.resourceInformation.singularLabel.toLowerCase(),
          })
        )

        if (response && response.data && response.data.redirect) {
          Nova.visit(response.data.redirect)
          return
        }

        Nova.visit(`/resources/${this.resourceName}`)
      })
    },

    /**
     * Open the force delete modal
     */
    openForceDeleteModal() {
      this.forceDeleteModalOpen = true
    },

    /**
     * Close the force delete modal
     */
    closeForceDeleteModal() {
      this.forceDeleteModalOpen = false
    },
  },

  computed: {
    ...mapGetters(['currentUser']),

    currentSearch() {
      return ''
    },

    encodedFilters() {
      return ''
    },

    currentTrashed() {
      return ''
    },

    hasDropdownItems() {
      return this.actions.length > 0 || this.canModifyResource
    },

    canModifyResource() {
      return (
        this.resource.authorizedToReplicate ||
        this.canBeImpersonated ||
        (this.resource.authorizedToDelete && !this.resource.softDeleted) ||
        (this.resource.authorizedToRestore && this.resource.softDeleted) ||
        this.resource.authorizedToForceDelete
      )
    },

    canBeImpersonated() {
      return (
        this.currentUser.canImpersonate && this.resource.authorizedToImpersonate
      )
    },

    selectedResources() {
      return [this.resource.id.value]
    },
  },
}
</script>
