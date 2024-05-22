<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Machine Top Bottom Earners Report</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          {
            title: 'Machine Top Bottom Earners Report',
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
                : `Machine Top Bottom Earners Report: ${since} — ${until}`
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
          <template #cell(fromDate)>
            {{ since }}
          </template>

          <template #cell(toDate)>
            {{ until }}
          </template>

          <template #cell(moneyIn)="data">
            {{ $getFormattedPrice(data.item.moneyIn) }}
          </template>

          <template #cell(revenue)="data">
            {{ $getFormattedPrice(data.item.revenue) }}
          </template>

          <template #cell(revenuePerDay)="data">
            {{ $getFormattedPrice(data.item.revenuePerDay) }}
          </template>

          <template #cell(grossIncome)="data">
            {{ $getFormattedPrice(data.item.grossIncome) }}
          </template>

          <template #cell(net)="data">
            {{ $getFormattedPrice(data.item.net) }}
          </template>

          <template #cell(netPerDay)="data">
            {{ $getFormattedPrice(data.item.netPerDay) }}
          </template>

          <template #cell(days)>
            {{ daysCount }}
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
import { saveAs } from 'file-saver'

import dayjsFn from '~/plugins/dayjs.js'
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'
import Breadcrumb from '~/components/Breadcrumb'
import AdvancedFilters from '~/components/AdvancedFilters'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

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
          sort: to.query.sort || 'number+asc',
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
          sort: to.query.sort || 'number+asc',
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
        'locationId',
        'programName',
        'number',
        'city',
        'state',
        'zipCode',
        'county',
        { name: 'active', default: null },
      ]),
      devices: null,
      devicesCount: 0,
      devicesTableFields: [
        {
          key: 'programName',
          label: 'Game Title',
          sortable: true,
        },
        {
          key: 'number',
          label: 'Terminal #',
          sortable: true,
        },
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
          key: 'fromDate',
          label: 'From Date',
        },
        {
          key: 'toDate',
          label: 'To Date',
        },
        {
          key: 'grossIncome',
          label: 'Gross Income',
          sortable: true,
        },
        {
          key: 'net',
          label: 'Net Income',
          sortable: true,
        },
        {
          key: 'netPerDay',
          label: 'Net Per Day',
          sortable: true,
        },
        {
          key: 'days',
          label: 'Days',
        },
      ],
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
    sortBy() {
      if (this.$route.query.sort.endsWith('+desc')) {
        return this.$route.query.sort.slice(0, -5)
      } else if (this.$route.query.sort.endsWith('+asc')) {
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
  },
  watch: {
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
    '$route.query'() {
      this.getDevices()
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
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (!item.isActive) return 'location-inactive'
    },
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
    async exportDevices() {
      try {
        this.$nuxt.$loading.start()

        const { data } = await this.$axios.post('/reports/inventory/earners', {
          filters: this.filters,
          since: this.$route.query.since
            ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
            : null,
          until: this.$route.query.until
            ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
            : null,
          sort: this.$route.query.sort,
          export: true,
        })

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'machine-top-bottom-earners.csv')
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

        const { data } = await this.$axios.post('/reports/inventory/earners', {
          filters: this.filters,
          since: this.$route.query.since
            ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
            : null,
          until: this.$route.query.until
            ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
            : null,
          sort: this.$route.query.sort,
          export: false,
        })

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
        `${window.origin}${this.$router.options.base}print/reports/machine-top-bottom-earners${window.location.search}`
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
