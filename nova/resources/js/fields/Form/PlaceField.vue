<template>
  <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText">
    <template #field>
      <input
        :ref="field.attribute"
        :id="field.uniqueKey"
        :dusk="field.attribute"
        type="text"
        v-model="value"
        class="w-full form-control form-input form-input-bordered"
        :class="errorClasses"
        :placeholder="field.name"
        :disabled="isReadonly"
      />
    </template>
  </DefaultField>
</template>

<script>
import find from 'lodash/find'
import { FormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, FormField],

  /**
   * Mount the component.
   */
  mounted() {
    this.setInitialValue()

    this.field.fill = this.fill

    this.initializePlaces()
  },

  methods: {
    /**
     * Initialize Algolia places library.
     */
    initializePlaces() {
      const places = require('places.js')

      const placeType = this.field.placeType

      const config = {
        appId: Nova.config('algoliaAppId'),
        apiKey: Nova.config('algoliaApiKey'),
        container: this.$refs[this.field.attribute],
        type: this.field.placeType ? this.field.placeType : 'address',
        templates: {
          value(suggestion) {
            return suggestion.name
          },
        },
      }

      if (this.field.countries) {
        config.countries = this.field.countries
      }

      if (this.field.language) {
        config.language = this.field.language
      }

      const placesAutocomplete = places(config)

      placesAutocomplete.on('change', e => {
        this.$nextTick(() => {
          this.value = e.suggestion.name

          this.emitFieldValue(this.field.secondAddressLine, '')
          this.emitFieldValue(this.field.city, e.suggestion.city)

          this.emitFieldValue(
            this.field.state,
            this.parseState(
              e.suggestion.administrative,
              e.suggestion.countryCode
            )
          )

          this.emitFieldValue(this.field.postalCode, e.suggestion.postcode)
          this.emitFieldValue(this.field.suburb, e.suggestion.suburb)

          this.emitFieldValue(
            this.field.country,
            e.suggestion.countryCode.toUpperCase()
          )

          this.emitFieldValue(this.field.latitude, e.suggestion.latlng.lat)
          this.emitFieldValue(this.field.longitude, e.suggestion.latlng.lng)
        })
      })

      placesAutocomplete.on('clear', () => {
        this.$nextTick(() => {
          this.value = ''

          this.emitFieldValue(this.field.secondAddressLine, '')
          this.emitFieldValue(this.field.city, '')
          this.emitFieldValue(this.field.state, '')
          this.emitFieldValue(this.field.postalCode, '')
          this.emitFieldValue(this.field.suburb, '')
          this.emitFieldValue(this.field.country, '')
          this.emitFieldValue(this.field.latitude, '')
          this.emitFieldValue(this.field.longitude, '')
        })
      })
    },

    /**
     * Parse the selected state into an abbreviation if possible.
     */
    parseState(state, countryCode) {
      if (countryCode != 'us') {
        return state
      }

      return find(this.states, s => {
        return s.name == state
      }).abbr
    },
  },

  computed: {
    /**
     * Get the list of United States.
     */
    states() {
      return {
        AL: {
          count: '0',
          name: 'Alabama',
          abbr: 'AL',
        },
        AK: {
          count: '1',
          name: 'Alaska',
          abbr: 'AK',
        },
        AZ: {
          count: '2',
          name: 'Arizona',
          abbr: 'AZ',
        },
        AR: {
          count: '3',
          name: 'Arkansas',
          abbr: 'AR',
        },
        CA: {
          count: '4',
          name: 'California',
          abbr: 'CA',
        },
        CO: {
          count: '5',
          name: 'Colorado',
          abbr: 'CO',
        },
        CT: {
          count: '6',
          name: 'Connecticut',
          abbr: 'CT',
        },
        DE: {
          count: '7',
          name: 'Delaware',
          abbr: 'DE',
        },
        DC: {
          count: '8',
          name: 'District Of Columbia',
          abbr: 'DC',
        },
        FL: {
          count: '9',
          name: 'Florida',
          abbr: 'FL',
        },
        GA: {
          count: '10',
          name: 'Georgia',
          abbr: 'GA',
        },
        HI: {
          count: '11',
          name: 'Hawaii',
          abbr: 'HI',
        },
        ID: {
          count: '12',
          name: 'Idaho',
          abbr: 'ID',
        },
        IL: {
          count: '13',
          name: 'Illinois',
          abbr: 'IL',
        },
        IN: {
          count: '14',
          name: 'Indiana',
          abbr: 'IN',
        },
        IA: {
          count: '15',
          name: 'Iowa',
          abbr: 'IA',
        },
        KS: {
          count: '16',
          name: 'Kansas',
          abbr: 'KS',
        },
        KY: {
          count: '17',
          name: 'Kentucky',
          abbr: 'KY',
        },
        LA: {
          count: '18',
          name: 'Louisiana',
          abbr: 'LA',
        },
        ME: {
          count: '19',
          name: 'Maine',
          abbr: 'ME',
        },
        MD: {
          count: '20',
          name: 'Maryland',
          abbr: 'MD',
        },
        MA: {
          count: '21',
          name: 'Massachusetts',
          abbr: 'MA',
        },
        MI: {
          count: '22',
          name: 'Michigan',
          abbr: 'MI',
        },
        MN: {
          count: '23',
          name: 'Minnesota',
          abbr: 'MN',
        },
        MS: {
          count: '24',
          name: 'Mississippi',
          abbr: 'MS',
        },
        MO: {
          count: '25',
          name: 'Missouri',
          abbr: 'MO',
        },
        MT: {
          count: '26',
          name: 'Montana',
          abbr: 'MT',
        },
        NE: {
          count: '27',
          name: 'Nebraska',
          abbr: 'NE',
        },
        NV: {
          count: '28',
          name: 'Nevada',
          abbr: 'NV',
        },
        NH: {
          count: '29',
          name: 'New Hampshire',
          abbr: 'NH',
        },
        NJ: {
          count: '30',
          name: 'New Jersey',
          abbr: 'NJ',
        },
        NM: {
          count: '31',
          name: 'New Mexico',
          abbr: 'NM',
        },
        NY: {
          count: '32',
          name: 'New York',
          abbr: 'NY',
        },
        NC: {
          count: '33',
          name: 'North Carolina',
          abbr: 'NC',
        },
        ND: {
          count: '34',
          name: 'North Dakota',
          abbr: 'ND',
        },
        OH: {
          count: '35',
          name: 'Ohio',
          abbr: 'OH',
        },
        OK: {
          count: '36',
          name: 'Oklahoma',
          abbr: 'OK',
        },
        OR: {
          count: '37',
          name: 'Oregon',
          abbr: 'OR',
        },
        PA: {
          count: '38',
          name: 'Pennsylvania',
          abbr: 'PA',
        },
        RI: {
          count: '39',
          name: 'Rhode Island',
          abbr: 'RI',
        },
        SC: {
          count: '40',
          name: 'South Carolina',
          abbr: 'SC',
        },
        SD: {
          count: '41',
          name: 'South Dakota',
          abbr: 'SD',
        },
        TN: {
          count: '42',
          name: 'Tennessee',
          abbr: 'TN',
        },
        TX: {
          count: '43',
          name: 'Texas',
          abbr: 'TX',
        },
        UT: {
          count: '44',
          name: 'Utah',
          abbr: 'UT',
        },
        VT: {
          count: '45',
          name: 'Vermont',
          abbr: 'VT',
        },
        VA: {
          count: '46',
          name: 'Virginia',
          abbr: 'VA',
        },
        WA: {
          count: '47',
          name: 'Washington',
          abbr: 'WA',
        },
        WV: {
          count: '48',
          name: 'West Virginia',
          abbr: 'WV',
        },
        WI: {
          count: '49',
          name: 'Wisconsin',
          abbr: 'WI',
        },
        WY: {
          count: '50',
          name: 'Wyoming',
          abbr: 'WY',
        },
      }
    },
  },
}
</script>
