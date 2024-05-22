<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">All Locations Standard Report</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          {
            title: 'All Locations Standard Report',
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
            {{ `All Locations Standard Report: ${since} — ${until}` }}
          </span>
          <div
            class="d-flex align-items-baseline text-nowrap flex-wrap flex-sm-row flex-column p-0"
            style="font-family: 'Circular Std Book', serif; color: #71748d;"
          >
            <div
              class="d-flex align-items-center mr-3 flex-sm-nowrap flex-wrap mt-2"
            >
              <span class="locations-standard__label mr-2"
                >Select a date range:</span
              >

              <DatePicker
                :value="dateRangeValue"
                :editable="false"
                :clearable="false"
                :range="true"
                format="MM/DD/YYYY"
                type="date"
                placeholder="Select a date range"
                @input="onDateRangeChanged"
              />
            </div>

            <div class="d-flex align-items-center mt-2">
              <b-input-group>
                <b-form-input
                  v-model="filters.query"
                  type="search"
                  size="sm"
                  debounce="500"
                  placeholder="Search..."
                />

                <b-input-group-append>
                  <b-input-group-text>
                    <b-icon-search font-scale="0.9" />
                  </b-input-group-text>
                </b-input-group-append>
              </b-input-group>
            </div>
          </div>
        </div>
      </template>
      <b-alert :show="typeof error === 'string'" variant="danger">
        {{ error }}
      </b-alert>

      <AdvancedFilters v-if="filters" v-model="filters" only-live-locations />

      <div
        class="d-flex align-items-baseline justify-content-between mb-3 flex-wrap"
      >
        <div class="row">
          <div class="col-12 d-flex flex-column">
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
        <b-table
          :fields="devicesTableFields"
          :items="devices"
          :sort-by="sortBy"
          :sort-desc="sortDesc"
          :pagination="{ rowsPerPage: 0 }"
          :tbody-tr-class="rowClass"
          head-variant="light"
          thead-class="text-uppercase"
          :empty-text="isLoading ? '' : 'No data available in table'"
          show-empty
          responsive
          bordered
          striped
          no-footer-sorting
          no-local-sorting
          no-sort-reset
          @sort-changed="sortingChanged"
        >
          <template #cell(cashIn)="data">
            {{ $getFormattedPrice(data.item.cashIn) }}
          </template>

          <template #cell(cashOut)="data">
            {{ $getFormattedPrice(data.item.cashOut) }}
          </template>

          <template #cell(netCash)="data">
            {{ $getFormattedPrice(data.item.netCash) }}
          </template>

          <template #cell(dailyCashIn)="data">
            {{ $getFormattedPrice(data.item.dailyCashIn) }}
          </template>

          <template #cell(dailyNet)="data">
            {{ $getFormattedPrice(data.item.dailyNet) }}
          </template>

          <template #cell(splitPercent)="data">
            {{ data.item.splitPercent ? `${data.item.splitPercent}%` : '' }}
          </template>
        </b-table>
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
import { mapGetters } from 'vuex'
import DatePicker from 'vue2-datepicker'
import { saveAs } from 'file-saver'

