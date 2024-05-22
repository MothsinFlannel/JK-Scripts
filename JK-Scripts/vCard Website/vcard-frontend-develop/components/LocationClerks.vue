<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <P class="m-0">Clerks and Managers</P>
        <div
          class="d-flex align-items-center text-nowrap"
          style="font-family: 'Circular Std Book', serif; color: #71748d;"
        >
          <div class="input-group">
            <b-form-input
              :value="search"
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
            v-if="clerksCount > perPage"
            v-model="currentPage"
            :total-rows="clerksCount"
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
          v-if="$isAllowed('app/clerks/create')"
          type="button"
          variant="outline-light"
          class="align-self-end text-nowrap"
          size="sm"
          @click="addClerk"
        >
          Add new Clerk
        </b-button>
      </div>
    </div>

    <b-table
      :fields="clerkTableFields"
      :items="clerks"
      head-variant="light"
      thead-class="text-uppercase"
      :empty-text="isLoading ? '' : 'No data available in table'"
      show-empty
      responsive
      bordered
      striped
    >
      <template #cell(role)="data">
        {{ data.item.isManager ? 'Manager' : 'Clerk' }}
      </template>

      <template #cell(actions)="data">
        <div
          class="d-flex flex-nowrap align-items-center"
          style="margin: -0.25rem;"
        >
          <b-button
            v-if="$isAllowed('app/clerks/update')"
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2 mr-2"
            @click="editClerk(data.item)"
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
            v-if="$isAllowed('app/clerks/delete')"
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2"
            @click="deleteClerk(data.item)"
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
        {{ Math.min((currentPage - 1) * perPage + 1, clerksCount) }} to
        {{ Math.min(currentPage * perPage, clerksCount) }} of
        {{ clerksCount }} entries
      </div>

      <b-pagination
        v-if="clerksCount > perPage"
        v-model="currentPage"
        :total-rows="clerksCount"
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

    <ClerkModal ref="clerk-modal" :location="location" @updated="getClerks" />
  </b-card>
</template>

<script>
import ClerkModal from '~/components/modals/Clerk'

export default {
  components: {
    ClerkModal,
  },
  props: {
    location: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      clerks: null,
      clerksCount: 0,
      currentPage: 1,
      perPage: 50,
      isLoading: false,
      search: null,
      error: null,
    }
  },
  computed: {
    clerkTableFields() {
      const clerkTableFields = [
        {
          key: 'fullName',
          label: 'Name',
        },
        {
          key: 'pin',
          label: 'PIN',
        },
        {
          key: 'role',
          label: 'Role',
        },
      ]

      if (
        this.$isAllowed('app/clerks/update') ||
        this.$isAllowed('app/clerks/delete')
      ) {
        clerkTableFields.push({
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        })
      }

      return clerkTableFields
    },
  },
  watch: {
    currentPage() {
      this.getClerks()
    },
    perPage() {
      this.getClerks()
    },
    search() {
      this.getClerks()
    },
  },
  created() {
    this.getClerks()
  },
  methods: {
    async getClerks() {
      try {
        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/clerks/list', {
          locationId: +this.$route.params.id,
          filters: {
            query: this.search,
          },
          offset: (this.currentPage - 1) * this.perPage,
          limit: this.perPage,
        })

        if (data.success) {
          this.clerks = data.results
          this.clerksCount = data.count
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    addClerk() {
      this.$refs['clerk-modal'].showModal()
    },
    editClerk(clerk) {
      this.$refs['clerk-modal'].showModal(clerk)
    },
    deleteClerk(clerk) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete the clerk ${clerk.fullName}?`,
          {
            buttonSize: 'sm',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              const { data } = await this.$axios.post('/app/clerks/delete', {
                id: clerk.id,
              })

              if (data.success) {
                this.getClerks()
              }
            } catch (error) {
              this.$bvToast.toast(this.$getErrorMessage(error), {
                title: 'Error!',
                variant: 'danger',
              })
            }
          }
        })
        .catch((error) => {
          console.error(error)
        })
    },
    onSearch(query) {
      this.currentPage = 1
      this.search = query
    },
  },
}
</script>
