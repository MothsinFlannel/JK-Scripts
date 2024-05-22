<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Invoices</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          {
            title: 'Invoices',
          },
        ]"
      />
    </div>

    <Exports
      v-if="$isAllowed('app/jobs/recent')"
      ref="exports"
      category="app/invoices/export"
    />

    <b-card class="mb-4 shadow-sm" header-class="pt-1">
      <template #header>
        <div
          class="d-flex align-items-baseline justify-content-between flex-wrap"
        >
          <span class="mt-2">Invoices</span>
          <div
            class="d-flex align-items-baseline text-nowrap flex-wrap flex-sm-row flex-column p-0"
            style="font-family: 'Circular Std Book', serif; color: #71748d;"
          >
            <div
              class="d-flex align-items-center mr-3 flex-sm-nowrap flex-wrap mt-2"
            >
              <span class="invoices__label mr-2">Select a week:</span>

              <DatePicker
                :value="dateRangeValue"
                :editable="false"
                :clearable="true"
                :range="false"
                type="week"
                :formatter="formatDate"
                :placeholder="'Select a week'"
                :show-week-number="false"
                @input="onDateRangeChanged"
              />
            </div>

            <div class="d-flex align-items-center mt-2">
              <b-input-group>
                <b-form-input
                  v-model="searchQuery"
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
        </div>
      </template>
      <b-alert :show="typeof error === 'string'" variant="danger">
        {{ error }}
      </b-alert>

      <b-alert
        :show="typeof success === 'string'"
        variant="success"
        dismissible
      >
        {{ success }}
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
                $route.query.limit > 0 && invoicesCount > +$route.query.limit
              "
              :value="+$route.query.page"
              :total-rows="invoicesCount"
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
            v-if="
              $isAllowed('app/invoices/create') &&
              invoicing &&
              invoicing.since &&
              invoicing.until
            "
            type="button"
            variant="outline-light"
            class="mr-2 text-nowrap"
            size="sm"
            @click="openInvoiceCreationModal"
          >
            Create Invoice
          </b-button>

          <b-button
            v-if="$isAllowed('app/invoices/export')"
            type="button"
            variant="outline-light"
            class="align-self-end text-nowrap"
            size="sm"
            :disabled="!hasInvoices"
            @click="exportInvoices('xlsx')"
          >
            {{
              invoices.filter((invoice) => invoice.selected).length === 0
                ? 'Export All as excel'
                : `Export as excel (${
                    invoices.filter((invoice) => invoice.selected).length
                  })`
            }}
          </b-button>

          <b-button
            v-if="$isAllowed('app/invoices/export')"
            type="button"
            variant="outline-light"
            class="text-nowrap ml-2"
            size="sm"
            :disabled="!hasInvoices"
            @click="exportInvoices('html')"
          >
            {{
              invoices.filter((invoice) => invoice.selected).length === 0
                ? 'Export All as PDFs'
                : `Export as PDFs (${
                    invoices.filter((invoice) => invoice.selected).length
                  })`
            }}
          </b-button>

          <b-button
            v-if="$isAllowed('app/invoices/delete')"
            type="button"
            variant="outline-light"
            class="text-nowrap ml-2"
            size="sm"
            :disabled="!hasSelectedInvoices"
            @click="deleteInvoices"
          >
            {{
              invoices.filter((invoice) => invoice.selected).length === 0
                ? 'Delete'
                : `Delete (${
                    invoices.filter((invoice) => invoice.selected).length
                  })`
            }}
          </b-button>

          <b-button
            type="button"
            variant="outline-light"
            class="text-nowrap ml-2"
            size="sm"
            target="_blank"
            :disabled="!hasInvoices"
            :to="
              invoices.filter((invoice) => invoice.selected).length === 0
                ? {
                    path: '/print/invoices',
                    query: {
                      query: searchQuery,
                      location:
                        $route.query.location || $route.query.locationId,
                      route: $route.query.routeId,
                      status: $route.query.invoiceStatus || $route.query.status,
                      since: $route.query.since,
                      until: $route.query.until,
                    },
                  }
                : {
                    path: '/print/invoices',
                    query: {
                      ids: invoices
                        .filter((invoice) => invoice.selected)
                        .map((invoice) => invoice.id)
                        .join('&'),
                    },
                  }
            "
          >
            {{
              invoices.filter((invoice) => invoice.selected).length === 0
                ? 'Print All'
                : `Print (${
                    invoices.filter((invoice) => invoice.selected).length
                  })`
            }}
          </b-button>
        </div>
      </div>

      <scroll-wrapper ref="scroll-wrapper">
        <b-table
          :fields="invoicesTableFields"
          :items="invoices"
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
          <template #head(select)>
            <b-form-checkbox
              :checked="allInvoicesSelected"
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

          <template #cell(since)="data">
            {{ $dayjs(data.item.since).format('MM/DD/YYYY') }}
          </template>

          <template #cell(until)="data">
            {{ $dayjs(data.item.until).format('MM/DD/YYYY') }}
          </template>

          <template #cell(location)="data">
            {{ data.item.location && data.item.location.name }}
          </template>

          <template #cell(amount)="data">
            {{ $getFormattedPrice(data.item.amount) }}
          </template>

          <template #cell(splitPercent)="data">
            {{ data.item.splitPercent }}&#x00025;
          </template>

          <template #cell(due)="data">
            {{ $getFormattedPrice(data.item.due) }}
          </template>

          <template #cell(status)="data">
            <span
              :class="{
                'text-success': data.item.status === 'paid',
                'text-warning': data.item.status === 'partial',
                'text-danger': data.item.status === 'unpaid',
              }"
              class="text-capitalize"
              >{{ data.item.status }}</span
            >
          </template>

          <template #cell(actions)="data">
            <div
              class="d-flex flex-nowrap align-items-center"
              style="margin: -0.25rem;"
            >
              <DeleteButton
                v-if="$isAllowed('app/invoices/delete')"
                class="mb-2 mr-2 btn-link-hover"
                @click="deleteInvoice(data.item)"
              />

              <SeeMoreButton :to="`/invoices/${data.item.id}`" />
            </div>
          </template>
        </b-table>
      </scroll-wrapper>

      <TablePagination :count="invoicesCount" />

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

    <InvoiceCreationModal
      v-if="invoicing && invoicing.since && invoicing.until"
      ref="invoice-creation-modal"
      :location-id="+$route.query.location"
      :since="invoicing ? invoicing.since : null"
      :until="invoicing ? invoicing.until : null"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import DatePicker from 'vue2-datepicker'
