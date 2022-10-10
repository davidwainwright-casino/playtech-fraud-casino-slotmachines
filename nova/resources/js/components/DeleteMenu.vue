<template>
  <div class="h-9" v-if="hasDropDownMenuItems">
    <Dropdown class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
      <span class="sr-only">{{ __('Trash Dropdown') }}</span>
      <DropdownTrigger class="px-2">
        <Icon class="text-gray-500 dark:text-gray-400" type="trash" />
      </DropdownTrigger>

      <template #menu>
        <DropdownMenu
          class="divide-y divide-gray-100 dark:divide-gray-800 divide-solid"
          width="250"
        >
          <!-- Delete Menu -->
          <DropdownMenuItem
            v-if="shouldShowDeleteItem"
            as="button"
            class="border-none"
            dusk="delete-selected-button"
            @click.prevent="confirmDeleteSelectedResources"
          >
            {{ __(viaManyToMany ? 'Detach Selected' : 'Delete Selected') }}
            <CircleBadge>{{ selectedResourcesCount }}</CircleBadge>
          </DropdownMenuItem>

          <!-- Restore Resources -->
          <DropdownMenuItem
            v-if="shouldShowRestoreItem"
            as="button"
            dusk="restore-selected-button"
            @click.prevent="confirmRestore"
          >
            {{ __('Restore Selected') }}
            <CircleBadge>{{ selectedResourcesCount }}</CircleBadge>
          </DropdownMenuItem>

          <!-- Force Delete Resources -->
          <DropdownMenuItem
            v-if="shouldShowForceDeleteItem"
            as="button"
            dusk="force-delete-selected-button"
            @click.prevent="confirmForceDeleteSelectedResources"
          >
            {{ __('Force Delete Selected') }}
            <CircleBadge>{{ selectedResourcesCount }}</CircleBadge>
          </DropdownMenuItem>
        </DropdownMenu>
      </template>
    </Dropdown>

    <DeleteResourceModal
      :mode="viaManyToMany ? 'detach' : 'delete'"
      :show="selectedResources.length > 0 && deleteSelectedModalOpen"
      @close="closeDeleteSelectedModal"
      @confirm="deleteSelectedResources"
    />

    <DeleteResourceModal
      :show="selectedResources.length > 0 && forceDeleteSelectedModalOpen"
      mode="delete"
      @close="closeForceDeleteSelectedModal"
      @confirm="forceDeleteSelectedResources"
    >
      <ModalHeader v-text="__('Force Delete Resource')" />
      <ModalContent>
        <p
          class="leading-normal"
          v-text="
            __('Are you sure you want to force delete the selected resources?')
          "
        />
      </ModalContent>
    </DeleteResourceModal>

    <RestoreResourceModal
      :show="selectedResources.length > 0 && restoreModalOpen"
      @close="closeRestoreModal"
      @confirm="restoreSelectedResources"
    />
  </div>
</template>

<script>
import find from 'lodash/find'
import { RouteParameters } from '@/mixins'

export default {
  emits: [
    'close',
    'deleteAllMatching',
    'deleteSelected',
    'forceDeleteAllMatching',
    'forceDeleteSelected',
    'restoreAllMatching',
    'restoreSelected',
  ],

  mixins: [RouteParameters],

  props: [
    'allMatchingResourceCount',
    'allMatchingSelected',
    'authorizedToDeleteAnyResources',
    'authorizedToDeleteSelectedResources',
    'authorizedToForceDeleteAnyResources',
    'authorizedToForceDeleteSelectedResources',
    'authorizedToRestoreAnyResources',
    'authorizedToRestoreSelectedResources',
    'resources',
    'selectedResources',
    'show',
    'softDeletes',
    'trashedParameter',
    'viaManyToMany',
  ],

  data: () => ({
    deleteSelectedModalOpen: false,
    forceDeleteSelectedModalOpen: false,
    restoreModalOpen: false,
  }),

  /**
   * Mount the component.
   */
  mounted() {
    document.addEventListener('keydown', this.handleEscape)

    Nova.$on('close-dropdowns', this.handleClosingDropdown)
  },

  /**
   * Prepare the component to be unmounted.
   */
  beforeUnmount() {
    document.removeEventListener('keydown', this.handleEscape)

    Nova.$off('close-dropdowns', this.handleClosingDropdown)
  },

  methods: {
    confirmDeleteSelectedResources() {
      this.deleteSelectedModalOpen = true
    },

    confirmForceDeleteSelectedResources() {
      this.forceDeleteSelectedModalOpen = true
    },

    confirmRestore() {
      this.restoreModalOpen = true
    },

    closeDeleteSelectedModal() {
      this.deleteSelectedModalOpen = false
    },

    closeForceDeleteSelectedModal() {
      this.forceDeleteSelectedModalOpen = false
    },

    closeRestoreModal() {
      this.restoreModalOpen = false
    },

    /**
     * Delete the selected resources.
     */
    deleteSelectedResources() {
      this.$emit(
        this.allMatchingSelected ? 'deleteAllMatching' : 'deleteSelected'
      )
    },

    /**
     * Force delete the selected resources.
     */
    forceDeleteSelectedResources() {
      this.$emit(
        this.allMatchingSelected
          ? 'forceDeleteAllMatching'
          : 'forceDeleteSelected'
      )
    },

    /**
     * Restore the selected resources.
     */
    restoreSelectedResources() {
      this.$emit(
        this.allMatchingSelected ? 'restoreAllMatching' : 'restoreSelected'
      )
    },

    /**
     * Handle the escape key press event.
     */
    handleEscape(e) {
      if (this.show && e.keyCode == 27) {
        this.close()
      }
    },

    /**
     * Close the modal.
     */
    close() {
      this.$emit('close')
    },

    /**
     * Handle closing the dropdown.
     */
    handleClosingDropdown() {
      this.deleteSelectedModalOpen = false
      this.forceDeleteSelectedModalOpen = false
      this.restoreModalOpen = false
    },
  },

  computed: {
    trashedOnlyMode() {
      return this.route.params[this.trashedParameter] == 'only'
    },

    hasDropDownMenuItems() {
      return (
        this.shouldShowDeleteItem ||
        this.shouldShowRestoreItem ||
        this.shouldShowForceDeleteItem
      )
    },

    shouldShowDeleteItem() {
      return (
        !this.trashedOnlyMode &&
        Boolean(
          this.authorizedToDeleteSelectedResources || this.allMatchingSelected
        )
      )
    },

    shouldShowRestoreItem() {
      return (
        this.softDeletes &&
        !this.viaManyToMany &&
        (this.softDeletedResourcesSelected || this.allMatchingSelected) &&
        (this.authorizedToRestoreSelectedResources || this.allMatchingSelected)
      )
    },

    shouldShowForceDeleteItem() {
      return (
        this.softDeletes &&
        !this.viaManyToMany &&
        (this.authorizedToForceDeleteSelectedResources ||
          this.allMatchingSelected)
      )
    },

    selectedResourcesCount() {
      return this.allMatchingSelected
        ? this.allMatchingResourceCount
        : this.selectedResources.length
    },

    /**
     * Determine if any soft deleted resources are selected.
     */
    softDeletedResourcesSelected() {
      return Boolean(
        find(this.selectedResources, resource => resource.softDeleted)
      )
    },
  },
}
</script>
