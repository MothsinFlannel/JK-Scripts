<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <span>
          Machines
        </span>

        <div
          class="d-flex align-items-center"
          style="font-family: 'Circular Std Book', serif; color: #71748d;"
        >
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

    <AdvancedFilters v-if="filters" v-model="filters" />

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
              $route.query.limit > 0 && terminalsCount > +$route.query.limit
            "
            :value="+$route.query.page"
            :total-rows="terminalsCount"
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

      <div class="d-flex align-items-center align-self-end mt-3">
        <b-button
          v-if="$isAllowed('app/terminals/create')"
          type="button"
          variant="outline-light"
          size="sm"
          class="mr-2"
          @click="addTerminal"
        >
          New Machines
        </b-button>

        <b-button
          v-if="$isAllowed('app/terminals/export') && terminals"
          type="button"
          variant="outline-light"
          size="sm"
          @click="exportTerminals"
        >
          {{
            terminals.filter((terminal) => terminal.selected).length === 0
              ? 'Export All'
              : `Export (${
                  terminals.filter((terminal) => terminal.selected).length
                })`
          }}
        </b-button>
      </div>
    </div>

    <ScrollWrapper ref="scroll-wrapper">
      <b-table
        ref="table"
        :fields="terminalTableFields"
        :items="terminals"
        :sort-by="sortBy"
        :sort-desc="sortDesc"
        head-variant="light"
        thead-class="text-uppercase"
        foot-variant="white"
        :empty-text="isLoading ? '' : 'No data available in table'"
        show-empty
        bordered
        striped
        no-footer-sorting
        no-local-sorting
        no-sort-reset
        @sort-changed="sortingChanged"
      >
        <template #head(select)>
          <b-form-checkbox
            :checked="allTerminalsSelected"
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

        <template #cell(number)="data">
          <nuxt-link :to="`/terminals/${data.item.id}`">
            #{{ data.item.number }}
          </nuxt-link>
        </template>

        <template #cell(reference) />

        <template #cell(licenseNumber)="data">
          {{ data.item.licenseNumber }}
        </template>

        <template #cell(machineType)="data">
          <span v-if="data.item.machineType">
            {{ data.item.machineType.name }}
          </span>
        </template>

        <template #cell(cabinetType)="data">
          <span v-if="data.item.cabinetType">
            {{ data.item.cabinetType.name }}
          </span>
        </template>

        <template #cell(location)="data">
          <span v-if="data.item.location">
            {{ data.item.location.name }}
          </span>
        </template>

        <template #cell(padlock)="data">
          <span v-if="data.item.padlock">
            {{ data.item.padlock.name }}
          </span>
        </template>

        <template #cell(doorLock)="data">
          <span v-if="data.item.doorLock">
            {{ data.item.doorLock.name }}
          </span>
        </template>

        <template #cell(placementDate)="data">
          <span v-if="data.item.placementDate">
            {{ $dayjs(data.item.placementDate).format('MM/DD/YYYY h:mm A') }}
          </span>
        </template>

        <template #cell(refillDate)="data">
          <span v-if="data.item.refillDate">
            {{ $dayjs(data.item.refillDate).format('MM/DD/YYYY h:mm A') }}
          </span>
        </template>

        <template #cell(actions)="data">
          <div
            class="d-flex flex-nowrap align-items-center"
            style="margin: -0.25rem;"
          >
            <MoveButton
              v-if="$isAllowed('app/terminals/update')"
              @click="moveTerminal(data.item)"
            />

            <EditButton
              v-if="$isAllowed('app/terminals/update')"
              class="mb-2 mr-2 btn-link-hover"
              @click="editTerminal(data.item)"
            />

            <DeleteButton
              v-if="$isAllowed('app/terminals/delete')"
              @click="deleteTerminal(data.item)"
            />
          </div>
        </template>
      </b-table>
    </ScrollWrapper>

    <TablePagination :count="terminalsCount" />

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

    <TerminalModal ref="terminal-modal" @updated="getTerminals" />
    <OutWarehouseModal ref="outWarehouse-modal" @updated="getTerminals" />
  </b-card>
</template>

<script>
import { mapGetters } from 'vuex'
import { saveAs } from 'file-saver'

import MoveButton from '@/components/buttons/MoveButton'
import EditButton from '@/components/buttons/EditButton'
import DeleteButton from '@/components/buttons/DeleteButton'
import TablePagination from '@/components/TablePagination'
import OutWarehouseModal from '@/components/modals/OutWarehouse'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'
import AdvancedFilters from '~/components/AdvancedFilters'
import TerminalModal from '~/components/modals/Terminal'
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'

