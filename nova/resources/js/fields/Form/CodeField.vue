<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :full-width-content="true"
    :show-help-text="showHelpText"
  >
    <template #field>
      <textarea
        ref="theTextarea"
        :id="currentField.uniqueKey"
        class="w-full form-control form-input form-input-bordered py-3 h-auto"
      />
    </template>
  </DefaultField>
</template>

<script>
import CodeMirror from 'codemirror'

// Modes

import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  codemirror: null,

  /**
   * Mount the component.
   */
  mounted() {
    this.setInitialValue()

    if (this.isVisible) {
      this.handleShowingComponent()
    }
  },

  watch: {
    currentlyIsVisible(current, previous) {
      if (current === true && previous === false) {
        this.$nextTick(() => this.handleShowingComponent())
      } else if (current === false && previous === true) {
        this.handleHidingComponent()
      }
    },
  },

  methods: {
    handleShowingComponent() {
      const config = {
        tabSize: 4,
        indentWithTabs: true,
        lineWrapping: true,
        lineNumbers: true,
        theme: 'dracula',
        ...{ readOnly: this.currentlyIsReadonly },
        ...this.currentField.options,
      }

      this.codemirror = CodeMirror.fromTextArea(this.$refs.theTextarea, config)
      this.codemirror.getDoc().setValue(this.value ?? this.currentField.value)
      this.codemirror.setSize('100%', this.currentField.height)
      this.codemirror.getDoc().on('change', (cm, changeObj) => {
        this.value = cm.getValue()

        if (this.field) {
          this.emitFieldValueChange(this.field.attribute, this.value)
        }
      })
    },

    handleHidingComponent() {
      this.codemirror = null
    },

    onSyncedField() {
      if (this.codemirror) {
        this.codemirror.getDoc().setValue(this.currentField.value)
      }
    },
  },
}
</script>
