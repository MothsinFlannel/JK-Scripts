<template>
  <v-select
    :loading="isLoading"
    :value="route"
    :options="routes"
    :filterable="false"
    :reduce="(route) => route.id"
    :disabled="disabled"
    label="name"
    placeholder="Select route"
    @input="onRouteChanged"
    @search="onSearch"
  >
    <template slot="no-options">
      No result
    </template>
  </v-select>
</template>

<script>
import _ from 'lodash'

export default {
  props: {
    value: {
      type: Number,
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isLoading: false,
      route: null,
      routes: [],
    }
  },
  watch: {
    value(id) {
      if (id) {
        this.getRoutes()
      } else {
        this.route = null
      }
    },
  },
  created() {
    this.getRoutes()
    if (this.value) {
      this.getRoutes()
    }
  },
  methods: {
    async getRoutes(query = null) {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/routes/list', {
          filters: {
            query,
          },
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.routes = data.results
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onRouteChanged(routeId) {
      this.route = this.routes.find((route) => route.id === routeId)

      this.$emit('input', this.route?.id || null)
    },
    onSearch(query, loading) {
      this.searchRoutes(query, loading)
    },
    searchRoutes: _.debounce(async function (query, loading) {
      loading(true)
      await this.getRoutes(query)
      loading(false)
    }, 500),
  },
}
</script>
