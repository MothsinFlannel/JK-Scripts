<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Warehouse '{{ warehouse.name }}'</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Warehouses', link: '/warehouses' },
          {
            title: `${warehouse.name}`,
          },
        ]"
      />
    </div>

    <div class="row">
      <div class="col-12">
        <b-card class="mb-4 shadow-sm">
          <template #header>
            <div
              class="d-flex align-items-baseline justify-content-between flex-wrap"
            >
              <p class="m-0">vCard Warehouse Terminals</p>
              <div
                class="d-flex align-items-center text-nowrap"
                style="font-family: 'Circular Std Book', serif; color: #71748d;"
              >
                <div class="input-group">
                  <b-form-input
                    :value="$route.query.query"
                    type="search"
                    size="sm"
                    class="form-control rounded-0 border-right-0"
                    placeholder="Search"
                    debounce="500"
                    @update="onSearch"
                  />
                  <div class="input-group-append">
                    <div class="input-group-text rounded-0">
                      <svg
                        width="1em"
                        height="1.0625rem"
                        viewBox="0 0 16 16"
                        class="bi bi-search"
                        fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"
                        />
                        <path
                          fill-rule="evenodd"
                          d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"
                        />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
          <b-pagination
            v-if="terminalsCount > +$route.query.limit"
            :value="+$route.query.page"
            :total-rows="terminalsCount"
            :per-page="+$route.query.limit"
            first-text="First"
            prev-text="Previous"
            next-text="Next"
            last-text="Last"
            pills
            @change="onCurrentPageChanged"
          />
          <b-table
            :fields="filteredTerminalTableFields"
            :items="terminals"
            :sort-by="sortBy"
            :sort-desc="sortDesc"
            head-variant="light"
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
            <template #cell(number)="data">#{{ data.item.number }}</template>

            <template #cell(archivedAt)="data">
              <span v-if="data.item.archivedAt">
                {{ $dayjs(data.item.archivedAt).format('MM/DD/YYYY h:mm A') }}
              </span>
            </template>

            <template #cell(actions)="data">
              <div
                class="d-flex flex-nowrap align-items-center"
                style="margin: -0.25rem;"
              >
                <b-button
                  v-if="$isAllowed('app/terminals/movements')"
                  type="button"
                  variant="outline-light"
                  size="sm"
                  class="mb-2 mr-2"
                  @click="viewHistory(data.item)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="1em"
                    height="21px"
                    fill="currentColor"
                    class="bi bi-card-list"
                    viewBox="0 0 16 16"
                  >
                    <path
                      d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"
                    />
                    <path
                      d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"
                    />
                  </svg>
                </b-button>

                <b-button
                  v-if="$isAllowed('app/terminals/update')"
                  type="button"
                  variant="outline-light"
                  size="sm"
                  class="mb-2"
                  @click="outService(data.item)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="1em"
                    height="21px"
                    fill="currentColor"
                    class="bi bi-arrow-bar-right"
                    viewBox="0 0 16 16"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6 8a.5.5 0 0 0 .5.5h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L12.293 7.5H6.5A.5.5 0 0 0 6 8zm-2.5 7a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5z"
                    />
                  </svg>
                </b-button>
              </div>
            </template>
          </b-table>

          <div
            class="d-flex align-items-center justify-content-sm-between justify-content-center flex-sm-row flex-column"
          >
            <div class="mr-3">
              Showing
              {{
                Math.min(
                  (+$route.query.page - 1) * +$route.query.limit + 1,
                  terminalsCount
                )
              }}
              to
              {{
                Math.min(
                  +$route.query.page * +$route.query.limit,
                  terminalsCount
                )
              }}
              of {{ terminalsCount }} entries
            </div>

            <b-pagination
              v-if="terminalsCount > +$route.query.limit"
              :value="+$route.query.page"
              :total-rows="terminalsCount"
              :per-page="+$route.query.limit"
              first-text="First"
              prev-text="Previous"
              next-text="Next"
              last-text="Last"
              class="mb-0"
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
      </div>
    </div>
    <OutWarehouseModal
      ref="outWarehouse-modal"
      @updated="getTerminals"
    ></OutWarehouseModal>
    <MovementHistoryModal ref="movementHistory-modal"></MovementHistoryModal>
  </div>
</template>

<script>
import OutWarehouseModal from '@/components/modals/OutWarehouse'
import MovementHistoryModal from '@/components/modals/MovementHistory'
import Breadcrumb from '~/components/Breadcrumb'
export default {
  components: {
    Breadcrumb,
    OutWarehouseModal,
    MovementHistoryModal,
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.page || !to.query.limit || !to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          page: '1',
          limit: '50',
          sort: 'number+asc',
        },
      })
    } else {
      next()
    }
  },
  async asyncData({ $axios, $getErrorMessage, route, redirect }) {
    if (!route.query.page || !route.query.limit || !route.query.sort) {
      redirect({
        ...route,
        query: {
          ...route.query,
          page: '1',
          limit: '50',
          sort: 'number+asc',
        },
      })
    }

    try {
      const { data } = await $axios.post('/app/warehouses/get', {
        id: +route.params.id,
      })
      const terminals = await $axios.post('/app/terminals/list', {
        filters: {
          query: route.query.query,
          warehouseId: +route.params.id,
        },
        offset: (route.query.page - 1) * +route.query.limit,
        limit: +route.query.limit,
        sort: route.query.sort,
      })
      if (data.success && terminals.data.success) {
        return {
          warehouse: data.warehouse,
          terminals: terminals.data.results,
          terminalsCount: terminals.data.count,
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
      warehouse: null,
      terminalTableFields: [
        {
          key: 'programName',
          label: 'Game',
          sortable: true,
        },
        {
          key: 'number',
          label: 'Terminal #',
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
          key: 'licenseNumber',
          label: 'License #',
          sortable: true,
        },
        {
          key: 'groupName',
          label: 'Group name',
          sortable: true,
        },
        {
          key: 'archivedAt',
          label: 'Archived At',
          sortable: true,
        },
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
      terminals: null,
      terminalsCount: 0,
      isLoading: false,
      error: null,
    }
  },
  validations: {},
  computed: {
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
    filteredTerminalTableFields() {
      if (
        this.$isAllowed('app/terminals/movements') ||
        this.$isAllowed('app/terminals/update')
      ) {
        return this.terminalTableFields
      } else {
        return this.terminalTableFields.filter((item) => item.key !== 'actions')
      }
    },
  },
  watch: {
    '$route.query'() {
      this.getTerminals()
    },
  },
  methods: {
    getWarehouse() {
      try {
        this.error = null
        this.isLoading = true

        const { data } = this.$axios.post('/app/warehouses/get', {
          id: +this.$route.params.id,
        })

        if (data.success) {
          this.warehouse = data.warehouse
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async getTerminals() {
      try {
        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/terminals/list', {
          filters: {
            query: this.$route.query.query,
            warehouseId: +this.$route.params.id,
          },
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success) {
          this.terminals = data.results
          this.terminalsCount = data.count
        }
        this.isLoading = false
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
    onSearch(query) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: '1',
          query,
        },
      })
    },
    outService(terminal) {
      this.$refs['outWarehouse-modal'].showModal(terminal)
    },
    viewHistory(terminal) {
      this.$refs['movementHistory-modal'].showModal(terminal)
    },
  },
}
</script>
