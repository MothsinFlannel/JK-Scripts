<template>
  <div
    class="container min-vh-100 d-flex flex-column py-4"
    style="page-break-before: always;"
  >
    <div class="d-flex align-items-center justify-content-between">
      <div class="text-uppercase font-weight-bold" style="font-size: 2rem;">
        vCard
      </div>

      <div>app.vcardsys.com</div>
    </div>

    <div style="margin: 6rem 0;">
      <div>
        <h1 style="color: #71748d;">
          {{ `Invoice #${invoice.id}` }}
        </h1>
      </div>

      <div class="border-top border-bottom py-3">
        <div class="row">
          <div class="col-6 d-flex flex-column">
            <span
              >Period Start:
              {{
                $dayjs(invoice.since, 'YYYY-MM-DD').format('MM/DD/YYYY')
              }}</span
            >

            <span
              >Period End:
              {{
                $dayjs(invoice.until, 'YYYY-MM-DD').format('MM/DD/YYYY')
              }}</span
            >

            <span
              >Balance Due:
              <span class="font-weight-bold">{{
                $getFormattedPrice(needToPay || 0)
              }}</span></span
            >
          </div>

          <div class="col-6 d-flex flex-column">
            <span class="font-weight-bold">{{ invoice.location.name }}</span>

            <span>{{ invoice.location.address }}</span>

            <span
              >{{ invoice.location.city }},
              <span class="text-uppercase">{{ invoice.location.state }}</span
              >, {{ invoice.location.zipCode }}</span
            >
          </div>
        </div>
      </div>
    </div>

    <b-table-simple responsive>
      <b-tbody>
        <b-tr style="border-bottom: 2px solid #dee2e6;" class="text-uppercase">
          <b-th>#</b-th>
          <b-th>Terminal</b-th>
          <b-th>Total In</b-th>
          <b-th>Total Out</b-th>
          <b-th>Revenue</b-th>
          <b-th>Notes</b-th>
        </b-tr>

        <b-tr
          v-if="
            !invoice.invoiceItems ||
            (invoice.invoiceItems && invoice.invoiceItems.length === 0)
          "
          class="b-table-empty-row"
        >
          <b-td colspan="6">
            <div class="text-center my-2">
              No data available in table
            </div>
          </b-td>
        </b-tr>

        <template
          v-if="invoice.invoiceItems && invoice.invoiceItems.length > 0"
        >
          <b-tr v-for="(item, index) in invoice.invoiceItems" :key="index">
            <b-td>{{ item.number }}</b-td>
            <b-td>{{ item.title }}</b-td>
            <b-td>{{ $getFormattedPrice(item.totalIn || 0) }}</b-td>
            <b-td>{{ $getFormattedPrice(item.totalOut || 0) }}</b-td>
            <b-td>{{ $getFormattedPrice(item.revenue || 0) }}</b-td>
            <b-td>{{ item.notes }}</b-td>
          </b-tr>

          <b-tr>
            <b-td colspan="2" class="text-right"></b-td>
            <b-td>{{ $getFormattedPrice(invoice.totals.totalIn || 0) }}</b-td>
            <b-td>{{ $getFormattedPrice(invoice.totals.totalOut || 0) }}</b-td>
            <b-td>{{ $getFormattedPrice(invoice.totals.revenue || 0) }}</b-td>
            <b-td>&nbsp;</b-td>
          </b-tr>

          <b-tr>
            <b-td colspan="4" style="vertical-align: top;">
              Notes: {{ invoice.notes }}
            </b-td>
            <b-td colspan="2" class="text-right">
              <div>
                Revenue to split:
                {{ $getFormattedPrice(invoice.totals.revenue || 0) }}
              </div>
              <div>
                Due to Location:
                {{
                  $getFormattedPrice(
                    invoice.totals.revenue - invoice.totals.balance || 0
                  )
                }}
              </div>
              <div>
                Due to Company:
                {{ $getFormattedPrice(invoice.totals.balance || 0) }}
              </div>
            </b-td>
          </b-tr>
        </template>
      </b-tbody>
    </b-table-simple>

    <div class="mt-auto text-center" style="padding-top: 6rem;">
      Payment is due upon receipt. Thank you for your business!
    </div>
  </div>
</template>

<script>
export default {
  props: {
    invoice: {
      type: Object,
      required: true,
    },
  },
  computed: {
    alreadyPay() {
      const alreadyPay = this.invoice.payments.reduce((result, value) => {
        return result + value.amount
      }, 0)

      return +alreadyPay.toFixed(2)
    },
    needToPay() {
      const alreadyPay = this.invoice.payments.reduce((result, value) => {
        return result + value.amount
      }, 0)

      return +(this.invoice.amount - alreadyPay).toFixed(2)
    },
  },
}
</script>
