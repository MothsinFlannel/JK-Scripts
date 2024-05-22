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

    <printed-invoice v-if="typeof error !== 'string'" :invoice="invoice" />
  </div>
</template>

<script>
import PrintedInvoice from '@/components/PrintedInvoice'

export default {
  components: {
    PrintedInvoice,
  },
  layout: 'invoice',
  async asyncData({ $axios, $getErrorMessage, route }) {
    try {
      const { data } = await $axios.post('/app/invoices/view', {
        token: route.params.token,
      })

      if (data.success) {
        const invoiceItems = data.invoice.invoiceItems

        return {
          invoice: { ...data.invoice, invoiceItems },
        }
      }
    } catch (error) {
      return {
        error: $getErrorMessage(error),
      }
    }
  },
  data() {
    return {
      error: null,
      invoice: null,
    }
  },
  mounted() {
    setTimeout(this.printPage, 1000)
  },
  methods: {
    printPage() {
      window.print()
    },
  },
}
</script>

<style lang="scss">
@media print {
  body {
    color: #000 !important;
  }
}
</style>
