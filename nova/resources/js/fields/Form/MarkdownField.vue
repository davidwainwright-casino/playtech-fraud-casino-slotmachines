<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :full-width-content="true"
    :show-help-text="showHelpText"
  >
    <template #field>
      <div
        class="bg-white dark:bg-gray-900 rounded-lg overflow-hidden"
        :class="{
          'markdown-fullscreen fixed inset-0 z-50': isFullScreen,
          'form-input form-input-bordered px-0': !isFullScreen,
          'form-input-border-error': errors.has('body'),
        }"
      >
        <header
          class="flex items-center content-center justify-between border-b border-gray-200 dark:border-gray-700"
          :class="{ 'bg-gray-100': currentlyIsReadonly }"
        >
          <div class="w-full flex items-center content-center">
            <button
              type="button"
              :class="{ 'text-primary-500 font-bold': visualMode == 'write' }"
              @click.stop="write"
              class="ml-1 px-3 h-10 focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600"
            >
              {{ __('Write') }}
            </button>
            <button
              type="button"
              :class="{ 'text-primary-500 font-bold': visualMode == 'preview' }"
              @click.stop="fetchPreviewContent"
              class="px-3 h-10 focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600"
            >
              {{ __('Preview') }}
            </button>
          </div>

          <div v-if="!currentlyIsReadonly" class="flex items-center">
            <button
              :key="tool.action"
              @click.prevent="callAction(tool.action)"
              v-for="tool in tools"
              class="rounded-none w-10 h-10 ico-button inline-flex items-center justify-center px-2 text-sm border-l border-gray-200 dark:border-gray-700 focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600"
            >
              <component :is="tool.icon" class="w-4 h-4" />
            </button>
          </div>
        </header>

        <div
          v-show="visualMode == 'write'"
          @click="focus"
          class="dark:bg-gray-900 p-4"
          :class="{ 'readonly bg-gray-100': currentlyIsReadonly }"
        >
          <textarea
            ref="theTextarea"
            :class="{ 'bg-gray-100': currentlyIsReadonly }"
          />
        </div>

        <div
          v-show="visualMode == 'preview'"
          class="prose prose-sm dark:prose-invert overflow-scroll p-4"
          v-html="previewContent"
        />
      </div>
    </template>
  </DefaultField>
</template>

<script>
import each from 'lodash/each'
import map from 'lodash/map'

