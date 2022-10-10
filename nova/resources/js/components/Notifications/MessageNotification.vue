<template>
  <div
    class="bg-white dark:bg-gray-800 flex items-start py-4"
    :dusk="`notification-${notification.id}`"
  >
    <div class="flex-none pl-4">
      <Icon :type="icon" :class="notification.iconClass" />
    </div>

    <div class="flex-auto px-4 space-y-4">
      <div>
        <div class="flex items-center">
          <div class="flex-auto">
            <p class="mr-1 leading-normal">
              {{ notification.message }}
            </p>
          </div>

          <div class="ml-auto flex-shrink-0 space-x-1">
            <button
              type="button"
              @click.prevent.stop="handleDeleteClick"
              dusk="delete-button"
              class="flex-none ml-auto hover:opacity-50 active:opacity-75 focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 rounded"
            >
              <Icon
                type="trash"
                :solid="true"
                class="text-gray-300 dark:text-gray-600"
              />
            </button>

            <button
              type="button"
              @click.prevent.stop="$emit('mark-as-read')"
              dusk="mark-as-read-button"
              class="flex-none ml-auto hover:opacity-50 active:opacity-75 focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 rounded"
            >
              <Icon
                type="eye"
                :solid="true"
                class="text-gray-300 dark:text-gray-600 hover:opacity-50 active:opacity-75"
              />
            </button>
          </div>
        </div>

        <p class="mt-1 text-xs" :title="notification.created_at">
          {{ notification.created_at_friendly }}
        </p>
      </div>

      <DefaultButton v-if="hasUrl" @click="handleClick" size="xs">
        {{ notification.actionText }}
      </DefaultButton>
    </div>
  </div>
</template>

<script>
import { mapMutations } from 'vuex'

export default {
  name: 'MessageNotification',

  props: {
    notification: {
      type: Object,
      required: true,
    },
  },

  methods: {
    ...mapMutations(['toggleNotifications']),

    handleClick() {
      this.toggleNotifications()
      this.visit()
    },

    handleDeleteClick() {
      if (
        confirm(this.__('Are you sure you want to delete this notification?'))
      ) {
        this.$emit('delete-notification')
      }
    },

    visit() {
      if (this.hasUrl) {
        return Nova.visit(this.notification.actionUrl)
      }
    },
  },

  computed: {
    icon() {
      return this.notification.icon
    },

    hasUrl() {
      return this.notification.actionUrl
    },
  },
}
</script>
