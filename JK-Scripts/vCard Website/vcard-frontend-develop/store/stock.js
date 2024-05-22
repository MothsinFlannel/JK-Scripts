export const state = () => ({
  installations: null,
})

export const getters = {
  installations: (state) => state.installations,
}

export const mutations = {
  setInstallations(state, installations) {
    state.installations = installations
  },
}

export const actions = {
  state: ({ commit }, data) => {
    commit('setInstallations', data.installations)
  },
}
