<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <b-card
          header="Inventory Details Report"
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
                </div>
              </div>
            </b-list-group-item>

            <div class="table-responsive px-3 mt-3">
              <table
                class="table b-table table-striped table-bordered table__shrink"
              >
                <thead class="thead-light text-uppercase">
                  <tr>
                    <th>Location</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip Code</th>
                    <th>County</th>
                    <th>Game Title</th>
                    <th>Machine ID</th>
                    <th>Cabinet Asset #</th>
                    <th>Board Asset #</th>
                    <th>Terminal ID</th>
                    <th>License #</th>
                    <th>Machine Type</th>
                    <th>Cabinet</th>
                    <th>Status</th>
                    <th>Cost</th>
                    <th>Acquisition Date</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-if="!results || results.length === 0"
                    class="table-empty-row"
                  >
                    <td colspan="14">
                      <div class="text-center my-2">
                        No data available in table
                      </div>
                    </td>
                  </tr>

                  <tr v-for="result in results" :key="result.id">
                    <td>{{ result.locationName }}</td>
                    <td>{{ result.city }}</td>
                    <td>{{ result.state }}</td>
                    <td>{{ result.zipCode }}</td>
                    <td>{{ result.county }}</td>
                    <td>{{ result.gameTitle }}</td>
                    <td>{{ result.machineId }}</td>
                    <td>{{ result.cabinetAssetNumber }}</td>
                    <td>{{ result.boardAssetNumber }}</td>
                    <td>{{ result.terminalId }}</td>
                    <td>{{ result.licenseNumber }}</td>
                    <td>{{ result.machineType }}</td>
                    <td>{{ result.cabinetType }}</td>
                    <td>{{ result.status }}</td>
                    <td>{{ result.cost }}</td>
                    <td>
                      {{
                        result.acquisitionDate &&
                        $dayjs(result.acquisitionDate).format('MM/DD/YYYY')
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
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'

export default {
  layout: 'print',
  async asyncData({ $axios, $dayjs, $getErrorMessage, route }) {
    try {
      const filters = parseFiltersFromQuery(route.query, [
        'query',
        'locationId',
        'machineTypeId',
        'licenseNumber',
        { name: 'active', default: true },
      ])

      const { data } = await $axios.post('/reports/inventory/details', {
        filters,
        sort: route.query.sort,
        offset: 0,
        limit: -1,
        export: false,
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

  .table__shrink {
    th,
    td {
      font-size: 0.75em;
    }
  }
}
</style>
