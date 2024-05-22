<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">
          Revenue Report
        </h6>

        <div
          class="revenue-report__date-picker d-flex flex-sm-nowrap flex-wrap"
        >
          <span class="mr-2">Select a date range:</span>

          <DatePicker
            v-model="dateRange"
            :editable="false"
            :clearable="false"
            :range="true"
            format="MM/DD/YYYY hh:mm A"
            :show-time-panel="showTimeRangePanel"
            type="datetime"
            placeholder="Select a date range"
            style="width: 340px;"
            use12h
          >
            <template #footer>
              <button class="mx-btn mx-btn-text" @click="toggleTimeRangePanel">
                {{ showTimeRangePanel ? 'select date' : 'select time' }}
              </button>
            </template>
          </DatePicker>
        </div>
      </div>
    </template>

    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="d-flex align-items-center justify-content-between mb-3">
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
      :value="currentPage"
      :total-rows="terminalsCount"
      :per-page="perPage"
      first-text="First"
      prev-text="Previous"
      next-text="Next"
      last-text="Last"
      pills
      @change="onCurrentPageChanged"
    />
    <b-table
      :fields="terminalTableFields"
      :items="terminals"
      :foot-clone="isFullMode"
      head-variant="light"
      thead-class="text-uppercase"
      foot-variant="white"
      :empty-text="isLoading ? '' : 'No data available in table'"
      show-empty
      responsive
      bordered
      striped
    >
      <template #cell(cashIn)="data">
        {{ $getFormattedPrice(data.item.cashIn) }}
      </template>
      <template #cell(sales)="data">
        {{ $getFormattedPrice(data.item.sales) }}
      </template>

      <template #cell(cashOut)="data">
        {{ $getFormattedPrice(data.item.cashOut) }}
      </template>

      <template #cell(netCash)="data">
        {{ $getFormattedPrice(data.item.netCash) }}
      </template>

      <template #cell(operatorProfit)="data">
        {{ $getFormattedPrice(data.item.operatorProfit) }}
      </template>

      <template #cell(dailyCashIn)="data">
        {{ $getFormattedPrice(data.item.dailyCashIn) }}
      </template>

      <template #cell(dailyCashOut)="data">
        {{ $getFormattedPrice(data.item.dailyCashOut) }}
      </template>

      <template #cell(dailyNet)="data">
        {{ $getFormattedPrice(data.item.dailyNet) }}
      </template>

      <template #cell(lastActivityAt)="data">
        <span v-if="data.item.lastActivityAt">
          {{ $dayjs(data.item.lastActivityAt).format('MM/DD/YYYY h:mm A') }}
        </span>

        <div v-if="!data.item.lastActivityAt" class="d-flex align-items-center">
          <div
            class="mr-1 bg-danger rounded"
            style="width: 0.5rem; height: 0.5rem; margin-top: 1px;"
          ></div>
          <span>never</span>
        </div>
      </template>

      <template #foot()>
        {{ '' }}
      </template>

      <template #foot(gameName)>
        <div class="text-right">Total:</div>
      </template>

      <template #foot(cashIn)>
        <div class="text-left">
          {{ terminalsTotals && $getFormattedPrice(terminalsTotals.cashIn) }}
        </div>
      </template>
      <template #foot(sales)>
        <div class="text-left">
          {{ terminalsTotals && $getFormattedPrice(terminalsTotals.sales) }}
        </div>
      </template>

      <template #foot(cashOut)>
        <div class="text-left">
          {{ terminalsTotals && $getFormattedPrice(terminalsTotals.cashOut) }}
        </div>
      </template>

      <template #foot(netCash)>
        <div class="text-left">
          {{ terminalsTotals && $getFormattedPrice(terminalsTotals.netCash) }}
        </div>
      </template>

      <template #foot(operatorProfit)>
        <div class="text-left">
          {{
            terminalsTotals &&
            $getFormattedPrice(terminalsTotals.operatorProfit)
          }}
        </div>
      </template>
    </b-table>

    <div
      class="d-flex align-items-center justify-content-sm-between justify-content-center flex-sm-row flex-column"
    >
      <div class="mr-3">
        Showing
        {{ Math.min((currentPage - 1) * perPage + 1, terminalsCount) }} to
        {{ Math.min(currentPage * perPage, terminalsCount) }} of
        {{ terminalsCount }} entries
      </div>

      <b-pagination
        v-if="terminalsCount > perPage"
        :value="currentPage"
        :total-rows="terminalsCount"
        :per-page="perPage"
        first-text="First"
        prev-text="Previous"
        next-text="Next"
        last-text="Last"
        class="mb-0"
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
import DatePicker from 'vue2-datepicker'

export default {
  components: {
    DatePicker,
  },
  props: {
    location: {
      type: Object,
      required: true,
    },
    locationId: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      dateRange: [
        this.$dayjs().isoWeekday(1).startOf('day').toDate(),
        this.$dayjs().isoWeekday(7).endOf('day').toDate(),
      ],
      showTimeRangePanel: false,
      terminals: null,
      terminalsTotals: null,
      terminalsCount: 0,
      currentPage: 1,
      perPage: 50,
      isLoading: false,
      error: null,
    }
  },
  computed: {
    isFullMode() {
      return this.$isAllowed('app/locations/get+all')
    },
    terminalTableFields() {
      if (!this.isFullMode && this.location && this.location.isLive) {
        return [
          {
            key: 'deviceId',
            label: '#',
          },
          {
            key: 'gameName',
            label: 'Game Title',
          },
          {
            key: 'lastActivityAt',
            label: 'Last Activity',
          },
        ]
      }

      return [
        {
          key: 'deviceId',
          label: '#',
        },
        {
          key: 'cabinet',
          label: 'Cabinet',
        },
        {
          key: 'gameName',
          label: 'Game Title',
        },
        {
          key: 'cashIn',
          label: 'Total In',
        },
        {
          key: 'sales',
          label: 'Sales',
        },
        {
          key: 'cashOut',
          label: 'Total Out',
        },
        {
          key: 'netCash',
          label: 'Revenue',
        },
        {
          key: 'operatorProfit',
          label: 'Operator Profit',
        },
        {
          key: 'dailyCashIn',
          label: 'Daily Cash In',
        },
        {
          key: 'dailyCashOut',
          label: 'Daily Cash Out',
        },
        {
          key: 'dailyNet',
          label: 'Daily Net',
        },
        {
          key: 'lastActivityAt',
          label: 'Last Activity',
        },
      ]
    },
  },
  watch: {
    dateRange() {
      this.getTerminals()
    },
    perPage() {
      this.getTerminals()
    },
  },
  created() {
    this.getTerminals()
  },
  methods: {
    toggleTimeRangePanel() {
      this.showTimeRangePanel = !this.showTimeRangePanel
    },
    async getTerminals() {
      try {
        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/locations/revenue', {
          since: this.$dayjs(this.dateRange[0]).format('YYYY-MM-DD HH:mm'),
          until: this.$dayjs(this.dateRange[1]).format('YYYY-MM-DD HH:mm'),
          locationId: this.locationId,
          offset: (this.currentPage - 1) * this.perPage,
          limit: this.perPage,
        })

        if (data.success) {
          this.terminals = data.results
          this.terminalsTotals = data.totals
          this.terminalsCount = data.count
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    onCurrentPageChanged(page) {
      this.currentPage = page

      this.getTerminals()
    },
  },
}
</script>

<style lang="scss" scoped>
.revenue-report {
  &__date-picker {
    align-items: center;

    @media (max-width: 767px) {
      flex-direction: column;
      align-items: flex-start;
    }
  }
}
</style>
