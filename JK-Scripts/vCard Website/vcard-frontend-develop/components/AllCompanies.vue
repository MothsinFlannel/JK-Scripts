<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div
        class="d-flex align-items-baseline justify-content-between flex-wrap"
      >
        <P class="m-0">vCard Companies</P>
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
              $route.query.limit > 0 && companiesCount > +$route.query.limit
            "
            :value="+$route.query.page"
            :total-rows="companiesCount"
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

      <div
        v-if="$isAllowed('app/companies/create')"
        class="d-flex align-items-center align-self-end mt-3"
      >
        <b-button
          type="button"
          variant="outline-light"
          class="align-self-end text-nowrap"
          size="sm"
          to="/companies/create"
        >
          New Company
        </b-button>
      </div>
    </div>

    <scroll-wrapper ref="scroll-wrapper">
      <b-table
        :fields="companyTableFields"
        :items="companies"
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
        <template #cell(role)="data">
          <span
            class="text-capitalize"
            :class="{
              'text-danger': data.item.role === 'admin',
            }"
            >{{ data.item.role }}</span
          >
        </template>

        <template #cell(createdAt)="data">
          {{
            data.item.createdAt &&
            $dayjs(data.item.createdAt).format('MM/DD/YYYY h:mm A')
          }}
        </template>

        <template #cell(isActive)="data">
          <span
            :class="{
              'text-danger': !data.item.isActive,
              'text-success': data.item.isActive,
            }"
          >
            {{ data.item.isActive ? 'Active' : 'Blocked' }}
          </span>
        </template>

        <template #cell(invoicingOnline)="data">
          <span>
            {{ data.item.invoicingOnline ? 'Yes' : 'No' }}
          </span>
        </template>

        <template #cell(actions)="data">
          <div
            class="d-flex flex-nowrap align-items-center"
            style="margin: -0.25rem;"
          >
            <b-button
              v-if="$isAllowed('app/companies/update')"
              type="button"
              :to="`/companies/${data.item.id}/settings`"
              variant="outline-light"
              size="sm"
              class="mb-2 mr-2 btn-link-hover"
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
              v-if="$isAllowed('app/companies/delete')"
              type="button"
              variant="outline-light"
              size="sm"
              class="mb-2"
              @click="deleteCompany(data.item)"
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
    </scroll-wrapper>

    <div v-if="$route.query.limit > 0" class="app-card__table-footer">
      <div class="mt-3 mr-3 mb-1 mb-sm-0">
        Showing
        {{
          Math.min(
            (+$route.query.page - 1) * +$route.query.limit + 1,
            companiesCount
          )
        }}
        to
        {{ Math.min(+$route.query.page * +$route.query.limit, companiesCount) }}
        of {{ companiesCount }} entries
      </div>

      <b-pagination
        v-if="companiesCount > +$route.query.limit"
        :value="+$route.query.page"
        :total-rows="companiesCount"
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
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: { ScrollWrapper },
  data() {
    return {
      companies: null,
      companiesCount: 0,
      isLoading: false,
      error: null,
      success: null,
    }
  },
  computed: {
    companyTableFields() {
      const companyTableFields = [
        {
          key: 'id',
          label: '#',
          sortable: true,
        },
        {
          key: 'name',
          label: 'Name',
          sortable: true,
        },
        {
          key: 'contactName',
          label: 'Contact Name',
          sortable: true,
        },
        {
          key: 'contactEmail',
          label: 'Contact Email',
          sortable: true,
        },
        {
          key: 'invoicingOnline',
          label: 'Invoicing Online',
          sortable: true,
        },
        {
          key: 'isActive',
          label: 'Status',
          sortable: true,
        },
        {
          key: 'createdAt',
          label: 'Created',
          sortable: true,
        },
      ]

      if (
        this.$isAllowed('app/companies/update') ||
        this.$isAllowed('app/companies/delete')
      ) {
        companyTableFields.push({
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        })
      }

      return companyTableFields
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
      this.getCompanies()
    },
  },
  created() {
    this.getCompanies()
  },
  methods: {
    async getCompanies() {
      try {
        this.error = null
        this.success = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/companies/list', {
          filters: {
            query: this.$route.query.query,
          },
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success) {
          this.companies = data.results
          this.companiesCount = data.count
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
    deleteCompany(company) {
      this.$bvModal
        .msgBoxConfirm(`Are you sure you want to delete ${company.name}?`, {
          title: 'Delete Company',
          headerBgVariant: 'danger',
          centered: true,
          okTitle: 'Delete',
        })
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post('/app/companies/delete', {
                id: company.id,
              })

              this.isLoading = false

              if (data.success) {
                await this.getCompanies()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
  },
}
</script>
