export const state = () => ({
  companyId: null,
})

export const getters = {
  companyId: (state) => state.companyId,
}

export const mutations = {
  changeCompanyId(state, companyId) {
    state.companyId = companyId
  },
}