import utc from 'dayjs/plugin/utc'
import dayjs from 'dayjs'

import TablePagination from '@/components/TablePagination'
import Breadcrumb from '~/components/Breadcrumb'
import AdvancedFilters from '~/components/AdvancedFilters'
import DeleteButton from '~/components/buttons/DeleteButton'
import SeeMoreButton from '~/components/buttons/SeeMoreButton'
import InvoiceCreationModal from '~/components/modals/InvoiceCreation'
import parseFiltersFromQuery from '~/helpers/parseFiltersFromQuery'
import Exports from '~/components/Exports'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

dayjs.extend(utc)
export default {
  components: {
    DatePicker,
    Breadcrumb,
    AdvancedFilters,
    InvoiceCreationModal,
    SeeMoreButton,
    TablePagination,
    DeleteButton,
    Exports,
    ScrollWrapper,
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.page || !to.query.limit || !to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          page: '1',
          limit: '50',
          sort: 'since+desc',
        },
      })
    } else {
      next()
    }
  },
  async asyncData({
    $axios,
    $dayjs,
    $getErrorMessage,
    route,
    redirect,
    store,
  }) {
    if (!route.query.page || !route.query.limit || !route.query.sort) {
      redirect({
        ...route,
        query: {
          ...route.query,
          page: '1',
          limit: '50',
          sort: 'since+desc',
        },
      })
    } else {
      try {
        const { data } = await $axios.post('/app/invoices/list', {
          filters: {
            locationId: +route.query.location || +route.query.locationId,
            routeId: route.query.routeId,
            status: route.query.invoiceStatus || route.query.status,
            since: route.query.since
              ? $dayjs(route.query.since).format('YYYY-MM-DD')
              : null,
            until: route.query.until
              ? $dayjs(route.query.until).format('YYYY-MM-DD')
              : null,
            companyId: store.getters['base/companyId'],
          },
          sort: route.query.sort,
          offset: (+route.query.page - 1) * +route.query.limit,
          limit: +route.query.limit,
          extraFields: false,
        })

        if (data.success) {
          return {
            invoices: data.results,
            invoicesCount: data.count,
            invoicing: data.invoicing,
          }
        }
      } catch (error) {
        return {
          error: $getErrorMessage(error),
        }
      }
    }
  },
  data() {
    return {
      formatDate: {
        stringify: (date) => {
          return date
            ? `${this.$dayjs(date).format('MM/DD/YYYY')} ~ ${this.$dayjs(date)
                .add(6, 'day')
                .format('MM/DD/YYYY')}`
            : null
        },
      },
      searchQuery: null,
      isLoading: false,
      error: null,
      success: null,
      invoices: null,
      invoicing: null,
      filters: null,
      invoicesCount: 0,
      invoicesTableFields: [
        {
          key: 'select',
          label: '',
          tdClass: 'pt-2 pb-0 pr-1',
          thStyle: { width: '1%' },
        },
        {
          key: 'id',
          label: '#',
          sortable: true,
        },
        {
          key: 'since',
          label: 'Since',
          sortable: true,
        },
        {
          key: 'until',
          label: 'Until',
          sortable: true,
        },
        {
          key: 'location',
          label: 'Location',
          sortable: true,
        },
        {
          key: 'splitPercent',
          label: 'Split Percent',
          sortable: true,
        },
        {
          key: 'amount',
          label: 'Amount',
          sortable: true,
        },
        {
          key: 'due',
          label: 'Partial Amount Owed',
          sortable: true,
        },
        {
          key: 'status',
          label: 'Status',
          sortable: true,
        },
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
    }
  },
  computed: {
    ...mapGetters({
      companyId: 'base/companyId',
      token: 'user/token',
    }),
    allInvoicesSelected() {
      return this.invoices.filter((invoice) => !invoice.selected).length === 0
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
    statusValue() {
      if (this.$route.query.status) {
        return this.$route.query.status
      }

      return null
    },
    dateRangeValue() {
      return this.$route.query.since
        ? this.$dayjs(this.$route.query.since, 'YYYY-MM-DD').toDate()
        : null
    },
    hasSelectedInvoices() {
      return this.invoices.filter((invoice) => invoice.selected).length !== 0
    },
    hasInvoices() {
      return this.invoices?.length > 0
    },
  },
  watch: {
    '$route.query'() {
      this.getInvoices()
    },
    companyId() {
      this.getInvoices()
    },
    searchQuery() {
      this.getInvoices()
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
      'locationId',
      'invoiceStatus',
      'routeId',
    ])
  },
  methods: {
    printInvoices() {
      try {
        this.$nuxt.$loading.start()
      } catch (error) {
        console.error(error)
        this.error = this.$getErrorMessage(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },

    toggleAllItemsSelection(value) {
      this.invoices = this.invoices.map((invoice) => {
        return {
          ...invoice,
          selected: value,
        }
      })
    },
    async forceIssuing() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/invoices/issue', {})

        this.isLoading = false

        if (data.success) {
          await this.getInvoices()
          this.success = 'Invoices generated'
        }
      } catch (error) {
        this.isLoading = false

        this.error = this.$getErrorMessage(error)
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
    async getInvoices() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/invoices/list', {
          filters: {
            query: this.searchQuery,
            locationId:
              this.$route.query.locationId || +this.$route.query.location,
            status: this.$route.query.invoiceStatus || this.$route.query.status,
            routeId: this.$route.query.routeId,
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            companyId: this.companyId,
          },
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
          extraFields: false,
        })

        this.isLoading = false

        if (data.success) {
          this.invoices = data.results
          this.invoicesCount = data.count
          this.invoicing = data.invoicing
        }

        this.$refs['scroll-wrapper'].getScrollbarWidth()
      } catch (error) {
        this.isLoading = false
        this.invoices = null
        this.invoicesCount = 0
        this.error = this.$getErrorMessage(error)
      }
    },
    onStatusChanged(status) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: '1',
          status: status || undefined,
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
    copyLink(item) {
      this.$copyText(
        `${window.location.origin}${this.$router.options.base}invoice/${item.token}`
      ).then(() => {
        if (typeof item.timerId === 'number') {
          window.clearTimeout(item.timerId)
        }

        const timerId = window.setTimeout(() => {
          window.clearTimeout(timerId)
          this.$set(item, 'timerId', null)
        }, 1500)

        this.$set(item, 'timerId', timerId)
      })
    },
    onDateRangeChanged(date) {
      if (
        this.$dayjs(date).format('ddd') === 'Sun' ||
        this.$dayjs(date).format('ddd') === 'Mon'
      ) {
        date = this.$dayjs(date).add(2, 'day')
      }
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: '1',
          since: date
            ? this.$dayjs(date).utc().startOf('isoWeek').format('YYYY-MM-DD')
            : undefined,
          until: date
            ? this.$dayjs(date).utc().endOf('isoWeek').format('YYYY-MM-DD')
            : undefined,
        },
      })
    },
    str2bytes(str) {
      const bytes = new Uint8Array(str.length)
      for (let i = 0; i < str.length; i++) {
        bytes[i] = str.charCodeAt(i)
      }
      return bytes
    },
    async exportInvoices(format) {
      try {
        this.$nuxt.$loading.start()

        const requestData = {
          format,
        }

        if (this.hasSelectedInvoices) {
          requestData.filters = {
            ids: this.invoices
              .filter((invoice) => invoice.selected)
              .map((invoice) => invoice.id),
          }
        } else {
          requestData.filters = {
            query: this.searchQuery,
            locationId:
              this.$route.query.locationId || +this.$route.query.location,
            status: this.$route.query.invoiceStatus || this.$route.query.status,
            routeId: this.$route.query.routeId,
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            companyId: this.companyId,
          }
          requestData.sort = this.$route.query.sort
        }

        const { data } = await this.$axios.post(
          '/app/invoices/export',
          requestData
        )

        if (data.success) {
          this.$bvToast.toast('Export is added to the queue.', {
            title: 'Exports',
            variant: 'success',
          })

          window.setTimeout(() => {
            this.$refs.exports.getJobs()
          }, 500)
        }
      } catch (error) {
        console.error(error)
        this.error = this.$getErrorMessage(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
    deleteInvoices() {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete invoices: ${this.invoices
            .filter((invoice) => invoice.selected)
            .map((invoice) => invoice.id)
            .join(', ')}?`,
          {
            title: 'Delete Invoices',
            headerBgVariant: 'danger',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              this.$nuxt.$loading.start()

              await this.$axios.post('/app/invoices/delete', {
                id: this.invoices
                  .filter((invoice) => invoice.selected)
                  .map((invoice) => invoice.id),
              })

              await this.getInvoices()
            } catch (error) {
              console.error(error)
              this.error = this.$getErrorMessage(error)
            } finally {
              this.$nuxt.$loading.finish()
            }
          }
        })
        .catch((error) => console.error(error))
    },
    deleteInvoice(invoice) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete invoice ${invoice.id}?`,
          {
            title: 'Delete Invoice',
            headerBgVariant: 'danger',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              this.$nuxt.$loading.start()

              await this.$axios.post('/app/invoices/delete', {
                id: [invoice.id],
              })

              await this.getInvoices()
            } catch (error) {
              console.error(error)
              this.error = this.$getErrorMessage(error)
            } finally {
              this.$nuxt.$loading.finish()
            }
          }
        })
        .catch((error) => console.error(error))
    },
    openInvoiceCreationModal() {
      this.$refs['invoice-creation-modal'].showModal()
    },
  },
}
</script>

<style lang="scss" scoped>
.invoices {
  &__label {
    @media (max-width: 767px) {
      display: none;
    }
  }
}
</style>
