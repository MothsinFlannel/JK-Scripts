<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Offline Locations</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Locations' },
          { title: 'Offline Locations' },
        ]"
      />
    </div>

    <OfflineLocations />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import OfflineLocations from '~/components/OfflineLocations'

export default {
  components: {
    Breadcrumb,
    OfflineLocations,
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.page || !to.query.limit || !to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          page: '1',
          limit: '50',
          sort: 'id+asc',
        },
      })
    } else {
      next()
    }
  },
  asyncData({ route, redirect }) {
    if (!route.query.page || !route.query.limit || !route.query.sort) {
      redirect({
        ...route,
        query: {
          ...route.query,
          page: '1',
          limit: '50',
          sort: 'id+asc',
        },
      })
    }
  },
}
</script>
