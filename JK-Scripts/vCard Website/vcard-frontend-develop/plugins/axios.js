export default function ({ $axios, $dayjs, store }) {
  $axios.onRequest((req) => {
    req.data.accessToken = store.getters['user/token']

    req.data.meta = {
      timezone: $dayjs.tz.guess(),
    }
  })

  $axios.onResponse((res) => {
    if (res.data._state) {
      store.dispatch('stock/state', { ...res.data._state })
    }
  })

  $axios.onError((error) => {
    if (error && error.response && error.response.status === 401) {
      store.dispatch('user/logout')
    }

    if (
      error &&
      error.response &&
      error.response.status === 403 &&
      error.response.data.exception.code === 100
    ) {
      store.dispatch('user/reqiurePasswordChange')
    }
  })
}
