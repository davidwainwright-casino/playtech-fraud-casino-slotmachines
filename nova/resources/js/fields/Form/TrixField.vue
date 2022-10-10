<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :full-width-content="true"
    :key="index"
    :show-help-text="showHelpText"
  >
    <template #field>
      <div class="rounded-lg" :class="{ disabled: isReadonly }">
        <Trix
          name="trixman"
          :value="value"
          @change="handleChange"
          @file-added="handleFileAdded"
          @file-removed="handleFileRemoved"
          :class="{ 'form-input-border-error': hasError }"
          :with-files="field.withFiles"
          v-bind="extraAttributes"
          :disabled="isReadonly"
          class="rounded-lg"
        />
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from '@/mixins'

export default {
  emits: ['field-changed'],

  mixins: [HandlesValidationErrors, FormField],

  data: () => ({ draftId: uuidv4(), index: 0 }),

  mounted() {
    Nova.$on(this.fieldAttributeValueEventName, this.listenToValueChanges)
  },

  beforeUnmount() {
    Nova.$off(this.fieldAttributeValueEventName, this.listenToValueChanges)
  },

  beforeUnmount() {
    this.cleanUp()
  },

  methods: {
    /**
     * Update the field's internal value when it's value changes
     */
    handleChange(value) {
      this.value = value

      this.$emit('field-changed')
    },

    fill(formData) {
      this.fillIfVisible(formData, this.field.attribute, this.value || '')
      this.fillIfVisible(
        formData,
        `${this.field.attribute}DraftId`,
        this.draftId
      )
    },

    /**
     * Initiate an attachement upload
     */
    handleFileAdded({ attachment }) {
      if (attachment.file) {
        this.uploadAttachment(attachment)
      }
    },

    /**
     * Upload an attachment
     */
    uploadAttachment(attachment) {
      const data = new FormData()
      data.append('Content-Type', attachment.file.type)
      data.append('attachment', attachment.file)
      data.append('draftId', this.draftId)

      Nova.request()
        .post(
          `/nova-api/${this.resourceName}/trix-attachment/${this.field.attribute}`,
          data,
          {
            onUploadProgress: function (progressEvent) {
              attachment.setUploadProgress(
                Math.round((progressEvent.loaded * 100) / progressEvent.total)
              )
            },
          }
        )
        .then(({ data: { url } }) => {
          return attachment.setAttributes({
            url: url,
            href: url,
          })
        })
        .catch(error => {
          this.$toasted.show(
            __('An error occured while uploading your file.'),
            { type: 'error' }
          )
        })
    },

    /**
     * Remove an attachment from the server
     */
    handleFileRemoved({ attachment: { attachment } }) {
      Nova.request()
        .delete(
          `/nova-api/${this.resourceName}/trix-attachment/${this.field.attribute}`,
          {
            params: {
              attachmentUrl: attachment.attributes.values.url,
            },
          }
        )
        .then(response => {})
        .catch(error => {})
    },

    /**
     * Purge pending attachments for the draft
     */
    cleanUp() {
      if (this.field.withFiles) {
        Nova.request()
          .delete(
            `/nova-api/${this.resourceName}/trix-attachment/${this.field.attribute}/${this.draftId}`
          )
          .then(response => {})
          .catch(error => {})
      }
    },

    listenToValueChanges(value) {
      this.index++
    },
  },

  computed: {
    defaultAttributes() {
      return {
        placeholder: this.field.placeholder || this.field.name,
      }
    },

    extraAttributes() {
      const attrs = this.field.extraAttributes

      return {
        ...this.defaultAttributes,
        ...attrs,
      }
    },
  },
}

function uuidv4() {
  return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
    (
      c ^
      (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
    ).toString(16)
  )
}
</script>
