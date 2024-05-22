<template>
  <div
    class="d-flex align-items-center flex-sm-nowrap flex-wrap"
    :class="{ 'w-100': grid }"
  >
    <span :class="{ 'col-4 p-0': grid }" style="white-space: nowrap;"
      >Select Location:</span
    >
    <v-select
      :value="location"
      :options="locations"
      :filterable="false"
      label="name"
      :reduce="(loc) => loc.id"
      placeholder="Start typing to search location"
      :class="{ 'col-8 px-0 w-100': grid, 'ml-sm-2 ': !grid }"
      class="ml-0"
      style="min-width: 15rem;"
      @input="onLocationChanged"
      @search="onSearch"
    >
      <template slot="no-options">
        No result
      </template>
    </v-select>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import _ from 'lodash'

export default {
  props: {
    grid: {
      required: false,
      type: Boolean,
      default: () => false,
    },
    onlyLiveLocations: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      location: null,
      locations: [],
    }
  },
  computed: {
    ...mapGetters({
      companyId: 'base/companyId',
    }),
  },
  created() {
    this.getLocations()
    if (this.$route.query.location) {
      this.getLocation()
    }
  },
  methods: {
    async getLocation() {
      try {
        const { data } = await this.$axios.post('/app/locations/get', {
          id: +this.$route.query.location,
        })

        if (data.success) {
          this.location = {
            id: data.location.id,
            name: data.location.name,
          }
        }
      } catch (error) {
        console.error(error)
      }
    },
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
    onLocationChanged(location) {
      this.location = this.locations.find((loc) => loc.id === location)

      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: '1',
          location: location || undefined,
        },
      })
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
