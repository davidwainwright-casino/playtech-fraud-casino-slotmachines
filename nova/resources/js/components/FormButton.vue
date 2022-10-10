<template>
  <form
    :action="href"
    method="POST"
    @submit="handleSubmit"
    data-testid="form-button"
  >
    <input
      v-for="(value, key) in data"
      type="hidden"
      :name="key"
      :value="value"
    />

    <input
      v-if="method !== 'POST'"
      type="hidden"
      name="_method"
      :value="method"
    />

    <component :is="component" v-bind="$attrs" type="submit">
      <slot />
    </component>
  </form>
</template>

<script>
import isNil from 'lodash/isNil'
import omit from 'lodash/omit'

export default {
  inheritAttrs: false,

  props: {
    href: { type: String, required: true },
    method: { type: String, required: true },
    data: { type: Object, required: false, default: {} },
    headers: { type: Object, required: false, default: null },
    component: { type: String, default: 'button' },
  },

  methods: {
    handleSubmit(e) {
      if (isNil(this.headers)) {
        return
      }

      e.preventDefault()

      this.$inertia.visit(this.href, {
        method: this.method,
        data: this.data,
        headers: this.headers,
      })
    },
  },
}
</script>
