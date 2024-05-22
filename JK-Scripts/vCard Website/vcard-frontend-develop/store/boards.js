export const state = () => ({
  boards: null,
})

export const getters = {
  boards: (state) => state.boards,
}

export const mutations = {
  setBoards(state, boards) {
    state.boards = boards
  },
}

export const actions = {
  getBoards({ commit }) {
    return this.$axios
      .post('/app/boards/list', {
        offset: 0,
        limit: -1,
      })
      .then(({ data }) => {
        commit('setBoards', data.results)
      })
      .catch((error) => console.error(error))
  },
}
