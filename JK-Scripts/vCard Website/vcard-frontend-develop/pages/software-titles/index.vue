<template>
  <div>
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between">
        <h3 class="mb-0">Software Titles</h3>
      </div>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Software Titles' },
        ]"
      />
    </div>

    <SoftwareTitles />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import SoftwareTitles from '~/components/SoftwareTitles'

export default {
  components: {
    Breadcrumb,
    SoftwareTitles,
  },
  beforeRouteEnter(to, from, next) {
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
}
</script>