import Breadcrumb from '~/components/Breadcrumb'
import AdvancedFilters from '~/components/AdvancedFilters'
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    DatePicker,
    Breadcrumb,
    AdvancedFilters,
    ScrollWrapper,
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.since || !to.query.until || !to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          since: this.$dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until: this.$dayjs().utc().endOf('week').format('YYYY-MM-DD'),
          sort: 'name+asc',
        },
      })
    } else {
      next()
    }
  },
  asyncData({ $dayjs, route, redirect }) {
    if (!route.query.since || !route.query.until || !route.query.sort) {
      redirect({
        ...route,
        query: {
          ...route.query,
          since: $dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until: $dayjs().utc().endOf('week').format('YYYY-MM-DD'),
          sort: 'name+asc',
        },
      })
    }
  },
  data() {
    return {
      isLoading: false,
      timerId: null,
      error: null,
      generatedOn: this.$dayjs(),
      filters: null,
      devices: null,
      devicesCount: 0,
      devicesTableFields: [
        {
          key: 'name',
          label: 'Location',
          sortable: true,
        },
        {
          key: 'address',
          label: 'Address',
          sortable: true,
        },
        {
          key: 'city',
          label: 'City',
          sortable: true,
        },
        {
          key: 'state',
          label: 'State',
          sortable: true,
          tdClass: 'text-uppercase',
        },
        {
          key: 'zipCode',
          label: 'Zip Code',
          sortable: true,
        },
        {
          key: 'county',
          label: 'County',
          sortable: true,
        },
        {
          key: 'cashIn',
          label: 'Cash In',
          sortable: true,
        },
        {
          key: 'cashOut',
          label: 'Cash Out',
          sortable: true,
        },
        {
          key: 'netCash',
          label: 'Net Cash',
          sortable: true,
        },
        {
          key: 'devicesCount',
          label: '# Devices',
          sortable: true,
        },
        {
          key: 'dailyCashIn',
          label: 'Daily Cash In Per Device',
          sortable: true,
        },
        {
          key: 'dailyNet',
          label: 'Daily Net Per Device',
          sortable: true,
        },
        {
          key: 'splitPercent',
          label: 'Split Percent',
          sortable: true,
        },
      ],
    }
  },
  computed: {
    ...mapGetters({
      companyId: 'base/companyId',
    }),
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
    sortBy() {
      if (this.$route.query.sort.endsWith('+desc')) {
        return this.$route.query.sort.slice(0, -5)
      }
      if (this.$route.query.sort.endsWith('+asc')) {
        return this.$route.query.sort.slice(0, -4)
      }
      return this.$route.query.sort
    },
    sortDesc() {
      return this.$route.query.sort.endsWith('+desc')
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
  },
  watch: {
    '$route.query'() {
      this.getDevices()
    },
    companyId() {
      this.getDevices()
    },
    filters: {
      handler(value) {
        const newQuery = {
          ...this.$route.query,
        }

        for (const key in value) {
          if (value[key] === undefined || value[key] === null) {
            delete newQuery[key]
          } else {
            newQuery[key] = value[key]
          }
        }

        this.$router.push({
          ...this.$route,
          query: newQuery,
        })
      },
      deep: true,
    },
  },
  created() {
    this.filters = parseFiltersFromQuery(this.$route.query, [
      'query',
      'address',
      'city',
      'state',
      'zipCode',
      'locationId',
      { name: 'active', default: true },
    ])

    this.getDevices()
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
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (!item.isActive) return 'location-inactive'
    },
    async exportDevices() {
      try {
        this.$nuxt.$loading.start()

        const { data } = await this.$axios.post(
          '/reports/locations/all-locations-standard',
          {
            filters: {
              ...this.filters,
              companyId: this.companyId,
            },
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            sort: this.sortDesc ? `${this.sortBy}+desc` : `${this.sortBy}+asc`,
            export: true,
          }
        )

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'all-locations-standard.csv')
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
    async getDevices(isShowLoadingIndicator = true) {
      try {
        this.isLoading = isShowLoadingIndicator

        const requestQuery = window.location.search

        const { data } = await this.$axios.post(
          '/reports/locations/all-locations-standard',
          {
            filters: {
              ...this.filters,
              companyId: this.companyId,
            },
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            sort: this.sortDesc ? `${this.sortBy}+desc` : `${this.sortBy}+asc`,
            export: false,
          }
        )

        this.isLoading = false

        if (data.success && requestQuery === window.location.search) {
          this.generatedOn = this.$dayjs()
          this.devices = data.results
          this.devicesCount = data.count
        }

        this.$refs['scroll-wrapper'].getScrollbarWidth()
      } catch (error) {
        this.isLoading = false

        this.error = this.$getErrorMessage(error)
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
    sortingChanged(sort) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          sort: `${
            sort.sortDesc ? `${sort.sortBy}+desc` : `${sort.sortBy}+asc`
          }`,
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
        `${window.origin}${this.$router.options.base}print/reports/locations-standard${window.location.search}`
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

<style lang="scss" scoped>
.locations-standard {
  &__label {
    @media (max-width: 767px) {
      display: none;
    }
  }
}
</style>
