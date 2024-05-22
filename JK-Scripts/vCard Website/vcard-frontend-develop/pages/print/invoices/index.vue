<template>
  <div class="w-100 min-vh-100 bg-white" style="font-size: 1.15em;">
    <div
      v-if="typeof error === 'string'"
      class="container min-vh-100 d-flex flex-column py-4"
    >
      <b-alert variant="danger">
        {{ error }}
      </b-alert>
    </div>

    <template v-if="typeof error !== 'string'">
      <printed-invoice
        v-for="invoice of invoices"
        :key="invoice.id"
        :invoice="invoice"
      />
    </template>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import PrintedInvoice from '@/components/PrintedInvoice'

export default {
  components: {
    PrintedInvoice,
  },
  layout: 'print',
  data() {
    return {
      error: null,
      invoices: null,
    }
  },
  computed: {
    ...mapGetters({
      token: 'user/token',
    }),
  },
  created() {
    this.fetchInvoices()
  },
  methods: {
    async fetchInvoices() {
      try {
        const headers = {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${this.token}`,
        }

        const { data } = await this.$axios.post(
          '/render/invoices/print',
          {
            query: this.$route.query.query,
            routeId: this.$route.query.route,
            locationId: this.$route.query.location
              ? +this.$route.query.location
              : null,
            status: this.$route.query.status,
            since: this.$route.query.since
              ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
              : null,
            ids: this.$route.query.ids
              ? this.$route.query.ids.split('&')
              : null,

            offset: 0,
            limit: -1,
            extraFields: true,
          },
          { headers }
        )

        document.write(data)
      } catch (error) {
        this.error = this.$getErrorMessage(error)
      }
    },
  },
}
</script>

<style lang="scss">
@media print {
  body {
    color: black !important;
  }

  ::-webkit-scrollbar {
    width: 0;
  }
}
</style>
