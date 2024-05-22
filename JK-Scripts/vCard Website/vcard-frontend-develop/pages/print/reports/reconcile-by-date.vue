<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <b-card
          header="Reconcile By Date Report"
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

                  <span
                    >Period Start:
                    {{
                      $dayjs($route.query.since, 'YYYY-MM-DD').format(
                        'MM/DD/YYYY'
                      )
                    }}</span
                  >

                  <span
                    >Period End:
                    {{
                      $dayjs($route.query.until, 'YYYY-MM-DD').format(
                        'MM/DD/YYYY'
                      )
                    }}</span
                  >
                </div>
              </div>
            </b-list-group-item>

            <div class="table-responsive px-3 mt-3">
              <table class="table b-table table-striped table-bordered">
                <thead class="thead-light text-uppercase">
                  <tr>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Cash In</th>
                    <th>Cash Out</th>
                    <th>Net</th>
                    <th>Location Split</th>
                    <th>Operator Split</th>
                    <th>Amount Due</th>
                    <th>Amount Paid</th>
                    <th>Balance Due</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-if="!results || results.length === 0"
                    class="table-empty-row"
                  >
                    <td colspan="10">
                      <div class="text-center my-2">
                        No data available in table
                      </div>
                    </td>
                  </tr>

                  <tr
                    v-for="result in results"
                    :key="`${result.locationId}-${result.date}`"
                  >
                    <td>
                      <span class="text-nowrap">{{ result.date }}</span>
                    </td>
                    <td>{{ result.name }}</td>
                    <td>{{ $getFormattedPrice(result.cashIn) }}</td>
                    <td>{{ $getFormattedPrice(result.cashOut) }}</td>
                    <td>{{ $getFormattedPrice(result.net) }}</td>
                    <td>{{ $getFormattedPrice(result.locationSplit) }}</td>
                    <td>{{ $getFormattedPrice(result.operatorSplit) }}</td>
                    <td>{{ $getFormattedPrice(result.amountDue) }}</td>
                    <td>{{ $getFormattedPrice(result.amountPaid) }}</td>
                    <td>{{ $getFormattedPrice(result.balanceDue) }}</td>
                  </tr>
                </tbody>

                <tfoot class="thead-white">
                  <tr>
                    <th class="text-right">Total:</th>
                    <th></th>
                    <th>
                      {{
                        resultsTotals &&
                        $getFormattedPrice(resultsTotals.cashIn)
                      }}
                    </th>
                    <th>
                      {{
                        resultsTotals &&
                        $getFormattedPrice(resultsTotals.cashOut)
                      }}
                    </th>
                    <th>
                      {{
                        resultsTotals && $getFormattedPrice(resultsTotals.net)
                      }}
                    </th>
                    <th>
                      {{
                        resultsTotals &&
                        $getFormattedPrice(resultsTotals.locationSplit)
                      }}
                    </th>
                    <th>
                      {{
                        resultsTotals &&
                        $getFormattedPrice(resultsTotals.operatorSplit)
                      }}
                    </th>
                    <th>
                      {{
                        resultsTotals &&
                        $getFormattedPrice(resultsTotals.amountDue)
                      }}
                    </th>
                    <th>
                      {{
                        resultsTotals &&
                        $getFormattedPrice(resultsTotals.amountPaid)
                      }}
                    </th>
                    <th>
                      {{
                        resultsTotals &&
                        $getFormattedPrice(resultsTotals.balanceDue)
                      }}
                    </th>
                  </tr>
                </tfoot>
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
export default {
  layout: 'print',
  async asyncData({ $axios, $dayjs, $getErrorMessage, route }) {
    try {
      const { data } = await $axios.post('/reports/inventory/reconcile', {
        filters: {
          locationId: route.query.location ? +route.query.location : null,
        },
        since: route.query.since
          ? $dayjs(route.query.since).format('YYYY-MM-DD')
          : null,
        until: route.query.until
          ? $dayjs(route.query.until).format('YYYY-MM-DD')
          : null,
        offset: 0,
        limit: -1,
        export: false,
      })

      if (data.success) {
        return {
          results: data.results,
          resultsCount: data.count,
          resultsTotals: data.totals,
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
  mounted() {
    if (window.parent && window.parent.postMessage) {
      window.parent.postMessage('ready-to-print')
    }
  },
  methods: {},
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
