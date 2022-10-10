<template>
  <div v-if="field.authorizedToCreate">
    <Heading :level="4" :class="panel.helpText ? 'mb-2' : 'mb-3'">{{
      panel.name
    }}</Heading>

    <p
      v-if="panel.helpText"
      class="text-gray-500 text-sm font-semibold italic mb-3"
      v-html="panel.helpText"
    ></p>

    <component
      :is="`form-${field.component}`"
      :errors="validationErrors"
      :resource-id="relationId"
      :resource-name="field.resourceName"
      :field="field"
      :via-resource="field.from.viaResource"
      :via-resource-id="field.from.viaResourceId"
      :via-relationship="field.from.viaRelationship"
      :form-unique-id="relationFormUniqueId"
      :mode="mode"
      @field-changed="$emit('field-changed')"
      @file-deleted="$emit('update-last-retrieved-at-timestamp')"
      @file-upload-started="$emit('file-upload-started')"
      @file-upload-finished="$emit('file-upload-finished')"
      :show-help-text="showHelpText"
    />
  </div>
</template>

<script>
import { uid } from 'uid/single'
import { BehavesAsPanel } from '@/mixins'
import { mapProps } from '@/mixins'

export default {
  name: 'FormRelationshipPanel',

  emits: [
    'field-changed',
    'update-last-retrieved-at-timestamp',
    'file-upload-started',
    'file-upload-finished',
  ],

  mixins: [BehavesAsPanel],

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
      default: 'Relationship Panel',
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

  data: () => ({
    relationFormUniqueId: uid(),
  }),

  mounted() {
    if (!this.field.authorizedToCreate) {
      this.field.fill = () => {}
    }
  },

  computed: {
    field() {
      return this.panel.fields[0]
    },

    relationId() {
      if (['hasOne', 'morphOne'].includes(this.field.relationshipType)) {
        return this.field.hasOneId
      }
    },
  },
}
</script>
