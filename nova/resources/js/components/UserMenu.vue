<template>
  <component
    :is="componentName"
    v-if="hasUserMenu"
    class="flex items-center"
    :placement="dropdownPlacement"
  >
    <DropdownTrigger
      class="hover:bg-gray-100 dark:hover:bg-gray-700 h-10 focus:outline-none focus:ring rounded-lg flex items-center text-sm font-semibold text-gray-600 dark:text-gray-400 px-3"
      role="navigation"
    >
      <Icon
        type="finger-print"
        :solid="true"
        v-if="currentUser.impersonating"
        class="w-8 h-8 mr-3"
      />
      <img
        v-else-if="currentUser.avatar"
        :alt="__(':name\'s Avatar', { name: userName })"
        :src="currentUser.avatar"
        class="rounded-full w-8 h-8 mr-3"
      />

      <span class="whitespace-nowrap">
        {{ userName }}
      </span>
    </DropdownTrigger>

    <template #menu>
      <DropdownMenu width="200" class="px-1">
        <nav class="py-1">
          <component
            :is="item.component"
            v-for="item in formattedItems"
            :key="item.path"
            v-bind="item.props"
            v-on="item.on"
          >
            <span v-if="item.badge" class="mr-1">
              <Badge :extra-classes="item.badge.typeClass">
                {{ item.badge.value }}
              </Badge>
            </span>

            {{ item.name }}
          </component>

          <DropdownMenuItem
            as="button"
            v-if="currentUser.impersonating"
            @click="handleStopImpersonating"
          >
            {{ __('Stop Impersonating') }}
          </DropdownMenuItem>

          <DropdownMenuItem
            as="button"
            v-if="supportsAuthentication"
            @click="attempt"
          >
            {{ __('Logout') }}
          </DropdownMenuItem>
        </nav>
      </DropdownMenu>
    </template>
  </component>
  <div v-else-if="currentUser" class="flex items-center">
    <img
      v-if="currentUser.avatar"
      :alt="__(':name\'s Avatar', { name: userName })"
      :src="currentUser.avatar"
      class="rounded-full w-8 h-8 mr-3"
    />

    <span class="whitespace-nowrap">
      {{ userName }}
    </span>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import identity from 'lodash/identity'
import isNull from 'lodash/isNull'
import omitBy from 'lodash/omitBy'
import pickBy from 'lodash/pickBy'
import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    mobile: { type: Boolean, default: false },
  },

  methods: {
    ...mapActions(['logout', 'stopImpersonating']),

    async attempt() {
      if (confirm(this.__('Are you sure you want to log out?'))) {
        this.logout(Nova.config('customLogoutPath'))
          .then(redirect => {
            if (redirect !== null) {
              location.href = redirect
              return
            }

            Nova.redirectToLogin()
          })
          .catch(e => {
            Inertia.reload()
          })
      }
    },

    handleStopImpersonating() {
      if (confirm(this.__('Are you sure you want to stop impersonating?'))) {
        this.stopImpersonating()
      }
    },
  },

  computed: {
    ...mapGetters(['currentUser', 'userMenu']),

    userName() {
      return (
        this.currentUser.name || this.currentUser.email || this.__('Nova User')
      )
    },

    formattedItems() {
      return this.userMenu.map(i => {
        let method = i.method || 'GET'
        let props = { href: i.path }

        if (i.external && method == 'GET') {
          return {
            component: 'DropdownMenuItem',
            props: {
              ...props,
              target: i.target || null,
            },
            name: i.name,
            external: i.external,
            on: {},
          }
        }

        return {
          component: 'DropdownMenuItem',
          props: pickBy(
            omitBy(
              {
                ...props,
                method: method !== 'GET' ? method : null,
                data: i.data || null,
                headers: i.headers || null,
                as: method === 'GET' ? 'link' : 'form-button',
              },
              isNull
            ),
            identity
          ),
          external: i.external,
          name: i.name,
          on: {},
          badge: i.badge,
        }
      })
    },

    hasUserMenu() {
      return (
        this.currentUser &&
        (this.formattedItems.length > 0 ||
          this.supportsAuthentication ||
          this.currentUser.impersonating)
      )
    },

    supportsAuthentication() {
      return (
        Nova.config('withAuthentication') === true ||
        this.customLogoutPath !== false
      )
    },

    customLogoutPath() {
      return Nova.config('customLogoutPath')
    },

    componentName() {
      return this.mobile === true ? 'MainMenuDropdown' : 'Dropdown'
    },

    dropdownPlacement() {
      return this.mobile === true ? 'top-start' : 'bottom-end'
    },
  },
}
</script>
