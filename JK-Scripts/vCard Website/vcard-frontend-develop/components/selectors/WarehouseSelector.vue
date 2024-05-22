<template>
  <v-select
    :loading="isLoading"
    :value="localLocation"
    :options="warehouses"
    :filterable="false"
    :reduce="(warehouse) => warehouse.id"
    :disabled="disabled"
    label="name"
    placeholder="Select warehouse"
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
  },
  data() {
    return {
      isLoading: false,
      localLocation: null,
      warehouses: [],
    }
  },
  computed: {
    ...mapGetters({
      companyId: 'base/companyId',
    }),
  },
  watch: {
    value(id) {
      if (id) {
        this.localLocation = id
      } else {
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

        const { data } = await this.$axios.post('/app/warehouses/list', {
          filters: {
            companyId: this.companyId,
            query,
          },
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.warehouses = data.results
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onLocationChanged(warehouseId) {
      this.localLocation = this.warehouses.find(
        (warehouse) => warehouse.id === warehouseId
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
