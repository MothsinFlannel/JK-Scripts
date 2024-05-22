export const state = () => ({
  user: null,
})

export const getters = {
  token: (state) => state.user && state.user.accessToken,
  user: (state) => state.user,
}

export const mutations = {
  setUser(state, user) {
    state.user = user
  },
}

export const actions = {
  login({ commit }, user) {
    commit('setUser', user)

    this.$router.push('/')
  },
  logout({ commit }) {
    commit('setUser', null)

    this.$router.push('/sign-in')
  },
  reqiurePasswordChange() {
    this.$router.push('/change-password')
  },
}
