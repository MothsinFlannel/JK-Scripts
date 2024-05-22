<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Register New Location</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Locations', link: '/locations/all' },
          {
            title: 'Register New Location',
          },
        ]"
      />
    </div>

    <LocationSettings />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import LocationSettings from '~/components/LocationSettings'

export default {
  components: {
    Breadcrumb,
    LocationSettings,
  },
}
</script>
