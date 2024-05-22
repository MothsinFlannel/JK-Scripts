<template>
  <div>
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between">
        <h3 class="mb-0">POS Audit</h3>
      </div>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'POS Audit' },
        ]"
      />
    </div>

    <b-card class="mb-4 shadow-sm" header-class="pt-1">
      <template #header>
        <div
          class="d-flex align-items-baseline justify-content-between flex-wrap"
        >
          <span class="mt-2">
            POS Audit
          </span>
          <div
            class="d-flex align-items-baseline text-nowrap flex-wrap flex-sm-row flex-column p-0"
            style="font-family: 'Circular Std Book', serif; color: #71748d;"
          >
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

      <div
        class="d-flex align-items-center justify-content-between mb-3 flex-wrap"
      >
        <div>
          Report Generated on {{ $dayjs(generatedOn).format('MM/DD/YYYY') }} at
          {{ $dayjs(generatedOn).format('HH:mm') }}
        </div>
      </div>

      <scroll-wrapper ref="scroll-wrapper">
        <b-table
          :fields="tableFields"
          :items="result"
          :pagination="{ rowsPerPage: 0 }"
          table-class="parent-table"
          head-variant="light"
          thead-class="text-uppercase"
          foot-variant="white"
          :empty-text="isLoading ? '' : 'No data available in table'"
          show-empty
          responsive
          bordered
          no-footer-sorting
          no-local-sorting
          no-sort-reset
        >
          <template #cell(logs)="data">
            <b-table
              class="mb-0"
              table-class="additional-table"
              :fields="logFields"
              :items="data.item.logs"
              thead-class="hidden"
              :empty-text="isLoading ? '' : 'No data available in table'"
              show-empty
              small
              responsive
            >
              <template #cell(location)="data">
                <span v-if="data.item.location">
                  {{ data.item.location.name }}
                </span>
              </template>

              <template #cell(address)="data">
                <span v-if="data.item.location">
                  {{ data.item.location.state }}, {{ data.item.location.city }},
                  {{ data.item.location.address }}
                </span>
              </template>

              <template #cell(createdAt)="data">
                {{ $dayjs(data.item.createdAt).format('MM/DD/YYYY h:mm A') }}
              </template>
            </b-table>
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
import Breadcrumb from '~/components/Breadcrumb'
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    Breadcrumb,
    ScrollWrapper,
  },
  data() {
    return {
      generatedOn: this.$dayjs(),
      logFields: [
        {
          key: 'location',
          label: 'location',
        },
        {
          key: 'address',
          label: 'address',
        },
        {
          key: 'createdAt',
          label: 'datetime',
        },
      ],
      tableFields: [
        {
          key: 'device',
          label: 'POS #',
        },
        {
          key: 'logs',
          label: 'Logs',
        },
      ],
      filters: null,
      result: null,
      resultTotals: null,
      resultCount: 0,
      isLoading: false,
      timerId: null,
      error: null,
    }
  },
  watch: {
    '$route.query'() {
      this.getReport()
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
    this.filters = parseFiltersFromQuery(this.$route.query, ['query'])

    this.getReport()
  },
  methods: {
    async getReport(isShowLoadingIndicator = true) {
      try {
        this.error = null
        this.isLoading = isShowLoadingIndicator

        const requestQuery = window.location.search

        const { data } = await this.$axios.post(
          '/reports/inventory/pos-audit',
          {
            filters: {
              ...this.filters,
            },
          }
        )

        if (data.success && requestQuery === window.location.search) {
          this.generatedOn = this.$dayjs()
          this.result = data.results
        }

        this.isLoading = false

        this.$refs['scroll-wrapper'].getScrollbarWidth()
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
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
