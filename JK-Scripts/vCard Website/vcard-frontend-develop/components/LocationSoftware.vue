<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <span>Software</span>
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
            v-if="softwareCount > perPage"
            v-model="currentPage"
            :total-rows="softwareCount"
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
          v-if="$isAllowed('app/software/create')"
          type="button"
          variant="outline-light"
          size="sm"
          @click="addSoftware"
        >
          Add Software
        </b-button>
      </div>
    </div>

    <b-table
      :fields="filteredSoftwareTableFields"
      :items="software"
      :sort-by="sortBy"
      :sort-desc="sortDesc"
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
      <template #cell(split)="data"> {{ data.item.split }}% </template>

      <template #cell(isMobileOnly)="data">
        {{ data.item.isMobileOnly ? 'Yes' : 'No' }}
      </template>

      <template #cell(installDate)="data">
        <span v-if="data.item.installDate">
          {{ $dayjs(data.item.installDate).format('MM/DD/YYYY h:mm A') }}
        </span>
      </template>

      <template #cell(isFrozen)="data">
        {{ data.item.isFrozen ? 'Yes' : 'No' }}
      </template>

      <template #cell(actions)="data">
        <div
          class="d-flex flex-nowrap align-items-center"
          style="margin: -0.25rem;"
        >
          <b-button
            v-if="$isAllowed('app/software/update')"
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2 mr-2"
            @click="editSoftware(data.item)"
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
            v-if="$isAllowed('app/software/delete')"
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2"
            @click="deleteSoftware(data.item)"
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
        {{ Math.min((currentPage - 1) * perPage + 1, softwareCount) }} to
        {{ Math.min(currentPage * perPage, softwareCount) }} of
        {{ softwareCount }} entries
      </div>

      <b-pagination
        v-if="softwareCount > perPage"
        v-model="currentPage"
        :total-rows="softwareCount"
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

    <SoftwareModal
      ref="software-modal"
      :location-id="location.id"
      @updated="getSoftware"
    />
  </b-card>
</template>

<script>
import SoftwareModal from '~/components/modals/Software'

export default {
  components: {
    SoftwareModal,
  },
  props: {
    location: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      softwareTableFields: [
        {
          key: 'name',
          label: 'Name',
          sortable: true,
        },
        {
          key: 'serverNo',
          label: 'Server #',
          sortable: true,
        },
        {
          key: 'maxMachineCount',
          label: 'Max Machine Count',
          sortable: true,
        },
        {
          key: 'isMobileOnly',
          label: 'Mobile Only',
          sortable: true,
        },
        {
          key: 'split',
          label: 'Split',
          sortable: true,
        },
        {
          key: 'installDate',
          label: 'Install Date',
          sortable: true,
        },
        {
          key: 'notes',
          label: 'Notes',
          sortable: true,
        },
        {
          key: 'isFrozen',
          label: 'Is Frozen',
          sortable: true,
        },
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
      software: null,
      softwareCount: 0,
      currentPage: 1,
      perPage: 50,
      sortBy: 'name',
      sortDesc: false,
      isLoading: false,
      search: null,
      error: null,
    }
  },
  computed: {
    filteredSoftwareTableFields() {
      if (
        this.$isAllowed('app/software/update') ||
        this.$isAllowed('app/software/delete')
      ) {
        return this.softwareTableFields
      } else {
        return this.softwareTableFields.filter((item) => item.key !== 'actions')
      }
    },
  },
  watch: {
    currentPage() {
      this.getSoftware()
    },
    perPage() {
      this.getSoftware()
    },
    search() {
      this.getSoftware()
    },
    sortBy() {
      this.getSoftware()
    },
    sortDesc() {
      this.getSoftware()
    },
  },
  created() {
    this.getSoftware()
  },
  methods: {
    async getSoftware() {
      try {
        if (this.isLoading) {
          return
        }

        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/software/list', {
          locationId: this.location.id,
          offset: (this.currentPage - 1) * this.perPage,
          limit: this.perPage,
          sort: this.$getSortValue(`${this.sortDesc ? '-' : ''}${this.sortBy}`),
        })

        if (data.success) {
          this.software = data.results
          this.softwareCount = data.count
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (item.isFrozen) return 'location-test'
    },
    addSoftware() {
      this.$refs['software-modal'].showModal()
    },
    editSoftware(software) {
      this.$refs['software-modal'].showModal(software)
    },
    deleteSoftware(software) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete software #${software.id}?`,
          {
            title: 'Delete Software',
            headerBgVariant: 'danger',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post('/app/software/delete', {
                id: software.id,
              })

              this.isLoading = false

              if (data.success) {
                await this.getSoftware()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
    sortingChanged(sort) {
      this.sortBy = sort.sortBy
      this.sortDesc = sort.sortDesc
    },
  },
}
</script>
