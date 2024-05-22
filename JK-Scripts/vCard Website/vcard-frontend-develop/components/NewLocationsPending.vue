<template>
  <b-card header="New Locations Pending" class="mb-4 shadow-sm">
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="d-flex align-items-center justify-content-between mb-3">
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
      v-if="$route.query.limit > 0 && locationsCount > +$route.query.limit"
      :value="+$route.query.page"
      :total-rows="locationsCount"
      :per-page="+$route.query.limit"
      first-text="First"
      prev-text="Previous"
      next-text="Next"
      last-text="Last"
      pills
      @change="onCurrentPageChanged"
    />
    <b-table
      :fields="locationsTableFields"
      :items="locations"
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
      <template #cell(createdAt)="data">
        {{
          data.item.createdAt &&
          $dayjs(data.item.createdAt).format('MM/DD/YYYY h:mm A')
        }}
      </template>

      <template #cell(actions)="data">
        <div
          class="d-flex flex-nowrap align-items-center"
          style="margin: -0.25rem;"
        >
          <b-button
            v-if="$isAllowed('app/locations/create')"
            :to="`/locations/create?installationId=${data.item.id}`"
            type="button"
            variant="outline-light"
            size="sm"
            class="m-1 btn-link-hover text-nowrap"
          >
            Register Location
          </b-button>

          <b-button
            v-if="$isAllowed('app/locations/delete')"
            type="button"
            variant="outline-light"
            size="sm"
            class="m-1 btn-link-hover"
            @click="deleteLocation(data.item)"
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

    <div v-if="$route.query.limit > 0" class="app-card__table-footer">
      <div class="mt-3 mr-3 mb-1 mb-sm-0">
        Showing
        {{
          Math.min(
            (+$route.query.page - 1) * +$route.query.limit + 1,
            locationsCount
          )
        }}
        to
        {{ Math.min(+$route.query.page * +$route.query.limit, locationsCount) }}
        of {{ locationsCount }} entries
      </div>

      <b-pagination
        v-if="locationsCount > +$route.query.limit"
        :value="+$route.query.page"
        :total-rows="locationsCount"
        :per-page="+$route.query.limit"
        first-text="First"
        prev-text="Previous"
        next-text="Next"
        last-text="Last"
        class="mt-3 mb-0"
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
</template>

<script>
export default {
  data() {
    return {
      locations: null,
      locationsCount: 0,
      isLoading: false,
      error: null,
    }
  },
  computed: {
    locationsTableFields() {
      const locationsTableFields = [
        {
          key: 'id',
          label: '#',
          sortable: true,
        },
        {
          key: 'serial',
          label: 'POS Serial',
          sortable: true,
        },
        {
          key: 'createdAt',
          label: 'Created At',
          sortable: true,
        },
      ]

      if (
        this.$isAllowed('app/locations/create') ||
        this.$isAllowed('app/locations/delete')
      ) {
        locationsTableFields.push({
          key: 'actions',
          label: 'Manage',
          tdClass: 'py-0',
          thStyle: { width: '1%' },
        })
      }

      return locationsTableFields
    },
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
      this.getLocations()
    },
  },
  created() {
    this.getLocations()
  },
  methods: {
    async getLocations() {
      try {
        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/installations/list', {
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success) {
          this.locations = data.results
          this.locationsCount = data.count
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
    deleteLocation(location) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete the location #${location.serial}?`,
          {
            buttonSize: 'sm',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              const { data } = await this.$axios.post(
                '/app/installations/delete',
                {
                  id: location.id,
                }
              )

              if (data.success) {
                this.getLocations()
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
  },
}
</script>
