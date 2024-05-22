<template>
  <v-select
    :loading="isLoading"
    :value="value"
    :options="cabinets"
    :filterable="false"
    :reduce="(cabinet) => cabinet.id"
    label="name"
    placeholder="Select cabinet type"
    @input="onCabinetChanged"
  />
</template>

<script>
export default {
  props: {
    value: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      cabinets: [],
    }
  },
  created() {
    this.getCabinets()
  },
  methods: {
    async getCabinets() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/cabinet-types/list', {
          offset: 0,
          limit: -1,
          filters: {
            activeOnly: true,
          },
        })

        this.cabinets = data.results
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onCabinetChanged(cabinetId) {
      this.$emit('input', cabinetId)
    },
  },
}
</script>
