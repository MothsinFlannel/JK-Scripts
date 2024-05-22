<template>
  <div>
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between">
        <h3 class="mb-0">Week to Date Report</h3>
      </div>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Week to Date Report' },
        ]"
      />
    </div>

    <b-card class="mb-4 shadow-sm" header-class="pt-1">
      <template #header>
        <div
          class="d-flex align-items-baseline justify-content-between flex-wrap"
        >
          <span class="mt-2">
            {{ `Week to Date Report: ${since} — ${until}` }}
          </span>
          <div
            class="d-flex align-items-baseline text-nowrap flex-wrap flex-sm-row flex-column p-0"
            style="font-family: 'Circular Std Book', serif; color: #71748d;"
          >
            <div
              class="d-flex align-items-center mr-3 flex-sm-nowrap flex-wrap mt-2"
            >
              <span class="week-date__label mr-2">Select a date range:</span>

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
        class="d-flex align-items-center justify-content-between mb-3 flex-wrap"
      >
        <div>
          Report Generated on {{ $dayjs(generatedOn).format('MM/DD/YYYY') }} at
          {{ $dayjs(generatedOn).format('HH:mm') }}
        </div>

        <div class="d-flex align-items-center align-self-end ml-auto">
          <b-button
            v-if="locations !== null"
            type="button"
            variant="outline-light"
            class="align-self-end mr-2"
            size="sm"
            @click="exportLocations"
          >
            Export
          </b-button>
          <b-button
            v-if="locations !== null"
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
          :fields="filteredLocationTableFields"
          :items="locations"
          :sort-by="sortBy"
          :sort-desc="sortDesc"
          :pagination="{ rowsPerPage: 0 }"
          :tbody-tr-class="rowClass"
          head-variant="light"
          thead-class="text-uppercase"
          foot-variant="white"
          :empty-text="isLoading ? '' : 'No data available in table'"
          show-empty
          bordered
          striped
          foot-clone
          no-footer-sorting
          no-local-sorting
          no-sort-reset
          sticky-header="700px"
          @sort-changed="sortingChanged"
        >
          <template #cell(totalIn)="data">
            {{ $getFormattedPrice(data.item.totalIn) }}
          </template>

          <template #cell(totalOut)="data">
            {{ $getFormattedPrice(data.item.totalOut) }}
          </template>

          <template #cell(revenue)="data">
            {{ $getFormattedPrice(data.item.revenue) }}
          </template>

          <template #cell(profit)="data">
            {{ $getFormattedPrice(data.item.profit) }}
          </template>

          <template #cell(paid)="data">
            {{ $getFormattedPrice(data.item.paid) }}
          </template>

          <template #cell(due)="data">
            {{ $getFormattedPrice(data.item.due) }}
          </template>

          <template #cell(lastActivityAt)="data">
            <span v-if="data.item.lastActivityAt">
              {{ $dayjs(data.item.lastActivityAt).format('MM/DD/YYYY h:mm A') }}
            </span>

            <div
              v-if="!data.item.lastActivityAt"
              class="d-flex align-items-center"
            >
              <div
                class="mr-1 bg-danger rounded"
                style="width: 0.5rem; height: 0.5rem; margin-top: 1px;"
              ></div>
              <span>never</span>
            </div>
          </template>

          <template #foot()>
            {{ '' }}
          </template>

          <template #foot(address)>
            <div class="text-right">Total:</div>
          </template>

          <template #foot(totalIn)>
            <div class="text-left">
              {{
                locationsTotals && $getFormattedPrice(locationsTotals.totalIn)
              }}
            </div>
          </template>

          <template #foot(totalOut)>
            <div class="text-left">
              {{
                locationsTotals && $getFormattedPrice(locationsTotals.totalOut)
              }}
            </div>
          </template>

          <template #foot(revenue)>
            <div class="text-left">
              {{
                locationsTotals && $getFormattedPrice(locationsTotals.revenue)
              }}
            </div>
          </template>

          <template #foot(profit)>
            <div class="text-left">
              {{
                locationsTotals && $getFormattedPrice(locationsTotals.profit)
              }}
            </div>
          </template>

          <template #foot(paid)>
            <div class="text-left">
              {{ locationsTotals && $getFormattedPrice(locationsTotals.paid) }}
            </div>
          </template>

          <template #foot(due)>
            <div class="text-left">
              {{ locationsTotals && $getFormattedPrice(locationsTotals.due) }}
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
import DatePicker from 'vue2-datepicker'

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
          sort: 'id+asc',
        },
      })
    } else {
      next()
    }
  },
  asyncData({ $dayjs, route, redirect }) {
    if (
      !route.query.since ||
      !route.query.until ||
      !route.query.sort ||
      !route.query.active
    ) {
      redirect({
        ...route,
        query: {
          ...route.query,
          since: $dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until: $dayjs().utc().endOf('week').format('YYYY-MM-DD'),
          sort: 'id+asc',
          active: 1,
        },
      })
    }
  },
  data() {
    return {
      locationTableFields: [
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
          key: 'totalIn',
          label: 'Total In',
        },
        {
          key: 'totalOut',
          label: 'Total Out',
        },
        {
          key: 'revenue',
          label: 'Revenue',
        },
        {
          key: 'profit',
          label: 'Operator Profit',
        },
        {
          key: 'paid',
          label: 'Paid',
        },
        {
          key: 'due',
          label: 'Due',
        },
        {
          key: 'lastActivityAt',
          label: 'Last Activity',
          sortable: true,
        },
      ],
      generatedOn: this.$dayjs(),
      filters: null,
      locations: null,
      locationsTotals: null,
      locationsCount: 0,
      isLoading: false,
      timerId: null,
      error: null,
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
    filteredLocationTableFields() {
      if (this.$isAllowed('app/locations/delete')) {
        return this.locationTableFields
      } else {
        return this.locationTableFields.filter((item) => item.key !== 'actions')
      }
    },
  },
  watch: {
    '$route.query'() {
      this.getLocations()
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

    this.getLocations()
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
    async exportLocations() {
      try {
        this.$nuxt.$loading.start()

        const { data } = await this.$axios.post(
          '/reports/locations/week-to-date',
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
            sort: this.$route.query.sort,
            export: true,
          }
        )

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'week-to-date.csv')
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
    async getLocations(isShowLoadingIndicator = true) {
      try {
        this.error = null
        this.isLoading = isShowLoadingIndicator

        const requestQuery = window.location.search

        const { data } = await this.$axios.post(
          '/reports/locations/week-to-date',
          {
            filters: {
              ...this.filters,
              companyId: this.companyId,
            },
            sort: this.$route.query.sort,
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            export: false,
          }
        )

        if (data.success && requestQuery === window.location.search) {
          this.generatedOn = this.$dayjs()
          this.locations = data.results
          this.locationsTotals = data.totals
          this.locationsCount = data.count
        }

        this.isLoading = false

        this.$refs['scroll-wrapper'].getScrollbarWidth()
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
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
        `${window.origin}${this.$router.options.base}print/reports/week-date${window.location.search}`
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
.week-date {
  &__label {
    @media (max-width: 767px) {
      display: none;
    }
  }
}
</style>
