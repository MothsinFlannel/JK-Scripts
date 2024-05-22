<template>
  <div>
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between">
        <h3 class="mb-0">Remains</h3>
      </div>

      <hr class="my-2" />

      <Breadcrumb
        :items="[{ title: 'vCard Dashboard', link: '/' }, { title: 'Remains' }]"
      />
    </div>

    <b-card class="mb-4 shadow-sm" header-class="pt-1">
      <template #header>
        <div
          class="d-flex align-items-baseline justify-content-between flex-wrap"
        >
          <span class="mt-2">{{ `Remains: ${since} — ${until}` }}</span>
          <div
            class="d-flex align-items-baseline text-nowrap flex-wrap flex-sm-row flex-column p-0"
            style="font-family: 'Circular Std Book', serif; color: #71748d;"
          >
            <div
              class="d-flex align-items-center mr-3 flex-sm-nowrap flex-wrap mt-2"
            >
              <span class="week-date__label mr-2">Select a week:</span>

              <DatePicker
                :value="dateRangeValue"
                :editable="false"
                :clearable="true"
                :range="false"
                type="week"
                :formatter="formatDate"
                :placeholder="'Select a week'"
                :show-week-number="false"
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

      <AdvancedFilters v-if="filters" v-model="filters" />

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
          :pagination="{ rowsPerPage: 0 }"
          :tbody-tr-class="rowClass"
          head-variant="light"
          thead-class="text-uppercase"
          :empty-text="isLoading ? '' : 'No data available in table'"
          show-empty
          responsive
          bordered
          striped
        >
          <template #cell(number)="data"> #{{ data.item.number }} </template>

          <template #cell(revenueRemain)="data">
            {{ $getFormattedPrice(data.item.revenueRemain) }}
          </template>

          <template #cell(balanceRemain)="data">
            {{ $getFormattedPrice(data.item.balanceRemain) }}
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
  asyncData({ $dayjs, route, redirect }) {
    if (!route.query.since || !route.query.until || !route.query.active) {
      redirect({
        ...route,
        query: {
          ...route.query,
          since: $dayjs().day(1).format('YYYY-MM-DD'),
          until: $dayjs().day(1).add(6, 'day').format('YYYY-MM-DD'),
          sort: null,
          active: 1,
        },
      })
    }
  },
  data() {
    return {
      formatDate: {
        stringify: (date) => {
          return date
            ? `${this.$dayjs(date).format('MM/DD/YYYY')} ~ ${this.$dayjs(date)
                .add(6, 'day')
                .format('MM/DD/YYYY')}`
            : null
        },
      },
      locationTableFields: [
        {
          key: 'name',
          label: 'Location',
        },
        {
          key: 'address',
          label: 'Address',
        },
        {
          key: 'city',
          label: 'City',
        },
        {
          key: 'state',
          label: 'State',
          tdClass: 'text-uppercase',
        },
        {
          key: 'zipCode',
          label: 'Zip Code',
        },
        {
          key: 'county',
          label: 'County',
        },
        {
          key: 'number',
          label: 'Terminal #',
        },
        {
          key: 'title',
          label: 'Program Name',
        },
        {
          key: 'revenueRemain',
          label: 'Revenue Remain',
        },
        {
          key: 'balanceRemain',
          label: 'Balance Remain',
        },
      ],
      generatedOn: this.$dayjs(),
      filters: null,
      locations: null,
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
    dateRangeValue() {
      return this.$route.query.since
        ? this.$dayjs(this.$route.query.since, 'YYYY-MM-DD').toDate()
        : null
    },
    until() {
      if (this.$route.query.until) {
        return this.$dayjs(this.$route.query.until).format('MM/DD/YYYY')
      }

      return null
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
      'companyId',
      'city',
      'state',
      'zipCode',
      { name: 'active', default: true },
    ])

    this.getLocations()
  },
  mounted() {
    window.addEventListener('message', this.onReadyToPrint, false)
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.since || !to.query.until) {
      next({
        ...to,
        query: {
          ...to.query,
          since: this.$dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until: this.$dayjs().utc().endOf('week').format('YYYY-MM-DD'),
          sort: null,
        },
      })
    } else {
      next()
    }
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

        const { data } = await this.$axios.post('/reports/locations/remains', {
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
        })
        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'remains.csv')
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

        const { data } = await this.$axios.post('/reports/locations/remains', {
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
        })

        if (data.success && requestQuery === window.location.search) {
          this.generatedOn = this.$dayjs()
          this.locations = data.results
        }

        this.isLoading = false

        this.$refs['scroll-wrapper'].getScrollbarWidth()
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    onDateRangeChanged(date) {
      if (
        this.$dayjs(date).format('ddd') === 'Sun' ||
        this.$dayjs(date).format('ddd') === 'Mon'
      ) {
        date = this.$dayjs(date).add(2, 'day')
      }
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: '1',
          since: date
            ? this.$dayjs(date).utc().startOf('isoWeek').format('YYYY-MM-DD')
            : undefined,
          until: date
            ? this.$dayjs(date).utc().endOf('isoWeek').format('YYYY-MM-DD')
            : undefined,
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
        `${window.origin}${this.$router.options.base}print/reports/remains${window.location.search}`
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
