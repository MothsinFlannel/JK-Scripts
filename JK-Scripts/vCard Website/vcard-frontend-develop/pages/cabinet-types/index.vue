<template>
  <div>
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between">
        <h3 class="mb-0">Cabinet Types</h3>
      </div>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Cabinet Types' },
        ]"
      />
    </div>

    <AllCabinetTypes />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import AllCabinetTypes from '~/components/AllCabinetTypes'

export default {
  components: {
    Breadcrumb,
    AllCabinetTypes,
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.page || !to.query.limit || !to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          page: '1',
          limit: '50',
          sort: 'name+asc',
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
          sort: 'name+asc',
        },
      })
    }
  },
}
</script>
