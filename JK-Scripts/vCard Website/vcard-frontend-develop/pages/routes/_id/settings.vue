<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Setup Route "{{ route.name }}"</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Routes', link: '/routes' },
          {
            title: `Setup \'${route.name}\'`,
          },
        ]"
      />
    </div>

    <RouteSettings :initial-route="route" @updated="onRouteUpdated" />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import RouteSettings from '~/components/RouteSettings'

export default {
  components: {
    Breadcrumb,
    RouteSettings,
  },
  async asyncData({ $axios, $getErrorMessage, route }) {
    try {
      const { data } = await $axios.post('/app/routes/get', {
        id: +route.params.id,
      })

      if (data.success) {
        return {
          route: data.route,
        }
      }
    } catch (error) {
      return {
        error: $getErrorMessage(error),
      }
    }
  },
  data() {
    return {
      error: null,
      route: null,
    }
  },
  methods: {
    onRouteUpdated(route) {
      this.route = Object.assign({}, route)
    },
  },
}
</script>
