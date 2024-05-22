<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <span>
          All Locations
        </span>

        <div
          class="d-flex align-items-center"
          style="font-family: 'Circular Std Book', serif; color: #71748d;"
        >
          <b-form-select
            :value="$route.query.status"
            :options="[
              { value: null, text: 'All' },
              { value: 1, text: 'Active' },
              { value: 0, text: 'Inactive' },
            ]"
            size="sm"
            class="mr-sm-3 mr-0"
            @change="onStatusChanged"
          />

          <b-input-group style="min-width: 200px;">
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
    </template>

    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-alert
      :show="typeof $route.query.error === 'string'"
      variant="danger"
      dismissible
      @dismissed="
        $router.push({
          ...$route,
          query: {
            ...$route.query,
            error: undefined,
          },
        })
      "
    >
      {{ $route.query.error }}
    </b-alert>

    <AdvancedFilters
      v-if="filters"
      v-model="filters"
      multiple-games
      multiple-softwares
    />

    <div
      class="d-flex align-items-baseline justify-content-between mb-3 flex-wrap"
      style="margin-top: -16px;"
    >
      <div class="row mr-auto">
        <div class="col-12 d-flex flex-column">
          <div class="d-flex align-items-center mr-3 mb-1 mb-sm-0 mt-3">
            <div class="d-flex align-items-center">
              Show
              <b-form-select
                :value="+$route.query.limit"
                :options="[
                  { value: 10, text: '10' },
                  { value: 25, text: '25' },
                  { value: 50, text: '50' },
                  { value: 100, text: '100' },
                  { value: -1, text: 'All' },
                ]"
                size="sm"
                class="mx-1"
                @input="onLimitChanged"
              />
              entries
            </div>
          </div>

          <b-pagination
            v-if="
              $route.query.limit > 0 && locationsCount > +$route.query.limit
            "
            :value="+$route.query.page"
            :total-rows="locationsCount"
            :per-page="+$route.query.limit"
            first-text="First"
            prev-text="Previous"
            next-text="Next"
            last-text="Last"
            class="mb-0 mt-3"
            pills
            @change="onCurrentPageChanged"
          />
        </div>
      </div>

      <div
        v-if="$isAllowed('app/locations/create')"
        class="d-flex align-items-center align-self-end mt-3"
      >
        <b-button
          type="button"
          variant="outline-light"
          class="align-self-end mr-2 text-nowrap"
          size="sm"
          to="/locations/create"
        >
          New Location
        </b-button>

        <b-button
          v-if="locations"
          type="button"
          variant="outline-light"
          class="ml-2"
          size="sm"
          @click="exportLocations"
        >
          {{
            locations.filter((location) => location.selected).length === 0
              ? 'Export All'
              : `Export (${
                  locations.filter((location) => location.selected).length
                })`
          }}
        </b-button>
      </div>
    </div>

    <scroll-wrapper ref="scroll-wrapper">
      <b-table
        :fields="filteredLocationTableFields"
        :items="locations"
        :sort-by="sortBy"
        :sort-desc="sortDesc"
        :tbody-tr-class="rowClass"
        head-variant="light"
        thead-class="text-uppercase"
        foot-variant="white"
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
        <template #head(select)>
          <b-form-checkbox
            :checked="allLocationsSelected"
            :value="true"
            :unchecked-value="false"
            style="min-height: 21px;"
            @change="toggleAllItemsSelection"
          />
        </template>

        <template #cell(select)="{ item }">
          <b-form-checkbox
            v-model="item.selected"
            :value="true"
            :unchecked-value="false"
            class="mb-1"
          />
        </template>

        <template #cell(name)="data">
          <nuxt-link :to="`/locations/${data.item.id}`">
            {{ data.item.name }}
          </nuxt-link>
        </template>

        <template #cell(licenseNumber)="data">
          {{ data.item.licenseNumber }}
        </template>

        <template #cell(isLive)="data">
          {{ data.item.isLive ? 'Yes' : 'No' }}
        </template>
      </b-table>
    </scroll-wrapper>

    <div v-if="$route.query.limit > 0" class="app-card__table-footer">
      <div class="mt-3 mr-3 mb-1 mb-sm-0">
        Showing
        {{
          Math.min(
            (+$route.query.page - 1) * +$route.query.limit + 1,
            locationsCount
          )
        }}
        to
        {{ Math.min(+$route.query.page * +$route.query.limit, locationsCount) }}
        of {{ locationsCount }} entries
      </div>

      <b-pagination
        v-if="locationsCount > +$route.query.limit"
        :value="+$route.query.page"
        :total-rows="locationsCount"
        :per-page="+$route.query.limit"
        first-text="First"
        prev-text="Previous"
        next-text="Next"
        last-text="Last"
        class="mt-3 mb-0"
        pills
        @change="onCurrentPageChanged"
      />
    </div>

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
</template>

