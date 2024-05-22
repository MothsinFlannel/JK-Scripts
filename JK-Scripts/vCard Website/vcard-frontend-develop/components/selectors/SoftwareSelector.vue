<template>
  <v-select
    :loading="isLoading"
    :value="value"
    :options="softwares"
    :filterable="false"
    :reduce="(software) => software.name || null"
    :multiple="multiple"
    label="name"
    placeholder="Select software"
    @input="onSoftwareChanged"
  />
</template>

<script>
export default {
  props: {
    value: {
      type: [Array, String],
      default: null,
    },
    multiple: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isLoading: false,
      softwares: [],
    }
  },
  created() {
    this.getSoftwares()
  },
  methods: {
    async getSoftwares() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/software-titles/list', {
          offset: 0,
          limit: -1,
        })

        this.softwares = data.results
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onSoftwareChanged(programName) {
      this.$emit('input', programName)
    },
  },
}
</script>
