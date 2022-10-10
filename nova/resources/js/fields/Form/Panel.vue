<template>
  <div v-if="panel.fields.length > 0" v-show="visibleFieldsCount > 0">
    <Heading :level="1" :class="panel.helpText ? 'mb-2' : 'mb-3'">{{
      panel.name
    }}</Heading>

    <p
      v-if="panel.helpText"
      class="text-gray-500 text-sm font-semibold italic mb-3"
      v-html="panel.helpText"
    />

    <Card class="divide-y divide-gray-100 dark:divide-gray-700">
      <component
        v-for="(field, index) in panel.fields"
        :index="index"
        :key="index"
        :is="`form-${field.component}`"
        :errors="validationErrors"
        :resource-id="resourceId"
        :resource-name="resourceName"
        :related-resource-name="relatedResourceName"
        :related-resource-id="relatedResourceId"
        :field="field"
        :via-resource="viaResource"
        :via-resource-id="viaResourceId"
        :via-relationship="viaRelationship"
        :shown-via-new-relation-modal="shownViaNewRelationModal"
        :form-unique-id="formUniqueId"
        :mode="mode"
        @field-shown="handleFieldShown"
        @field-hidden="handleFieldHidden"
        @field-changed="$emit('field-changed')"
        @file-deleted="$emit('update-last-retrieved-at-timestamp')"
        @file-upload-started="$emit('file-upload-started')"
        @file-upload-finished="$emit('file-upload-finished')"
        :show-help-text="showHelpText"
      />
    </Card>
  </div>
</template>

<script>
import { HandlesPanelVisibility, mapProps } from '@/mixins'

export default {
  name: 'FormPanel',

  mixins: [HandlesPanelVisibility],

  emits: [
    'field-changed',
    'update-last-retrieved-at-timestamp',
    'file-upload-started',
    'file-upload-finished',
  ],

  props: {
    shownViaNewRelationModal: {
      type: Boolean,
      default: false,
    },

    showHelpText: {
      type: Boolean,
      default: false,
    },

    panel: {
      type: Object,
      required: true,
    },

    name: {
      default: 'Panel',
    },

    ...mapProps(['mode']),

    fields: {
      type: Array,
      default: [],
    },

    formUniqueId: {
      type: String,
    },

    validationErrors: {
      type: Object,
      required: true,
    },

    resourceName: {
      type: String,
      required: true,
    },

    resourceId: {
      type: [Number, String],
    },

    relatedResourceName: {
      type: String,
    },

    relatedResourceId: {
      type: [Number, String],
    },

    viaResource: {
      type: String,
    },

    viaResourceId: {
      type: [Number, String],
    },

    viaRelationship: {
      type: String,
    },
  },
}
</script>
