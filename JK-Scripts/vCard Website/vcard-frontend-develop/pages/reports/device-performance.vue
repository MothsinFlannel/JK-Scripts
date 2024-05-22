<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Device Performance Report</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          {
            title: 'Device Performance Report',
          },
        ]"
      />
    </div>

    <b-card class="mb-4 shadow-sm" header-class="py-2">
      <template #header>
        <div
          class="d-flex align-items-baseline justify-content-between flex-wrap"
        >
          <span class="mt-2">
            {{
              devices === null
                ? 'Click the run to receive the report.'
                : `Device Performance Report: ${since} — ${until}`
            }}
          </span>
          <div
            class="d-flex align-items-baseline text-nowrap flex-wrap flex-row p-0"
            style="font-family: 'Circular Std Book', serif; color: #71748d;"
          >
            <b-button
              type="button"
              variant="outline-primary"
              class="mr-2"
              size="sm"
              @click="resetFilters"
            >
              Reset
            </b-button>

            <b-button
              type="button"
              variant="primary"
              size="sm"
              @click="getReport"
            >
              Run
            </b-button>
          </div>
        </div>
      </template>

      <b-alert :show="typeof error === 'string'" variant="danger">
        {{ error }}
      </b-alert>

      <AdvancedFilters
        v-if="filters"
        ref="advanced-filters"
        :value="filters"
        only-live-locations
        flat
        @input="applyFilters"
      />

      <div
        v-if="devices"
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
          foot-variant="white"
          :empty-text="isLoading ? '' : 'No data available in table'"
          show-empty
          responsive
          bordered
          striped
          foot-clone
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

          <template #foot()>
            {{ '' }}
          </template>

          <template #foot(gameName)>
            <div class="text-right">Total:</div>
          </template>

          <template #foot(cashIn)>
            <div class="text-left">
              {{ devicesTotals && $getFormattedPrice(devicesTotals.cashIn) }}
            </div>
          </template>

          <template #foot(cashOut)>
            <div class="text-left">
              {{ devicesTotals && $getFormattedPrice(devicesTotals.cashOut) }}
            </div>
          </template>

          <template #foot(netCash)>
            <div class="text-left">
              {{ devicesTotals && $getFormattedPrice(devicesTotals.netCash) }}
            </div>
          </template>

          <template #foot(dailyCashIn)>
            <div class="text-left">
              {{
                devicesTotals && $getFormattedPrice(devicesTotals.dailyCashIn)
              }}
            </div>
          </template>

          <template #foot(dailyNet)>
            <div class="text-left">
              {{ devicesTotals && $getFormattedPrice(devicesTotals.dailyNet) }}
            </div>
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
import { saveAs } from 'file-saver'

import Breadcrumb from '~/components/Breadcrumb'
import AdvancedFilters from '~/components/AdvancedFilters'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'
import dayjsFn from '~/plugins/dayjs.js'

const dayjs = dayjsFn()

export default {
  components: {
    Breadcrumb,
    AdvancedFilters,
    ScrollWrapper,
  },
  beforeRouteEnter(to, from, next) {
    if (!to.query.since || !to.query.until || !to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          since:
            to.query.since ||
            dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until:
            to.query.until || dayjs().utc().endOf('week').format('YYYY-MM-DD'),
          sort: to.query.sort || 'name+asc',
        },
      })
    } else {
      next()
    }
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.since || !to.query.until || !to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          since:
            to.query.since ||
            this.$dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until:
            to.query.until ||
            this.$dayjs().utc().endOf('week').format('YYYY-MM-DD'),
          sort: to.query.sort || 'name+asc',
        },
      })
    } else {
      next()
    }
  },
  data() {
    return {
      isLoading: false,
      error: null,
      generatedOn: null,
      filters: parseFiltersFromQuery(this.$route.query, [
        'since',
        'until',
        'query',
        'city',
        'state',
        'zipCode',
        'locationId',
        'cabinetTypeId',
        'programName',
      ]),
      devices: null,
      devicesCount: 0,
      devicesTableFields: [
        {
          key: 'name',
          label: 'Location',
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
          key: 'number',
          label: 'Device ID',
          sortable: true,
        },
        {
          key: 'cabinet',
          label: 'Cabinet',
          sortable: true,
        },
        {
          key: 'programName',
          label: 'Game Title',
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
          key: 'dailyCashIn',
          label: 'Daily Cash In Per Device',
          sortable: true,
        },
        {
          key: 'dailyNet',
          label: 'Daily Net Per Device',
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
    const isFiltersEmpty = this.$checkFilters(this.filters, ['since', 'until'])

    if (!isFiltersEmpty) {
      this.getDevices()
    }
  },
  mounted() {
    window.addEventListener('message', this.onReadyToPrint, false)
  },
  beforeDestroy() {
    window.removeEventListener('message', this.onReadyToPrint, false)
  },
  methods: {
    resetFilters() {
      this.$refs['advanced-filters'].reset()
    },
    getReport() {
      this.$refs['advanced-filters'].submit()
    },
    applyFilters(filters) {
      const isFiltersEmpty = this.$checkFilters(filters, ['since', 'until'])

      if (isFiltersEmpty) {
        this.error = 'You must apply at least one filter from the list below'
        this.devices = null
      } else {
        this.error = null
        this.filters = filters
      }
    },
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (!item.isActive) return 'location-inactive'
    },
    async exportDevices() {
      try {
        this.$nuxt.$loading.start()

        const { data } = await this.$axios.post(
          '/reports/locations/device-performance',
          {
            filters: {
              ...this.filters,
              active: 1,
              companyId: this.companyId,
            },
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            sort: this.$route.query.sort,
            export: true,
          }
        )

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'device-performance.csv')
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
          '/reports/locations/device-performance',
          {
            filters: {
              ...this.filters,
              active: 1,
              companyId: this.companyId,
            },
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            sort: this.$route.query.sort,
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
        `${window.origin}${this.$router.options.base}print/reports/device-performance${window.location.search}`
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
