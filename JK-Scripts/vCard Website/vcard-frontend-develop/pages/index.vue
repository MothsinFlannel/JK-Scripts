<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">vCard Admin</h3>

      <hr class="my-2" />

      <Breadcrumb :items="[{ title: 'vCard Dashboard' }]" />
    </div>

    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-alert
      :show="
        typeof offlineLocationsCount === 'number' && offlineLocationsCount > 0
      "
      variant="danger"
    >
      {{ offlineLocationsCount }}
      {{ offlineLocationsCount === 1 ? 'location' : 'locations' }} did not
      report for over 24 hours
    </b-alert>

    <div class="row">
      <div v-if="revenue" class="col-12 col-xl-7">
        <WeekRevenue :revenue="revenue" />
      </div>

      <div v-if="revenue" class="col-12 col-xl-5">
        <b-card header="This Week Revenue by State" class="mb-4 shadow-sm">
          <div class="row">
            <div
              v-for="state in revenue.byState"
              :key="state.state"
              class="col-6 mb-3"
            >
              <h6 class="text-uppercase">{{ state.state }}</h6>
              <span>{{ $getFormattedPrice(state.amount) }}</span>
            </div>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumb from '~/components/Breadcrumb'
import WeekRevenue from '~/components/charts/WeekRevenue'

export default {
  components: {
    Breadcrumb,
    WeekRevenue,
  },
  async asyncData({ $axios, $getErrorMessage, route, store }) {
    try {
      const { data } = await $axios.post('/app/dashboard/index', {
        filters: {
          companyId: store.getters['base/companyId'],
        },
      })

      if (data.success) {
        return {
          revenue: data.revenue,
          offlineLocationsCount: data.offlineLocations,
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
      revenue: null,
      offlineLocationsCount: null,
    }
  },
  computed: {
    ...mapGetters({
      companyId: 'base/companyId',
    }),
  },
  watch: {
    companyId() {
      this.getDashboardData()
    },
  },
  methods: {
    async getDashboardData() {
      try {
        const { data } = await this.$axios.post('/app/dashboard/index', {
          filters: {
            companyId: this.companyId,
          },
        })

        if (data.success) {
          this.revenue = data.revenue
          this.offlineLocationsCount = data.offlineLocations
        }
      } catch (error) {
        this.error = this.$getErrorMessage(error)
      }
    },
  },
}
</script>
