<template>
  <v-select
    :loading="isLoading"
    :value="localLocation"
    :options="locations"
    :filterable="false"
    :reduce="(location) => location.id"
    :disabled="disabled"
    label="name"
    placeholder="Start typing to search location"
    @input="onLocationChanged"
    @search="onSearch"
  >
    <template slot="no-options">
      No result
    </template>
  </v-select>
</template>

<script>
import { mapGetters } from 'vuex'
import _ from 'lodash'

export default {
  props: {
    value: {
      type: Number,
      default: null,
    },
    emitLocation: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    onlyLiveLocations: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isLoading: false,
      localLocation: null,
      locations: [],
    }
  },
  computed: {
    ...mapGetters({
      companyId: 'base/companyId',
    }),
  },
  watch: {
    value(id) {
      if (!id) {
        this.localLocation = null
      }
    },
  },
  created() {
    this.getLocations()
    if (this.value) {
      this.localLocation = this.value
    }
  },
  methods: {
    async getLocations(query = null) {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/locations/list', {
          filters: {
            companyId: this.companyId,
            live: this.onlyLiveLocations ? true : null,
            query,
          },
          offset: 0,
          limit: 20,
        })

        if (data.success) {
          this.locations = data.results
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onLocationChanged(locationId) {
      this.localLocation = this.locations.find(
        (location) => location.id === locationId
      )

      this.$emit('input', this.localLocation?.id || null)
    },
    onSearch(query, loading) {
      this.searchLocations(query, loading)
    },
    searchLocations: _.debounce(async function (query, loading) {
      loading(true)
      await this.getLocations(query)
      loading(false)
    }, 500),
  },
}
</script>
