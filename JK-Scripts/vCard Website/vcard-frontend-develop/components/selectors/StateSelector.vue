<template>
  <v-select
    :value="value"
    class="text-uppercase"
    :options="states"
    :class="{
      'is-invalid': state.$dirty && state.$error,
      'is-valid': state.$dirty && !state.$error,
    }"
    :filterable="true"
    :reduce="(state) => state.value"
    label="text"
    placeholder="Select a state"
    :multiple="multiple"
    @input="onInput"
  />
</template>

<script>
export default {
  props: {
    value: {
      type: [Array, String],
      required: true,
    },
    state: {
      type: Object,
      default: () => ({}),
    },
    multiple: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isLoading: false,
      states: [],
    }
  },
  created() {
    this.getStates()
  },
  methods: {
    async getStates() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/misc/states', {})

        this.states = data.results.map((state) => {
          return {
            value: state,
            text: state,
          }
        })
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onInput(value) {
      this.$emit('input', value)
    },
  },
}
</script>
