<template>
  <b-modal
    ref="movementHistory-modal"
    :title="`Movement history terminal ${terminal.number}`"
    header-bg-variant="primary"
    header-text-variant="white"
    size="lg"
    no-close-on-backdrop
    centered
    ok-only
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>
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
      class="d-flex align-items-center justify-content-sm-end justify-content-center flex-sm-row flex-column"
    >
      <b-pagination
        v-if="movementsCount > 5"
        v-model="currentPage"
        :total-rows="movementsCount"
        :per-page="5"
        first-text="First"
        prev-text="Previous"
        next-text="Next"
        last-text="Last"
        class="mb-0"
        pills
      />
    </div>
  </b-modal>
</template>

<script>
export default {
  data() {
    return {
      error: null,
      currentPage: 1,
      terminal: {},
      movements: null,
      movementsCount: 0,
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
    }
  },
  watch: {
    currentPage() {
      this.getMovements()
    },
  },
  methods: {
    async showModal(terminal) {
      this.error = null

      this.terminal = Object.assign({}, terminal)

      await this.getMovements()

      this.$refs['movementHistory-modal'].show()
    },
    async getMovements() {
      try {
        this.$nuxt.$loading.start()

        const movements = await this.$axios.post('/app/terminals/movements', {
          id: this.terminal.id,
          offset: (this.currentPage - 1) * 5,
          limit: 5,
        })

        if (movements.data.success) {
          this.movements = movements.data.results
          this.movementsCount = movements.data.count
        }

        this.$nuxt.$loading.finish()
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.$nuxt.$loading.finish()
      }
    },
    onSubmit() {
      this.$refs['movementHistory-modal'].hide()
    },
  },
}
</script>
