<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Location Daily Report</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          {
            title: 'Location Daily Report',
          },
        ]"
      />
    </div>

    <b-card class="mb-4 shadow-sm" header-class="pt-1">
      <template #header>
        <div
          class="d-flex align-items-baseline justify-content-between flex-wrap"
        >
          <span class="mt-2">
            {{ `Location Daily Report: ${since} — ${until}` }}
          </span>
          <div
            class="d-flex align-items-baseline text-nowrap flex-wrap flex-sm-row flex-column p-0"
            style="font-family: 'Circular Std Book', serif; color: #71748d;"
          >
            <OldLocationSelector only-live-locations class="mt-2 mr-3" />

            <div
              class="d-flex align-items-center mr-3 flex-sm-nowrap flex-wrap mt-2"
            >
              <span class="mr-2">
                Select a date range:
              </span>

              <DatePicker
                :value="dateRangeValue"
                :editable="false"
                :clearable="false"
                :range="true"
                format="MM/DD/YYYY"
                type="date"
                placeholder="Select a date range"
                style="width: 210px;"
                @input="onDateRangeChanged"
              />
            </div>
          </div>
        </div>
      </template>
      <b-alert :show="typeof error === 'string'" variant="danger">
        {{ error }}
      </b-alert>

      <b-alert :show="!$route.query.location" variant="info" class="mb-3">
        Select the location to receive the report.
      </b-alert>

      <div
        class="d-flex align-items-baseline justify-content-between mb-3 flex-wrap"
      >
        <div v-if="location" class="row">
          <div class="col-12 d-flex flex-column">
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
        <div class="d-flex align-items-center align-self-end ml-auto">
          <b-button
            v-if="devices !== null"
            type="button"
            variant="outline-light"
            class="align-self-end mr-2"
            size="sm"
            @click="exportDevices"
          >
            Export
          </b-button>
          <b-button
            v-if="devices !== null"
            type="button"
            variant="outline-light"
            size="sm"
            @click="printReport"
          >
            Print
          </b-button>
        </div>
      </div>
      <scroll-wrapper ref="scroll-wrapper">
        <b-table-simple responsive bordered striped>
          <b-thead head-variant="light" class="text-uppercase">
            <b-tr>
              <b-th>Date</b-th>
              <b-th>Device ID</b-th>
              <b-th>Game Title</b-th>
              <b-th>Cash In</b-th>
              <b-th>Cash Out</b-th>
              <b-th>Net Cash</b-th>
              <b-th>Daily Cash In Per Device</b-th>
              <b-th>Daily Net Per Device</b-th>
            </b-tr>
          </b-thead>

          <b-tbody>
            <b-tr
              v-if="!devices || (devices && devices.length === 0)"
              class="b-table-empty-row"
            >
              <b-td colspan="9">
                <div class="text-center my-2">
                  No data available in table
                </div>
              </b-td>
            </b-tr>
            <template v-if="devices && devices.length > 0">
              <template v-for="date in devices">
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
                  <b-td>{{ $getFormattedPrice(device.dailyCashIn) }}</b-td>
                  <b-td>{{ $getFormattedPrice(device.dailyNet) }}</b-td>
                </b-tr>
                <b-tr :key="`total-${date.date}`">
                  <b-td></b-td>
                  <b-td></b-td>
                  <b-td>Total:</b-td>
                  <b-td>{{ $getFormattedPrice(date.totals.cashIn) }}</b-td>
                  <b-td>{{ $getFormattedPrice(date.totals.cashOut) }}</b-td>
                  <b-td>{{ $getFormattedPrice(date.totals.netCash) }}</b-td>
                  <b-td>{{ $getFormattedPrice(date.totals.dailyCashIn) }}</b-td>
                  <b-td>{{ $getFormattedPrice(date.totals.dailyNet) }}</b-td>
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
                    devicesTotals && $getFormattedPrice(devicesTotals.cashIn)
                  }}
                </b-td>
                <b-td>
                  {{
                    devicesTotals && $getFormattedPrice(devicesTotals.cashOut)
                  }}
                </b-td>
                <b-td>
                  {{
                    devicesTotals && $getFormattedPrice(devicesTotals.netCash)
                  }}
                </b-td>
                <b-td>
                  {{
                    devicesTotals &&
                    $getFormattedPrice(devicesTotals.dailyCashIn)
                  }}
                </b-td>
                <b-td>
                  {{
                    devicesTotals && $getFormattedPrice(devicesTotals.dailyNet)
                  }}
                </b-td>
              </b-tr>
            </template>
          </b-tbody>
        </b-table-simple>
      </scroll-wrapper>

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
</template>

<script>
import DatePicker from 'vue2-datepicker'
import { saveAs } from 'file-saver'

