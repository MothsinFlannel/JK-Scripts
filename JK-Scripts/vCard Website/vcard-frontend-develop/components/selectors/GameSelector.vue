<template>
  <v-select
    :loading="isLoading"
    :value="value"
    :options="games"
    :filterable="false"
    :reduce="(game) => game.name"
    :multiple="multiple"
    label="name"
    placeholder="Select game"
    @input="onGameChanged"
  />
</template>

<script>
export default {
  props: {
    value: {
      type: Number,
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
      games: [],
    }
  },
  created() {
    this.getGames()
  },
  methods: {
    async getGames() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/games/list', {
          offset: 0,
          limit: -1,
        })

        this.games = data.results
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onGameChanged(programName) {
      this.$emit('input', programName)
    },
  },
}
</script>
