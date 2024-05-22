<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <P class="m-0">Movement History</P>
      </div>
    </template>
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>
    <!--    <div-->
    <!--      class="d-flex align-items-baseline justify-content-between mb-3 flex-wrap"-->
    <!--    >-->
    <!--      <div class="row">-->
    <!--        <div class="col-12 d-flex flex-column">-->
    <!--          <div class="d-flex align-items-center mr-3 mb-1 mb-sm-0">-->
    <!--                        <div class="d-flex align-items-center">-->
    <!--                          Show-->
    <!--                          <b-form-select-->
    <!--                            v-model="perPage"-->
    <!--                            :options="[-->
    <!--                              { value: 10, text: '10' },-->
    <!--                              { value: 25, text: '25' },-->
    <!--                              { value: 50, text: '50' },-->
    <!--                              { value: 100, text: '100' },-->
    <!--                            ]"-->
    <!--                            size="sm"-->
    <!--                            class="mx-1"-->
    <!--                          />-->
    <!--                          entries-->
    <!--                        </div>-->
    <!--          </div>-->

    <!--          <b-pagination-->
    <!--            v-if="movementsCount > perPage"-->
    <!--            v-model="currentPage"-->
    <!--            :total-rows="movementsCount"-->
    <!--            :per-page="perPage"-->
    <!--            first-text="First"-->
    <!--            prev-text="Previous"-->
    <!--            next-text="Next"-->
    <!--            last-text="Last"-->
    <!--            class="mb-0 mt-3"-->
    <!--            pills-->
    <!--          />-->
    <!--        </div>-->
    <!--      </div>-->
    <!--    </div>-->

    <b-table
      v-if="!error"
      :fields="movementsTableFields"
      :items="movements"
      head-variant="light"
      thead-class="text-uppercase"
      foot-variant="white"
      :empty-text="'No data available in table'"
      show-empty
      responsive
      bordered
      striped
    >
      <template #cell(createdAt)="data">
        <span v-if="data.item.createdAt" class="text-nowrap">
          {{ $dayjs(data.item.createdAt).format('MM/DD/YYYY h:mm A') }}
        </span>
      </template>
    </b-table>
    <div
      class="d-flex align-items-center justify-content-sm-between justify-content-center flex-sm-row flex-column"
    >
      <div class="mr-3">
        Showing
        {{ Math.min((currentPage - 1) * perPage + 1, movementsCount) }} to
        {{ Math.min(currentPage * perPage, movementsCount) }} of
        {{ movementsCount }} entries
      </div>

      <b-pagination
        v-if="movementsCount > perPage"
        v-model="currentPage"
        :total-rows="movementsCount"
        :per-page="perPage"
        first-text="First"
        prev-text="Previous"
        next-text="Next"
        last-text="Last"
        class="mb-0"
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
  </b-card>
</template>

<script>
export default {
  props: {
    terminalId: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      movementsTableFields: [
        {
          key: 'name',
          label: 'Location',
        },
        {
          key: 'createdAt',
          label: 'Move Date',
          thStyle: { width: '15%' },
        },
      ],
      error: null,
      currentPage: 1,
      perPage: 5,
      movementsCount: 0,
      movements: null,
      isLoading: false,
    }
  },
  computed: {},
  watch: {
    currentPage() {
      this.getMovements()
    },
    perPage() {
      this.getMovements()
    },
  },
  created() {
    this.getMovements()
  },
  methods: {
    async getMovements() {
      try {
        if (this.isLoading) {
          return
        }
        this.error = null
        this.isLoading = true

        const movements = await this.$axios.post('/app/terminals/movements', {
          id: this.terminalId,
          offset: (this.currentPage - 1) * this.perPage,
          limit: this.perPage,
        })

        if (movements.data.success) {
          this.movements = movements.data.results
          this.movementsCount = movements.data.count
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
  },
}
</script>