import Breadcrumb from '~/components/Breadcrumb'
import OldLocationSelector from '~/components/selectors/OldLocationSelector'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    DatePicker,
    Breadcrumb,
    OldLocationSelector,
    ScrollWrapper,
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.since || !to.query.until) {
      next({
        ...to,
        query: {
          ...to.query,
          since: this.$dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until: this.$dayjs().utc().endOf('week').format('YYYY-MM-DD'),
        },
      })
    } else {
      next()
    }
  },
  async asyncData({ $axios, $dayjs, $getErrorMessage, route, redirect }) {
    if (!route.query.since || !route.query.until) {
      redirect({
        ...route,
        query: {
          ...route.query,
          since: $dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until: $dayjs().utc().endOf('week').format('YYYY-MM-DD'),
        },
      })
    } else {
      try {
        const result = {}

        if (route.query.location) {
          const location = await $axios.post(
            '/reports/locations/location-daily',
            {
              locationId: +route.query.location,
              since: route.query.since
                ? $dayjs(route.query.since).format('YYYY-MM-DD')
                : null,
              until: route.query.until
                ? $dayjs(route.query.until).format('YYYY-MM-DD')
                : null,
              export: false,
            }
          )

          if (location.data.success) {
            result.location = location.data.location
            result.devices = location.data.results
            result.devicesCount = location.data.count
            result.devicesTotals = location.data.totals
          }
        }

        return result
      } catch (error) {
        return {
          error: $getErrorMessage(error),
        }
      }
    }
  },
  data() {
    return {
      isLoading: false,
      timerId: null,
      error: null,
      generatedOn: this.$dayjs(),
      location: null,
      devices: null,
      devicesTotals: null,
      devicesCount: 0,
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
  watch: {
    '$route.query'() {
      this.getDevices()
    },
  },
  mounted() {
    if (typeof this.timerId === 'number') {
      window.clearInterval(this.timerId)
    }

    this.timerId = window.setInterval(() => {
      this.getDevices(false)
    }, 15000)

    window.addEventListener('message', this.onReadyToPrint, false)
  },
  beforeDestroy() {
    if (typeof this.timerId === 'number') {
      window.clearInterval(this.timerId)
    }

    window.removeEventListener('message', this.onReadyToPrint, false)
  },
  methods: {
    async exportDevices() {
      try {
        this.$nuxt.$loading.start()

        const { data } = await this.$axios.post(
          '/reports/locations/location-daily',
          {
            locationId: +this.$route.query.location,
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            export: true,
          }
        )

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'location-daily.csv')
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
    async getDevices(isShowLoadingIndicator = true) {
      if (this.$route.query.location) {
        try {
          this.isLoading = isShowLoadingIndicator

          const requestQuery = window.location.search

          const { data } = await this.$axios.post(
            '/reports/locations/location-daily',
            {
              locationId: +this.$route.query.location,
              since: this.$route.query.since
                ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
                : null,
              until: this.$route.query.until
                ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
                : null,
              export: false,
            }
          )

          this.isLoading = false

          if (data.success && requestQuery === window.location.search) {
            this.generatedOn = this.$dayjs()
            this.location = data.location
            this.devices = data.results
            this.devicesCount = data.count
            this.devicesTotals = data.totals
          }

          this.$refs['scroll-wrapper'].getScrollbarWidth()
        } catch (error) {
          this.isLoading = false

          this.error = this.$getErrorMessage(error)
        }
      } else {
        this.location = null
        this.devices = null
        this.devicesCount = null
        this.devicesTotals = null
      }
    },
    onDateRangeChanged(dates) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          since: dates[0] ? this.$dayjs(dates[0]).format('YYYY-MM-DD') : null,
          until: dates[1] ? this.$dayjs(dates[1]).format('YYYY-MM-DD') : null,
        },
      })
    },
    onLocationChanged(location) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          location,
        },
      })
    },
    onReadyToPrint(event) {
      if (event.data === 'ready-to-print') {
        this.$nuxt.$loading.finish()

        try {
          this.printIframe.contentWindow.document.execCommand(
            'print',
            true,
            null
          )
        } catch {
          this.printIframe.contentWindow.print()
        }
      }
    },
    printReport() {
      if (this.printIframe) {
        window.document.body.removeChild(this.printIframe)
        this.printIframe = null
      }

      this.$nuxt.$loading.start()

      this.printIframe = document.createElement('iframe')

      this.printIframe.setAttribute(
        'src',
        `${window.origin}${this.$router.options.base}print/reports/location-daily${window.location.search}`
      )
      this.printIframe.setAttribute(
        'style',
        'visibility: hidden; position: absolute; left: 0; top: 0; height: 0; width: 0; border: none;'
      )

      window.document.body.appendChild(this.printIframe)
    },
  },
}
</script>
