<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">New Route</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Routes', link: '/routes' },
          {
            title: 'New Route',
          },
        ]"
      />
    </div>

    <RouteSettings />
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
}
</script>
