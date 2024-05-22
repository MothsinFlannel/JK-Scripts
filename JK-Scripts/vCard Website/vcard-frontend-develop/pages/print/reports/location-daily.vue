<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <b-card
          header="Location Daily Report"
          class="shadow-sm"
          style="page-break-before: always;"
          no-body
        >
          <b-list-group flush>
            <b-list-group-item>
              <b-alert :show="typeof error === 'string'" variant="danger">
                {{ error }}
              </b-alert>

              <div class="row">
                <div class="col-6 d-flex flex-column">
                  <span v-if="location.route">
                    Route: {{ location.route.name }}
                  </span>
                  <span>Location Name: {{ location.name }}</span>
                  <span>Street Address: {{ location.address }}</span>
                  <span> City, State, Zip: {{ $cityStateZip(location) }} </span>
                  <span>Phone: {{ location.contactPhone }}</span>
                  <span>Email: {{ location.operatorEmail }}</span>
                  <br />
                  <span
                    >Report Generated on
                    {{ $dayjs(generatedOn).format('MM/DD/YYYY') }} at
                    {{ $dayjs(generatedOn).format('HH:mm') }}</span
                  >
                  <br />
                  <span>Start Date: {{ since }} at 00:00</span>
                  <span>End Date: {{ until }} at 23:59</span>
                  <span># of Days: {{ daysCount }}</span>
                </div>
              </div>
            </b-list-group-item>

            <div class="table-responsive px-3 mt-3">
              <table class="table b-table table-striped table-bordered">
                <thead class="thead-light text-uppercase">
                  <tr>
                    <b-th>Date</b-th>
                    <b-th>Device ID</b-th>
                    <b-th>Game Title</b-th>
                    <b-th>Cash In</b-th>
                    <b-th>Cash Out</b-th>
                    <b-th>Net Cash</b-th>
                    <b-th>Daily Cash In Per Device</b-th>
                    <b-th>Daily Net Per Device</b-th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-if="!results || results.length === 0"
                    class="table-empty-row"
                  >
                    <td colspan="8">
                      <div class="text-center my-2">
                        No data available in table
                      </div>
                    </td>
                  </tr>

                  <template v-if="results && results.length > 0">
                    <template v-for="date in results">
                      <b-tr
                        v-for="device in date.items"
                        :key="`${device.deviceId}-${device.date}`"
                      >
                        <b-td>
                          {{ $dayjs(date.date).format('MM/DD/YYYY') }}
                        </b-td>
                        <b-td>{{ device.deviceId }}</b-td>
                        <b-td>{{ device.gameTitle }}</b-td>
                        <b-td>{{ $getFormattedPrice(device.cashIn) }}</b-td>
                        <b-td>{{ $getFormattedPrice(device.cashOut) }}</b-td>
                        <b-td>{{ $getFormattedPrice(device.netCash) }}</b-td>
                        <b-td>{{
                          $getFormattedPrice(device.dailyCashIn)
                        }}</b-td>
                        <b-td>{{ $getFormattedPrice(device.dailyNet) }}</b-td>
                      </b-tr>
                      <b-tr :key="`total-${date.date}`">
                        <b-td></b-td>
                        <b-td></b-td>
                        <b-td>Total:</b-td>
                        <b-td>{{
                          $getFormattedPrice(date.totals.cashIn)
                        }}</b-td>
                        <b-td>{{
                          $getFormattedPrice(date.totals.cashOut)
                        }}</b-td>
                        <b-td>{{
                          $getFormattedPrice(date.totals.netCash)
                        }}</b-td>
                        <b-td>{{
                          $getFormattedPrice(date.totals.dailyCashIn)
                        }}</b-td>
                        <b-td>{{
                          $getFormattedPrice(date.totals.dailyNet)
                        }}</b-td>
                      </b-tr>
                      <b-tr :key="`empty-${date.date}`">
                        <b-td colspan="9">⠀</b-td>
                      </b-tr>
                    </template>
                    <b-tr>
                      <b-td></b-td>
                      <b-td></b-td>
                      <b-td>TOTAL:</b-td>
                      <b-td>
                        {{
                          resultsTotals &&
                          $getFormattedPrice(resultsTotals.cashIn)
                        }}
                      </b-td>
                      <b-td>
                        {{
                          resultsTotals &&
                          $getFormattedPrice(resultsTotals.cashOut)
                        }}
                      </b-td>
                      <b-td>
                        {{
                          resultsTotals &&
                          $getFormattedPrice(resultsTotals.netCash)
                        }}
                      </b-td>
                      <b-td>
                        {{
                          resultsTotals &&
                          $getFormattedPrice(resultsTotals.dailyCashIn)
                        }}
                      </b-td>
                      <b-td>
                        {{
                          resultsTotals &&
                          $getFormattedPrice(resultsTotals.dailyNet)
                        }}
                      </b-td>
                    </b-tr>
                  </template>
                </tbody>
              </table>
            </div>
          </b-list-group>

          <div
            v-if="isLoading"
            class="d-flex align-items-center justify-content-center position-absolute"
            style="
              top: 0;
              right: 0;
              bottom: 0;
              left: 0;
              background-color: rgba(255, 255, 255, 0.5);
              z-index: 99;
            "
          >
            <b-spinner variant="primary" label="Loading..." />
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'print',
  async asyncData({ $axios, $dayjs, $getErrorMessage, route }) {
    try {
      const { data } = await $axios.post('/reports/locations/location-daily', {
        locationId: +route.query.location,
        since: route.query.since
          ? $dayjs(route.query.since).format('YYYY-MM-DD')
          : null,
        until: route.query.until
          ? $dayjs(route.query.until).format('YYYY-MM-DD')
          : null,
        offset: 0,
        limit: -1,
        export: false,
      })

      if (data.success) {
        return {
          results: data.results,
          resultsCount: data.count,
          resultsTotals: data.totals,
          location: data.location,
          generatedOn: $dayjs(),
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
      isLoading: false,
      error: null,
      success: null,
      generatedOn: null,
      results: null,
      resultsCount: null,
      resultsTotals: null,
      location: null,
    }
  },
  computed: {
    since() {
      if (this.$route.query.since) {
        return this.$dayjs(this.$route.query.since).format('MM/DD/YYYY')
      }

      return null
    },
    until() {
      if (this.$route.query.until) {
        return this.$dayjs(this.$route.query.until).format('MM/DD/YYYY')
      }

      return null
    },
    daysCount() {
      if (this.$route.query.since && this.$route.query.until) {
        return (
          this.$dayjs(this.$route.query.until).diff(
            this.$dayjs(this.$route.query.since),
            'day'
          ) + 1
        )
      }

      return null
    },
    dateRangeValue() {
      return [
        this.$route.query.since
          ? this.$dayjs(this.$route.query.since, 'YYYY-MM-DD').toDate()
          : null,
        this.$route.query.until
          ? this.$dayjs(this.$route.query.until, 'YYYY-MM-DD').toDate()
          : null,
      ]
    },
    locationValue() {
      if (this.$route.query.location) {
        return +this.$route.query.location
      }

      return null
    },
  },
  mounted() {
    if (window.parent && window.parent.postMessage) {
      window.parent.postMessage('ready-to-print')
    }
  },
  methods: {},
}
</script>
<style lang="scss">
@media print {
  body {
    color: black !important;
  }

  ::-webkit-scrollbar {
    width: 0;
  }
}
</style>
