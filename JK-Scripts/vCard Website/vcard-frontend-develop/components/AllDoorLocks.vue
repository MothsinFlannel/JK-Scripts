<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div
        class="d-flex align-items-baseline justify-content-between flex-wrap"
      >
        <p class="m-0">vCard Door Locks</p>
        <div
          class="d-flex align-items-center text-nowrap"
          style="font-family: 'Circular Std Book', serif; color: #71748d;"
        ></div>
      </div>
    </template>
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-alert :show="typeof success === 'string'" variant="success" dismissible>
      {{ success }}
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
              $route.query.limit > 0 && doorLocksCount > +$route.query.limit
            "
            :value="+$route.query.page"
            :total-rows="doorLocksCount"
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
          type="button"
          variant="outline-light"
          class="align-self-end text-nowrap"
          size="sm"
          @click="addDoorLocks"
        >
          New Door Lock
        </b-button>
      </div>
    </div>

    <scroll-wrapper ref="scroll-wrapper">
      <b-table
        :fields="doorLocksTableFields"
        :items="doorLocks"
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
        <template #cell(actions)="data">
          <div
            class="d-flex flex-nowrap align-items-center"
            style="margin: -0.25rem;"
          >
            <EditButton
              class="mb-2 mr-2 btn-link-hover"
              @click="editDoorLocks(data.item)"
            />

            <DeleteButton @click="deleteDoorLocks(data.item)" />
          </div>
        </template>
      </b-table>
    </scroll-wrapper>

    <TablePagination :count="doorLocksCount" />

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
    <DoorLocksModal
      ref="door-locks-modal"
      @updated="getDoorLocks"
    ></DoorLocksModal>
  </b-card>
</template>

<script>
import TablePagination from '@/components/TablePagination'
import DoorLocksModal from '@/components/modals/DoorLocks'
import EditButton from '@/components/buttons/EditButton'
import DeleteButton from '@/components/buttons/DeleteButton'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    TablePagination,
    DoorLocksModal,
    EditButton,
    DeleteButton,
    ScrollWrapper,
  },
  data() {
    return {
      doorLocksTableFields: [
        {
          key: 'name',
          label: 'Name',
          sortable: true,
        },
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
      doorLocks: null,
      doorLocksCount: 0,
      isLoading: false,
      error: null,
      success: null,
    }
  },
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
  },
  watch: {
    '$route.query'() {
      this.getDoorLocks()
    },
  },
  created() {
    this.getDoorLocks()
  },
  methods: {
    async getDoorLocks() {
      try {
        this.error = null
        this.success = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/door-locks/list', {
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success) {
          this.doorLocks = data.results
          this.doorLocksCount = data.count
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
    deleteDoorLocks(doorLocks) {
      this.$bvModal
        .msgBoxConfirm(`Are you sure you want to delete ${doorLocks.name}?`, {
          title: 'Delete door lock',
          headerBgVariant: 'danger',
          centered: true,
          okTitle: 'Delete',
        })
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post(
                '/app/door-locks/delete',
                {
                  id: doorLocks.id,
                }
              )

              this.isLoading = false

              if (data.success) {
                await this.getDoorLocks()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)
              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
    addDoorLocks() {
      this.$refs['door-locks-modal'].showModal(null)
    },
    editDoorLocks(terminal) {
      this.$refs['door-locks-modal'].showModal(terminal)
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
  },
}
</script>
