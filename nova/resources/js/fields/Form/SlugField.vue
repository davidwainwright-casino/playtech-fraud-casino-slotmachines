<template>
  <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText">
    <template #field>
      <div class="flex items-center">
        <input
          v-bind="extraAttributes"
          ref="theInput"
          class="w-full form-control form-input form-input-bordered"
          :id="field.uniqueKey"
          :dusk="field.attribute"
          v-model="value"
          :disabled="isReadonly"
        />

        <button
          class="rounded px-1 py-1 inline-flex text-sm text-gray-500 ml-1 mt-2"
          v-if="field.showCustomizeButton"
          type="button"
          @click="toggleCustomizeClick"
        >
          {{ __('Customize') }}
        </button>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from '@/mixins'
import slugify from '@/util/slugify'

export default {
  mixins: [HandlesValidationErrors, FormField],

  data: () => ({
    isListeningToChanges: false,
  }),

  mounted() {
    if (this.shouldRegisterInitialListener) {
      this.registerChangeListener()
    }
  },

  beforeUnmount() {
    this.removeChangeListener()
  },

  methods: {
    changeListener(value) {
      return value => {
        this.value = slugify(value, this.field.separator)
      }
    },

    registerChangeListener() {
      Nova.$on(this.eventName, this.handleChange)

      this.isListeningToChanges = true
    },

    removeChangeListener() {
      if (this.isListeningToChanges === true) {
        Nova.$off(this.eventName)
      }
    },

    handleChange(value) {
      this.value = slugify(value, this.field.separator)
    },

    toggleCustomizeClick() {
      if (this.field.readonly) {
        this.removeChangeListener()
        this.isListeningToChanges = false
        this.field.readonly = false
        this.field.extraAttributes.readonly = false
        this.field.showCustomizeButton = false
        this.$refs.theInput.focus()
        return
      }

      this.registerChangeListener()
      this.field.readonly = true
      this.field.extraAttributes.readonly = true
    },
  },

  computed: {
    shouldRegisterInitialListener() {
      return !this.field.updating
    },

    eventName() {
      return this.getFieldAttributeChangeEventName(this.field.from)
    },

    extraAttributes() {
      return this.field.extraAttributes || {}
    },
  },
}
</script>
