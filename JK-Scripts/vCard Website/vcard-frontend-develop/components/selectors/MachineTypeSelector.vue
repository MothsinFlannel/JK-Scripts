<template>
  <v-select
    :loading="isLoading"
    :value="value"
    :options="machines"
    :filterable="false"
    :reduce="(machine) => machine.id"
    label="name"
    placeholder="Select machine type"
    @input="onMachineChanged"
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
      machines: [],
    }
  },
  created() {
    this.getMachines()
  },
  methods: {
    async getMachines() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/machine-types/list', {
          offset: 0,
          limit: -1,
        })

        this.machines = data.results
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onMachineChanged(machineId) {
      this.$emit('input', machineId)
    },
  },
}
</script>
