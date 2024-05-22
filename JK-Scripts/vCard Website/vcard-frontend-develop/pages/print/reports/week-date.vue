<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <b-card
          header="Week to Date Report"
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
                  <span
                    >Report Generated on
                    {{ $dayjs(generatedOn).format('MM/DD/YYYY') }} at
                    {{ $dayjs(generatedOn).format('HH:mm') }}</span
                  >

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
                    <th>Location</th>
                    <th>Address</th>
                    <th>Total In</th>
                    <th>Total Out</th>
                    <th>Revenue</th>
                    <th>Operator Profit</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Last Activity</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-if="!results || results.length === 0"
                    class="table-empty-row"
                  >
                    <td colspan="13">
                      <div class="text-center my-2">
                        No data available in table
                      </div>
                    </td>
                  </tr>

                  <tr v-for="result in results" :key="result.id">
                    <td>{{ result.name }}</td>
                    <td>{{ result.address }}</td>
                    <td>{{ $getFormattedPrice(result.totalIn) }}</td>
                    <td>{{ $getFormattedPrice(result.totalOut) }}</td>
                    <td>{{ $getFormattedPrice(result.revenue) }}</td>
                    <td>{{ $getFormattedPrice(result.profit) }}</td>
                    <td>{{ $getFormattedPrice(result.paid) }}</td>
                    <td>{{ $getFormattedPrice(result.due) }}</td>
                    <td>
                      {{
                        result.lastActivityAt
                          ? $dayjs(result.lastActivityAt).format(
                              'MM/DD/YYYY h:mm A'
                            )
                          : 'never'
                      }}
                    </td>
                  </tr>
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
import { mapGetters } from 'vuex'

import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'

export default {
  layout: 'print',
  async asyncData({ $axios, $dayjs, $getErrorMessage, route, store }) {
    try {
      const filters = parseFiltersFromQuery(route.query, [
        'query',
        'address',
        'city',
        'state',
        'zipCode',
        'locationId',
        { name: 'active', default: true },
      ])

      const { data } = await $axios.post('/reports/locations/week-to-date', {
        filters: {
          ...filters,
          companyId: store.getters['base/companyId'],
        },
        offset: 0,
        limit: -1,
        sort: route.query.sort,
        export: false,
        since: route.query.since
          ? $dayjs(route.query.since).format('YYYY-MM-DD')
          : null,
        until: route.query.until
          ? $dayjs(route.query.until).format('YYYY-MM-DD')
          : null,
      })

      if (data.success) {
        return {
          results: data.results,
          resultsCount: data.count,
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
    companyId() {
      this.getReportData()
    },
  },
  mounted() {
    if (window.parent && window.parent.postMessage) {
      window.parent.postMessage('ready-to-print')
    }
  },
  methods: {
    async getReportData() {
      try {
        const filters = parseFiltersFromQuery(this.$route.query, [
          'address',
          'city',
          'state',
          'zipCode',
          'locationId',
          { name: 'active', default: true },
        ])

        const { data } = await this.$axios.post(
          '/reports/locations/week-to-date',
          {
            filters: {
              ...filters,
              companyId: this.companyId,
            },
            offset: 0,
            limit: -1,
            sort: this.$route.query.sort,
            export: false,
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
          }
        )

        if (data.success) {
          this.results = data.results
          this.resultsCount = data.count
          this.generatedOn = this.$dayjs()
        }
      } catch (error) {
        this.error = this.$getErrorMessage(error)
      }
    },
  },
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
