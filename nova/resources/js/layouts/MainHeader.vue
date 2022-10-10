<template>
  <div>
    <header
      class="bg-white dark:bg-gray-800 flex items-center h-14 shadow-b dark:border-b dark:border-gray-700"
    >
      <button
        @click.prevent="toggleMainMenu"
        class="lg:hidden inline-flex items-center justify-center ml-1 w-12 h-12 rounded-lg focus:ring focus:ring-inset focus:outline-none ring-primary-200 dark:ring-gray-600"
        :aria-label="__('Toggle Sidebar')"
        :aria-expanded="mainMenuShown ? 'true' : 'false'"
      >
        <Icon type="menu" />
      </button>

      <div class="hidden lg:w-60 flex-shrink-0 md:flex items-center">
        <Link
          :href="$url('/')"
          class="text-gray-900 hover:text-gray-500 active:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 dark:active:text-gray-500 h-12 rounded-lg flex items-center ml-2 focus:ring focus:ring-inset focus:outline-none ring-primary-200 dark:ring-gray-600 px-4"
          :aria-label="appName"
        >
          <AppLogo class="h-6" />
        </Link>

        <LicenseWarning />
      </div>

      <div class="flex flex-1 px-4 sm:px-8 lg:px-12">
        <GlobalSearch
          class="relative z-50"
          v-if="globalSearchEnabled"
          dusk="global-search-component"
        />

        <div class="flex items-center pl-6 ml-auto">
          <ThemeDropdown />
          <NotificationCenter v-if="notificationCenterEnabled" />
          <UserMenu class="hidden md:flex ml-2" />
        </div>
      </div>
    </header>

    <!-- Mobile Sidebar -->
    <div class="lg:hidden w-60" :class="{ hidden: !mainMenuShown }">
      <div class="fixed inset-0 flex z-50">
        <div class="fixed inset-0" aria-hidden="true">
          <div
            @click="toggleMainMenu"
            class="absolute inset-0 bg-gray-600 dark:bg-gray-900 opacity-75"
          />
        </div>

        <div
          class="bg-white dark:bg-gray-800 relative flex-1 flex flex-col max-w-xxs w-full"
        >
          <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button
              @click.prevent="toggleMainMenu"
              class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
              :aria-label="__('Close Sidebar')"
            >
              <!-- Heroicon name: outline/x -->
              <svg
                class="h-6 w-6 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <div class="px-2 border-b border-gray-100 dark:border-gray-700">
            <Link
              :href="$url('/')"
              class="text-gray-900 hover:text-gray-500 active:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 dark:active:text-gray-500 h-12 px-2 rounded-lg flex items-center focus:ring focus:ring-inset focus:outline-none"
              :aria-label="appName"
            >
              <AppLogo class="h-6" />
            </Link>
          </div>

          <div class="overflow-x-auto">
            <MainMenu class="mt-3 px-2" />
          </div>

          <div
            class="bg-white dark:bg-gray-800 absolute left-0 bottom-0 right-0 py-1 px-2 md:hidden border-t border-gray-100 dark:border-gray-700"
          >
            <UserMenu :mobile="true" />
          </div>

          <div class="flex-shrink-0 w-14" aria-hidden="true">
            <!-- Dummy element to force sidebar to shrink to fit close icon -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import LicenseWarning from '@/components/LicenseWarning'

export default {
  methods: mapMutations(['toggleMainMenu']),
  components: { LicenseWarning },

  watch: {
    mainMenuShown(newValue) {
      if (newValue == true) {
        document.body.classList.add('overflow-y-hidden')
        return
      }

      document.body.classList.remove('overflow-y-hidden')
    },
  },

  beforeUnmount() {
    document.body.classList.remove('overflow-y-hidden')
  },

  computed: {
    ...mapGetters(['mainMenuShown']),

    globalSearchEnabled() {
      return Nova.config('globalSearchEnabled')
    },

    notificationCenterEnabled() {
      return Nova.config('notificationCenterEnabled')
    },

    appName() {
      return Nova.config('appName')
    },
  },
}
</script>
