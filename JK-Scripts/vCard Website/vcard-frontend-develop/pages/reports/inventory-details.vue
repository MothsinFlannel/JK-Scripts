<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Inventory Details Report</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          {
            title: 'Inventory Details Report',
          },
        ]"
      />
    </div>

    <b-card class="mb-4 shadow-sm" header-class="py-2">
      <template #header>
        <div
          class="d-flex align-items-center justify-content-between flex-wrap"
        >
          <span class="mt-2">
            {{
              devices === null
                ? 'Click the run to receive the report.'
                : 'Inventory Details Report'
            }}
          </span>

          <div
            class="d-flex align-items-center"
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
        class="d-flex align-items-center justify-content-between mb-3 flex-wrap"
      >
        <div>
          Report Generated on {{ $dayjs(generatedOn).format('MM/DD/YYYY') }} at
          {{ $dayjs(generatedOn).format('HH:mm') }}
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
          <template #cell(acquisitionDate)="data">
            {{
              data.item.acquisitionDate &&
              $dayjs(data.item.acquisitionDate).format('MM/DD/YYYY')
            }}
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

import Breadcrumb from '~/components/Breadcrumb'
import AdvancedFilters from '~/components/AdvancedFilters'
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    Breadcrumb,
    AdvancedFilters,
    ScrollWrapper,
  },
  beforeRouteEnter(to, from, next) {
    if (!to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          sort: 'locationName+asc',
        },
      })
    } else {
      next()
    }
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          sort: 'locationName+asc',
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
        'query',
        'locationId',
        'machineTypeId',
        'cabinetTypeId',
        'programName',
        'licenseNumber',
        'city',
        'state',
        'zipCode',
        'county',
        'boardAssetNumber',
        'cabinetAssetNumber',
        { name: 'active', default: null },
      ]),
      devices: null,
      devicesCount: 0,
      devicesTableFields: [
        {
          key: 'locationName',
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
          key: 'programName',
          label: 'Game Title',
          sortable: true,
        },
        {
          key: 'machineId',
          label: 'Machine ID',
          sortable: true,
        },
        {
          key: 'cabinetAssetNumber',
          label: 'Cabinet Asset #',
          sortable: true,
        },
        {
          key: 'boardAssetNumber',
          label: 'Board Asset #',
          sortable: true,
        },
        {
          key: 'terminalId',
          label: 'Terminal ID',
          sortable: true,
        },
        {
          key: 'licenseNumber',
          label: 'License #',
          sortable: true,
        },
        {
          key: 'machineType',
          label: 'Machine Type',
          sortable: true,
        },
        {
          key: 'cabinetType',
          label: 'Cabinet',
          sortable: true,
        },
        {
          key: 'status',
          label: 'Status',
          sortable: true,
        },
        {
          key: 'cost',
          label: 'Cost',
          sortable: true,
        },
        {
          key: 'acquisitionDate',
          label: 'Acquisition Date',
          sortable: true,
        },
      ],
    }
  },
  computed: {
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
    const isFiltersEmpty = this.$checkFilters(this.filters)

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
      const isFiltersEmpty = this.$checkFilters(filters)

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

        const { data } = await this.$axios.post('/reports/inventory/details', {
          filters: this.filters,
          sort: this.$route.query.sort,
          export: true,
        })

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'inventory-details.csv')
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

        const { data } = await this.$axios.post('/reports/inventory/details', {
          filters: this.filters,
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
        `${window.origin}${this.$router.options.base}print/reports/inventory-details${window.location.search}`
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