import CodeMirror from 'codemirror'
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  data: () => ({
    fullScreen: false,
    isFocused: false,
    visualMode: 'write',
    previewContent: '',
  }),

  codemirror: null,

  mounted() {
    Nova.$on(this.fieldAttributeValueEventName, this.listenToValueChanges)

    if (this.isVisible) {
      this.handleShowingComponent()
    }
  },

  beforeUnmount() {
    Nova.$off(this.fieldAttributeValueEventName, this.listenToValueChanges)
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
      this.codemirror = CodeMirror.fromTextArea(this.$refs.theTextarea, {
        tabSize: 4,
        indentWithTabs: true,
        lineWrapping: true,
        mode: 'markdown',
        viewportMargin: Infinity,
        extraKeys: {
          Enter: 'newlineAndIndentContinueMarkdownList',
          ...map(this.tools, tool => {
            return tool.action
          }),
        },
        readOnly: this.currentlyIsReadonly,
      })

      each(this.keyMaps, (action, map) => {
        const realMap = map.replace(
          'Cmd-',
          CodeMirror.keyMap['default'] == CodeMirror.keyMap.macDefault
            ? 'Cmd-'
            : 'Ctrl-'
        )
        this.codemirror.options.extraKeys[realMap] =
          this.actions[this.keyMaps[map]].bind(this)
      })

      this.doc().on('change', (cm, changeObj) => {
        this.value = cm.getValue()

        this.emitFieldValueChange(this.field.attribute, this.value)
      })

      this.codemirror.on('focus', () => (this.isFocused = true))
      this.codemirror.on('blur', () => (this.isFocused = false))

      this.doc().setValue(this.value ?? this.currentField.value)

      this.$nextTick(() => this.codemirror.refresh())
    },

    handleHidingComponent() {
      this.codemirror = null
    },

    onSyncedField() {
      if (this.codemirror) {
        this.doc().setValue(this.currentField.value)
      }
    },

    focus() {
      this.isFocused = true
      this.codemirror.focus()
    },

    write() {
      this.visualMode = 'write'
      this.$nextTick(() => {
        this.codemirror.refresh()
      })
    },

    preview() {},

    insert(insertion) {
      this.doc().replaceRange(insertion, {
        line: this.cursor.line,
        ch: this.cursor.ch,
      })
    },

    insertAround(start, end) {
      if (this.doc().somethingSelected()) {
        const selection = this.doc().getSelection()
        this.doc().replaceSelection(start + selection + end)
      } else {
        this.doc().replaceRange(start + end, {
          line: this.cursor.line,
          ch: this.cursor.ch,
        })
        this.doc().setCursor({
          line: this.cursor.line,
          ch: this.cursor.ch - end.length,
        })
      }
    },

    insertBefore(insertion, cursorOffset) {
      if (this.doc().somethingSelected()) {
        const selects = this.doc().listSelections()
        selects.forEach(selection => {
          const pos = [selection.head.line, selection.anchor.line].sort()

          for (let i = pos[0]; i <= pos[1]; i++) {
            this.doc().replaceRange(insertion, { line: i, ch: 0 })
          }

          this.doc().setCursor({ line: pos[0], ch: cursorOffset || 0 })
        })
      } else {
        this.doc().replaceRange(insertion, {
          line: this.cursor.line,
          ch: 0,
        })
        this.doc().setCursor({
          line: this.cursor.line,
          ch: cursorOffset || 0,
        })
      }
    },

    callAction(action) {
      if (!this.currentlyIsReadonly) {
        this.focus()
        this.actions[action].call(this)
      }
    },

    listenToValueChanges(value) {
      if (this.codemirror) {
        this.doc().setValue(value)
        this.$nextTick(() => this.codemirror.refresh())
      }
    },

    doc() {
      return this.codemirror.getDoc()
    },

    cursor() {
      return this.doc().getCursor()
    },

    rawContent() {
      return this.codemirror?.getValue()
    },

    async fetchPreviewContent() {
      const {
        data: { preview },
      } = await Nova.request().post(
        `/nova-api/${this.resourceName}/field/${this.field.attribute}/preview`,
        {
          value: this.rawContent() ?? '',
        }
      )

      this.previewContent = preview
      this.visualMode = 'preview'
    },
  },

  computed: {
    keyMaps: () => ({
      'Cmd-B': 'bold',
      'Cmd-I': 'italicize',
      'Cmd-Alt-I': 'image',
      'Cmd-K': 'link',
      F11: 'fullScreen',
      Esc: 'exitFullScreen',
    }),

    actions: () => ({
      bold() {
        if (!this.isEditable) return

        this.insertAround('**', '**')
      },

      italicize() {
        if (!this.isEditable) return

        this.insertAround('*', '*')
      },

      image() {
        if (!this.isEditable) return

        this.insertBefore('![](url)', 2)
      },

      link() {
        if (!this.isEditable) return

        this.insertAround('[', '](url)')
      },

      toggleFullScreen() {
        this.fullScreen = !this.fullScreen
        this.$nextTick(() => this.codemirror.refresh())
      },

      fullScreen() {
        this.fullScreen = true
      },

      exitFullScreen() {
        this.fullScreen = false
      },
    }),

    tools: () => [
      {
        name: 'bold',
        action: 'bold',
        className: 'fa fa-bold',
        icon: 'icon-bold',
      },
      {
        name: 'italicize',
        action: 'italicize',
        className: 'fa fa-italic',
        icon: 'icon-italic',
      },
      {
        name: 'link',
        action: 'link',
        className: 'fa fa-link',
        icon: 'icon-link',
      },
      {
        name: 'image',
        action: 'image',
        className: 'fa fa-image',
        icon: 'icon-image',
      },
      {
        name: 'fullScreen',
        action: 'toggleFullScreen',
        className: 'fa fa-expand',
        icon: 'icon-full-screen',
      },
    ],

    isFullScreen() {
      return this.fullScreen == true
    },

    isEditable() {
      return !this.currentlyIsReadonly && this.visualMode == 'write'
    },
  },
}
</script>
