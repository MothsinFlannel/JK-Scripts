import createPersistedState from 'vuex-persistedstate'

export default ({ store }) => {
  createPersistedState({
    key: 'vcard',
    paths: ['user', 'stock'],
  })(store)
}