export default {
  components: {
    AdvancedFilters,
    TerminalModal,
    OutWarehouseModal,
    ScrollWrapper,
    TablePagination,
    EditButton,
    DeleteButton,
    MoveButton,
  },
  data() {
    return {
      filters: null,
      isWidthChanged: false,
      terminals: null,
      terminalsCount: 0,
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
    allTerminalsSelected() {
      return (
        this.terminals &&
        this.terminals.filter((terminal) => !terminal.selected).length === 0
      )
    },
    hasSelectedTerminals() {
      return this.terminals.filter((terminal) => terminal.selected).length !== 0
    },
    terminalTableFields() {
      const terminalTableFields = [
        {
          key: 'select',
          label: '',
          tdClass: 'pt-2 pb-0 pr-1',
          thStyle: { width: '1%' },
        },
        {
          key: 'number',
          label: 'Terminal #',
          sortable: true,
        },
        {
          key: 'programName',
          label: 'Game Title',
          sortable: true,
        },
        {
          key: 'groupName',
          label: 'Group',
          sortable: true,
        },
        {
          key: 'boardAssetNumber',
          label: 'Board Asset #',
          sortable: true,
        },
        {
          key: 'cabinetAssetNumber',
          label: 'Cabinet Asset #',
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
          key: 'placementDate',
          label: 'Placement date',
          sortable: true,
        },
        {
          key: 'padlock',
          label: 'Padlock',
          sortable: true,
        },
        {
          key: 'doorLock',
          label: 'Door lock',
          sortable: true,
        },
        {
          key: 'referenceNumber',
          label: 'Reference #',
          sortable: true,
        },
        {
          key: 'refillDate',
          label: 'Refill date',
          sortable: true,
        },
        {
          key: 'location',
          label: 'Location',
          sortable: true,
        },
        {
          key: 'splitPercent',
          label: 'Split',
          sortable: true,
        },
        {
          key: 'id',
          label: 'Terminal ID',
          sortable: true,
        },
        {
          key: 'cabinetType',
          label: 'Cabinet Type',
          sortable: true,
        },
        {
          key: 'notes',
          label: 'Notes',
          sortable: true,
        },
      ]

      if (
        this.$isAllowed('app/terminals/update') ||
        this.$isAllowed('app/terminals/delete')
      ) {
        terminalTableFields.push({
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        })
      }

      return terminalTableFields
    },
  },
  watch: {
    '$route.query'() {
      this.getTerminals()
    },
    companyId() {
      this.getTerminals()
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
      'number',
      'locationId',
      'warehouseId',
      'machineTypeId',
      'cabinetTypeId',
      'boardAssetNumber',
      'cabinetAssetNumber',
      'licenseNumber',
      'programName',
      'boardAssetNumber',
      'cabinetAssetNumber',
      'referenceNumber',
      { name: 'active', default: 1 },
    ])

    this.getTerminals()
  },
  mounted() {
    if (typeof this.timerId === 'number') {
      window.clearInterval(this.timerId)
    }

    this.timerId = window.setInterval(() => {
      this.getTerminals(false)
    }, 15000)
  },
  beforeDestroy() {
    if (typeof this.timerId === 'number') {
      window.clearInterval(this.timerId)
    }
  },
  methods: {
    toggleAllItemsSelection(value) {
      this.terminals = this.terminals.map((terminal) => {
        return {
          ...terminal,
          selected: value,
        }
      })
    },
    async exportTerminals() {
      try {
        this.$nuxt.$loading.start()

        const requestData = {}

        if (this.hasSelectedTerminals) {
          requestData.filters = {
            ids: this.terminals
              .filter((terminal) => terminal.selected)
              .map((terminal) => terminal.id),
          }
        } else {
          requestData.filters = {
            ...this.filters,
            companyId: this.companyId,
          }
          requestData.sort = this.$route.query.sort
        }

        const { data } = await this.$axios.post(
          '/app/terminals/export',
          requestData
        )

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'machines.csv')
        }
      } catch (error) {
        console.error(error)
        this.error = this.$getErrorMessage(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
    async getTerminals(isShowLoadingIndicator = true) {
      try {
        this.error = null
        this.isLoading = isShowLoadingIndicator

        const { data } = await this.$axios.post('/app/terminals/list', {
          filters: {
            ...this.filters,
            companyId: this.companyId,
          },
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success) {
          if (isShowLoadingIndicator) {
            this.terminals = data.results
          } else {
            const terminals = data.results.map((terminal) => {
              const oldTerminal = this.terminals.find(
                (l) => l.id === terminal.id
              )

              return {
                ...terminal,
                selected: Boolean(oldTerminal && oldTerminal.selected),
              }
            })

            this.terminals = terminals
          }

          this.terminalsCount = data.count
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
    addTerminal() {
      this.$refs['terminal-modal'].showModal()
    },
    editTerminal(terminal) {
      this.$refs['terminal-modal'].showModal(terminal)
    },
    deleteTerminal(terminal) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete terminal #${terminal.number}?`,
          {
            title: 'Delete Terminal',
            headerBgVariant: 'danger',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post('/app/terminals/delete', {
                id: terminal.id,
              })

              this.isLoading = false

              if (data.success) {
                await this.getTerminals()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
    moveTerminal(terminal) {
      this.$refs['outWarehouse-modal'].showModal(terminal)
    },
  },
}
</script>
