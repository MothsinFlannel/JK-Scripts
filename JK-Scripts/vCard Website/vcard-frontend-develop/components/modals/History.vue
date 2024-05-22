<template>
  <b-modal
    ref="history-modal"
    title="History of a POS"
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
      :fields="tableFields"
      :items="history"
      :is-loading="isLoading"
      head-variant="light"
      thead-class="text-uppercase"
      foot-variant="white"
      :empty-text="isLoading ? '' : 'No data available in table'"
      show-empty
      responsive
      bordered
      striped
    >
    </b-table>

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
  </b-modal>
</template>

<script>
export default {
  data() {
    return {
      error: null,
      history: null,
      isLoading: false,
      tableFields: [
        {
          key: 'location.name',
          label: 'Location Name',
        },
        {
          key: 'location.address',
          label: 'Address',
        },
        {
          key: 'location.city',
          label: 'City',
        },
        {
          key: 'location.state',
          label: 'State',
        },
        {
          key: 'location.contactPhone',
          label: 'Contact Phone',
        },
      ],
    }
  },
  methods: {
    open(attribute, value) {
      this.history = null

      this.getHistory(attribute, value)

      this.$refs['history-modal'].show()
    },
    async getHistory(attribute, value) {
      try {
        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/audit/by-attribute', {
          attribute,
          value,
        })

        if (data.success) {
          this.history = data.results
        }
      } catch (error) {
        this.error = this.$getErrorMessage(error)
      } finally {
        this.isLoading = false
      }
    },
  },
}
</script>
