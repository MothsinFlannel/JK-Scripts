<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">
        Setup '{{ location.name }}',
        <span class="text-uppercase"> {{ location.state }} </span>
      </h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Locations', link: '/locations/all' },
          {
            title: `${location.name}`,
            link: `/locations/${$route.params.id}`,
          },
          {
            title: `Setup \'${location.name}\'`,
          },
        ]"
      />
    </div>

    <LocationSettings
      :initial-location="location"
      @updated="onLocationUpdated"
    />

    <div class="row">
      <div class="col-md-6 col-12">
        <LocationEmails :location-id="+$route.params.id" />
      </div>

      <div class="col-md-6 col-12">
        <LocationRedemptionCategories :location-id="+$route.params.id" />
      </div>
    </div>

    <destructiveSettings
      v-if="$isAllowed('app/locations/delete')"
      @deleted="onLocationDeleted"
    />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import LocationSettings from '~/components/LocationSettings'
import LocationEmails from '~/components/LocationEmails'
import LocationRedemptionCategories from '~/components/LocationRedemptionCategories'
import DestructiveSettings from '~/components/DestructiveSettings'

export default {
  components: {
    Breadcrumb,
    LocationSettings,
    LocationEmails,
    LocationRedemptionCategories,
    DestructiveSettings,
  },
  async asyncData({ $axios, route, redirect }) {
    try {
      const { data } = await $axios.post('/app/locations/get', {
        id: +route.params.id,
      })

      if (data.success) {
        return {
          location: data.location,
        }
      }
    } catch (error) {
      if (error.response.data.exception.status === 400)
        return redirect({
          name: 'locations-all',
          query: {
            error: 'Location not found',
          },
        })
    }
  },
  data() {
    return {
      error: null,
      location: null,
    }
  },
  methods: {
    onLocationUpdated(location) {
      this.location = Object.assign({}, location)
    },
    onLocationDeleted() {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete the location ${this.location.name}?`,
          {
            buttonSize: 'sm',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              const { data } = await this.$axios.post('/app/locations/delete', {
                id: this.location.id,
              })

              if (data.success) {
                this.$router.push({
                  name: 'locations-all',
                })
              }
            } catch (error) {
              this.$bvToast.toast(this.$getErrorMessage(error), {
                title: 'Error!',
                variant: 'danger',
              })
            }
          }
        })
        .catch((error) => {
          console.error(error)
        })
    },
  },
}
</script>
