<template>
  <div class="sidebar-item">
    <component
      :is="component"
      v-bind="linkAttributes"
      class="sidebar-item-title relative"
      :class="{ 'inertia-link-active': item.active }"
      @click="handleClick"
    >
      <span class="sidebar-item-icon" />
      <span class="sidebar-item-label flex items-center">
        {{ item.name }}

        <span v-if="item.badge" class="mx-2 absolute right-0">
          <Badge :extra-classes="item.badge.typeClass" class="">
            {{ item.badge.value }}
          </Badge>
        </span>
      </span>
    </component>
  </div>
</template>

<script>
import identity from 'lodash/identity'
import isNull from 'lodash/isNull'
import omitBy from 'lodash/omitBy'
import pickBy from 'lodash/pickBy'
import { mapGetters, mapMutations } from 'vuex'

export default {
  props: {
    item: {
      type: Object,
      required: true,
    },
  },

  methods: {
    ...mapMutations(['toggleMainMenu']),

    handleClick() {
      if (this.mainMenuShown) {
        this.toggleMainMenu()
      }
    },
  },

  computed: {
    ...mapGetters(['mainMenuShown']),

    requestMethod() {
      return this.item.method || 'GET'
    },

    component() {
      if (this.requestMethod !== 'GET') {
        return 'FormButton'
      } else if (this.item.external !== true) {
        return 'Link'
      }

      return 'a'
    },

    linkAttributes() {
      let method = this.requestMethod

      return pickBy(
        omitBy(
          {
            href: this.item.path,
            method: method !== 'GET' ? method : null,
            headers: this.item.headers || null,
            data: this.item.data || null,
            rel: this.component === 'a' ? 'noreferrer noopener' : null,
            target: this.item.target || null,
          },
          isNull
        ),
        identity
      )
    },
  },
}
</script>