<script>
import { mapGetters } from 'vuex'
import { saveAs } from 'file-saver'

import AdvancedFilters from '~/components/AdvancedFilters'
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    AdvancedFilters,
    ScrollWrapper,
  },
  data() {
    return {
      filters: null,
      locationTableFields: [
        {
          key: 'select',
          label: '',
          tdClass: 'pt-2 pb-0 pr-1',
          thStyle: { width: '1%' },
        },
        {
          key: 'name',
          label: 'Name',
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
          key: 'contactPhone',
          label: 'Phone',
          sortable: true,
        },
        {
          key: 'operatorEmail',
          label: 'Email',
          sortable: true,
        },
        {
          key: 'licenseNumber',
          label: 'License #',
          sortable: true,
        },
        {
          key: 'isLive',
          label: 'Is Live',
          sortable: true,
        },
      ],
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
    filteredLocationTableFields() {
      if (this.$isAllowed('app/locations/delete')) {
        return this.locationTableFields
      } else {
        return this.locationTableFields.filter((item) => item.key !== 'actions')
      }
    },
    allLocationsSelected() {
      return (
        this.locations &&
        this.locations.filter((location) => !location.selected).length === 0
      )
    },
    hasSelectedLocations() {
      return this.locations.filter((location) => location.selected).length !== 0
    },
  },
  watch: {
    '$route.query'() {
      this.getLocations()
    },
    companyId() {
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
      'name',
      'address',
      'city',
      'state',
      'zipCode',
      'county',
      'gpsNumber',
      'operatorEmail',
      'programName',
      'software',
      'live',
      'onHold',
    ])

    this.getLocations()
  },
  mounted() {
    if (typeof this.timerId === 'number') {
      window.clearInterval(this.timerId)
    }

    this.timerId = window.setInterval(() => {
      this.getLocations(false)
    }, 15000)
  },
  beforeDestroy() {
    if (typeof this.timerId === 'number') {
      window.clearInterval(this.timerId)
    }
  },
  methods: {
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (!item.isActive) return 'location-inactive'
      if (!item.isLive) return 'location-test'
    },
    toggleAllItemsSelection(value) {
      this.locations = this.locations.map((location) => {
        return {
          ...location,
          selected: value,
        }
      })
    },
    async exportLocations() {
      try {
        this.$nuxt.$loading.start()

        const requestData = {}

        if (this.hasSelectedLocations) {
          requestData.filters = {
            ids: this.locations
              .filter((location) => location.selected)
              .map((location) => location.id),
          }
        } else {
          requestData.filters = {
            ...this.filters,
            active: this.$route.query.status,
            companyId: this.companyId,
          }
          requestData.sort = this.$route.query.sort
        }

        const { data } = await this.$axios.post(
          '/app/locations/export',
          requestData
        )

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'all-locations.csv')
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

        const { data } = await this.$axios.post('/app/locations/list', {
          filters: {
            ...this.filters,
            active: this.$route.query.status,
            companyId: this.companyId,
          },
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success && requestQuery === window.location.search) {
          if (isShowLoadingIndicator) {
            this.locations = data.results
          } else {
            const locations = data.results.map((location) => {
              const oldLocation = this.locations.find(
                (l) => l.id === location.id
              )

              return {
                ...location,
                selected: Boolean(oldLocation && oldLocation.selected),
              }
            })

            this.locations = locations
          }

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
    onLimitChanged(limit) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: '1',
          limit: `${limit}`,
        },
      })
    },
    onCurrentPageChanged(page) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: `${page}`,
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
    onStatusChanged(status) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          status,
        },
      })
    },
  },
}
</script>
