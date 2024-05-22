<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <P class="m-0">Terminal Information</P>
        <div
          class="d-flex align-items-center text-nowrap"
          style="font-family: 'Circular Std Book', serif; color: #71748d;"
        >
          <div class="input-group">
            <b-form-input
              :value="search"
              type="search"
              size="sm"
              class="form-control rounded-0 border-right-0 mb-0"
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
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

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
                v-model="perPage"
                :options="[
                  { value: 10, text: '10' },
                  { value: 25, text: '25' },
                  { value: 50, text: '50' },
                  { value: 100, text: '100' },
                ]"
                size="sm"
                class="mx-1"
              />
              entries
            </div>
          </div>

          <b-pagination
            v-if="terminalsCount > perPage"
            v-model="currentPage"
            :total-rows="terminalsCount"
            :per-page="perPage"
            first-text="First"
            prev-text="Previous"
            next-text="Next"
            last-text="Last"
            class="mb-0 mt-3"
            pills
          />
        </div>
      </div>

      <div class="d-flex align-items-center align-self-end mt-3">
        <b-button
          v-if="$isAllowed('app/terminals/create')"
          type="button"
          variant="outline-light"
          size="sm"
          @click="addTerminal"
        >
          Add Terminal
        </b-button>
      </div>
    </div>

    <b-table
      :fields="filteredTerminalTableFields"
      :items="terminals"
      :sort-by="sortBy"
      :sort-desc="sortDesc"
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
      <template #cell(number)="data">
        <nuxt-link :to="`/terminals/${data.item.id}`">
          #{{ data.item.number }}
        </nuxt-link>
      </template>

      <template #cell(reference) />

      <template #cell(splitPercent)="data">
        {{ data.item.splitPercent }}%
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

      <template #cell(cabinetType)="data">
        <span v-if="data.item.cabinetType">
          {{ data.item.cabinetType.name }}
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
            class="mb-2 mr-2"
            @click="moveTerminal(data.item)"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="1em"
              height="21px"
              fill="currentColor"
              class="bi bi-wrench"
              viewBox="0 0 16 16"
            >
              <path
                d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364L.102 2.223zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11l.471.242z"
              />
            </svg>
          </b-button>

          <b-button
            v-if="$isAllowed('app/terminals/update')"
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2 mr-2"
            @click="editTerminal(data.item)"
          >
            <svg
              width="1em"
              height="21px"
              viewBox="0 0 16 16"
              class="bi bi-pencil"
              fill="currentColor"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"
              />
            </svg>
          </b-button>

          <b-button
            v-if="$isAllowed('app/terminals/delete')"
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2"
            @click="deleteTerminal(data.item)"
          >
            <svg
              width="1em"
              height="21px"
              viewBox="0 0 16 16"
              class="bi bi-trash"
              fill="currentColor"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"
              />
              <path
                fill-rule="evenodd"
                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"
              />
            </svg>
          </b-button>
        </div>
      </template>
    </b-table>

    <div class="app-card__table-footer">
      <div class="mt-3 mr-3 mb-1 mb-sm-0">
        Showing
        {{ Math.min((currentPage - 1) * perPage + 1, terminalsCount) }} to
        {{ Math.min(currentPage * perPage, terminalsCount) }} of
        {{ terminalsCount }} entries
      </div>

      <b-pagination
        v-if="terminalsCount > perPage"
        v-model="currentPage"
        :total-rows="terminalsCount"
        :per-page="perPage"
        first-text="First"
        prev-text="Previous"
        next-text="Next"
        last-text="Last"
        class="mt-3 mb-0"
        pills
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

    <TerminalModal
      ref="terminal-modal"
      :disable-location="true"
      @updated="getTerminals"
    />
    <OutWarehouseModal ref="outWarehouse-modal" @updated="getTerminals" />
    <MovementHistoryModal ref="movementHistory-modal"></MovementHistoryModal>
  </b-card>
</template>

<script>
import MovementHistoryModal from '@/components/modals/MovementHistory'
import OutWarehouseModal from '@/components/modals/OutWarehouse'
import TerminalModal from '~/components/modals/Terminal'
export default {
  components: {
    TerminalModal,
    OutWarehouseModal,
    MovementHistoryModal,
  },
  props: {
    location: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      terminalTableFields: [
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
          key: 'machineType.name',
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
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
      terminals: null,
      terminalsCount: 0,
      currentPage: 1,
      perPage: 50,
      sortBy: 'number',
      sortDesc: false,
      isLoading: false,
      search: null,
      error: null,
    }
  },
  computed: {
    filteredTerminalTableFields() {
      if (
        this.$isAllowed('app/terminals/movements') ||
        this.$isAllowed('app/terminals/update') ||
        this.$isAllowed('app/terminals/delete')
      ) {
        return this.terminalTableFields
      } else {
        return this.terminalTableFields.filter((item) => item.key !== 'actions')
      }
    },
  },
  watch: {
    currentPage() {
      this.getTerminals()
    },
    perPage() {
      this.getTerminals()
    },
    search() {
      this.getTerminals()
    },
    sortBy() {
      this.getTerminals()
    },
    sortDesc() {
      this.getTerminals()
    },
  },
  created() {
    this.getTerminals()
  },
  methods: {
    async getTerminals() {
      try {
        if (this.isLoading) {
          return
        }

        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/terminals/list', {
          filters: {
            query: this.search,
            locationId: +this.$route.params.id,
            includeTestOnes: true,
          },
          offset: (this.currentPage - 1) * this.perPage,
          limit: this.perPage,
          sort: this.$getSortValue(`${this.sortDesc ? '-' : ''}${this.sortBy}`),
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
    addTerminal() {
      this.$refs['terminal-modal'].showModal(null, this.location)
    },
    editTerminal(terminal) {
      this.$refs['terminal-modal'].showModal(terminal, this.location)
    },
    deleteTerminal(terminal) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete terminal #${terminal.id}?`,
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
    onSearch(query) {
      this.currentPage = 1
      this.search = query
    },
    sortingChanged(sort) {
      this.sortBy = sort.sortBy
      this.sortDesc = sort.sortDesc
    },
    moveTerminal(terminal) {
      this.$refs['outWarehouse-modal'].showModal(terminal)
    },
    viewHistory(terminal) {
      this.$refs['movementHistory-modal'].showModal(terminal)
    },
  },
}
</script>
