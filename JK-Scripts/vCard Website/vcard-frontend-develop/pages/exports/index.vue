<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Exports</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[{ title: 'vCard Dashboard', link: '/' }, { title: 'Exports' }]"
      />
    </div>

    <b-card class="mb-4 shadow-sm">
      <template #header>
        <div
          class="d-flex align-items-baseline justify-content-between flex-wrap"
        >
          <p class="m-0">Exports</p>
        </div>
      </template>

      <b-alert :show="typeof error === 'string'" variant="danger">
        {{ error }}
      </b-alert>

      <div
        class="d-flex align-items-baseline justify-content-between mb-3 flex-wrap"
      >
        <div class="row">
          <div class="col-12 d-flex flex-column">
            <div class="d-flex align-items-center mr-3 mb-1 mb-sm-0">
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
              v-if="$route.query.limit > 0 && jobsCount > +$route.query.limit"
              :value="+$route.query.page"
              :total-rows="jobsCount"
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
      </div>

      <scroll-wrapper ref="scroll-wrapper">
        <b-table
          :fields="tableFields"
          :items="jobs"
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
          <template #cell(initiatorId)="data">
            {{ data.item.initiator.fullName }}
          </template>

          <template #cell(createdAt)="data">
            {{
              data.item.createdAt &&
              $dayjs(data.item.createdAt).format('MM/DD/YYYY h:mm A')
            }}
          </template>

          <template #cell(endedAt)="data">
            {{
              data.item.endedAt &&
              $dayjs(data.item.endedAt).format('MM/DD/YYYY h:mm A')
            }}
          </template>

          <template #cell(result)="data">
            <span v-if="data.item.state === 'pending'" class="text-warning">
              Pending...
            </span>

            <span v-if="data.item.state === 'in-progress'" class="text-warning">
              In progress - {{ data.item.progress }}%
            </span>

            <a
              v-if="data.item.state === 'succeeded'"
              :href="data.item.output"
              target="_blank"
              class="text-primary"
            >
              Download
            </a>

            <span v-if="data.item.state === 'failed'" class="text-danger">
              {{ data.item.output || 'Failed' }}
            </span>
          </template>

          <template #cell(actions)="data">
            <div
              class="d-flex flex-nowrap align-items-center justify-content-center"
              style="margin: -0.25rem;"
            >
              <DeleteButton
                v-if="
                  data.item.state === 'in-progress' ||
                  data.item.state === 'pending'
                "
                @click="onDeleteExport(data.item.id)"
              />
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
              jobsCount
            )
          }}
          to
          {{ Math.min(+$route.query.page * +$route.query.limit, jobsCount) }}
          of {{ jobsCount }} entries
        </div>

        <b-pagination
          v-if="jobsCount > +$route.query.limit"
          :value="+$route.query.page"
          :total-rows="jobsCount"
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
  </div>
</template>

<script>
import DeleteButton from '@/components/buttons/DeleteButton'
import Breadcrumb from '~/components/Breadcrumb'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    Breadcrumb,
    ScrollWrapper,
    DeleteButton,
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.page || !to.query.limit || !to.query.sort) {
      next({
        ...to,
        query: {
          ...to.query,
          page: '1',
          limit: '50',
          sort: 'id+desc',
        },
      })
    } else {
      next()
    }
  },
  asyncData({ route, redirect }) {
    if (!route.query.page || !route.query.limit || !route.query.sort) {
      redirect({
        ...route,
        query: {
          ...route.query,
          page: '1',
          limit: '50',
          sort: 'id+desc',
        },
      })
    }
  },
  data() {
    return {
      timerId: null,
      tableFields: [
        {
          key: 'id',
          label: '#',
          sortable: true,
        },
        {
          key: 'initiatorId',
          label: 'Initiator',
          sortable: true,
        },
        {
          key: 'title',
          label: 'Title',
          sortable: true,
        },
        {
          key: 'state',
          label: 'State',
          sortable: true,
        },
        {
          key: 'createdAt',
          label: 'Created',
          sortable: true,
        },
        {
          key: 'endedAt',
          label: 'Ended',
          sortable: true,
        },
        {
          key: 'result',
          label: 'Result',
        },
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
      jobs: null,
      jobsCount: null,
      isLoading: false,
      error: null,
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
      this.getJobs()
    },
  },
  created() {
    this.getJobs()
  },
  mounted() {
    if (typeof this.timerId === 'number') {
      window.clearInterval(this.timerId)
    }

    this.timerId = window.setInterval(() => {
      this.getJobs(false)
    }, 5000)
  },
  beforeDestroy() {
    if (typeof this.timerId === 'number') {
      window.clearInterval(this.timerId)
    }
  },
  methods: {
    onDeleteExport(id) {
      this.$bvModal
        .msgBoxConfirm(`Are you sure you want to delete export #${id}?`, {
          title: 'Delete export',
          headerBgVariant: 'danger',
          centered: true,
          okTitle: 'Delete',
        })
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post('/app/jobs/cancel', {
                id,
              })

              this.isLoading = false

              if (data.success) {
                await this.getJobs()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
    async getJobs(isShowLoadingIndicator = true) {
      try {
        this.error = null
        this.isLoading = isShowLoadingIndicator

        const { data } = await this.$axios.post('/app/jobs/list', {
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success) {
          this.jobs = data.results
          this.jobsCount = data.count
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
  },
}
</script>
