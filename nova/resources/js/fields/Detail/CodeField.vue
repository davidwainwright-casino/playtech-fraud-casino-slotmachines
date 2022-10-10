<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <div class="form-input form-input-bordered px-0 overflow-hidden">
        <textarea ref="theTextarea" />
      </div>
    </template>
  </PanelItem>
</template>

<script>
import CodeMirror from 'codemirror'

export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  codemirror: null,

  /**
   * Mount the component.
   */
  mounted() {
    const config = {
      tabSize: 4,
      indentWithTabs: true,
      lineWrapping: true,
      lineNumbers: true,
      theme: 'dracula',
      ...this.field.options,
      readOnly: true,
      tabindex: '-1', // The editor is for display only and should not be tabbable.
    }

    this.codemirror = CodeMirror.fromTextArea(this.$refs.theTextarea, config)
    this.codemirror
      ?.getDoc()
      .setValue(this.field.displayedAs || this.field.value)
    this.codemirror?.setSize('100%', this.field.height)
  },
}
</script>
