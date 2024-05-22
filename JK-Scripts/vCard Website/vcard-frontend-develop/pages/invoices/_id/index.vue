<template>
  <div>
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between">
        <h3 class="mb-0">Invoice Details</h3>
      </div>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Invoices', link: '/invoices' },
          {
            title: 'Invoice Details',
          },
        ]"
      />
    </div>

    <b-card
      :header="`Invoice #${$route.params.id}`"
      class="mb-4 shadow-sm"
      no-body
    >
      <b-list-group flush>
        <b-list-group-item>
          <b-alert :show="typeof error === 'string'" variant="danger">
            {{ error }}
          </b-alert>

          <b-alert :show="typeof success === 'string'" variant="success">
            {{ success }}
          </b-alert>

          <div class="row">
            <div class="col-6 d-flex flex-column">
              <span>
                Period Start:
                <span
                  :class="{
                    'vue-quick-edit__link--is-clickable':
                      invoice.location.invoicingMode === 'blank',
                  }"
                  @click="changeInvoiceDates"
                >
                  {{ $dayjs(invoice.since, 'YYYY-MM-DD').format('MM/DD/YYYY') }}
                </span>
              </span>

              <span>
                Period End:
                <span
                  :class="{
                    'vue-quick-edit__link--is-clickable':
                      invoice.location.invoicingMode === 'blank',
                  }"
                  @click="changeInvoiceDates"
                >
                  {{ $dayjs(invoice.until, 'YYYY-MM-DD').format('MM/DD/YYYY') }}
                </span>
              </span>

              <span class="d-inline-flex align-items-center">
                Split Percent:&#x000A0;
                <quick-edit
                  v-if="$isAllowed('app/invoices/update')"
                  id="quick-edit-splitPercent"
                  v-model="invoice.splitPercent"
                  :required="true"
                  button-cancel-text="Cancel"
                  button-ok-text="Ok"
                  :classes="quickEditClasses"
                  :disabled="
                    invoice.status !== 'incomplete' &&
                    invoice.status !== 'unpaid'
                  "
                  type="number"
                  mode="ignore"
                  :min="0"
                  :max="100"
                  :validator="splitPercentValidate"
                  @input="rebuildSplitPercent"
                  @invalid="invalidSplitPercent"
                >
                  <template #default="{ value }">
                    <span>{{ value }}&#x00025;</span>
                  </template>
                </quick-edit>
                <b-popover
                  target="quick-edit-splitPercent"
                  triggers="manual"
                  placement="bottom"
                  variant="danger"
                  :show="invalidSplitPercentMsg"
                  content="The field must not be empty and the value must be in the range from 0 to 100"
                ></b-popover>

                <span v-if="!$isAllowed('app/invoices/update')">
                  {{ invoice.splitPercent }}&#x00025;
                </span>
              </span>
              <span>Balance Due: {{ $getFormattedPrice(needToPay) }}</span>
            </div>
            <div class="col-6 d-flex flex-column">
              <span>{{ invoice.location.name }}</span>

              <span>{{ invoice.location.address }}</span>

              <span
                >{{ invoice.location.city }},
                <span class="text-uppercase">{{ invoice.location.state }}</span
                >, {{ invoice.location.zipCode }}</span
              >
            </div>
          </div>
        </b-list-group-item>

        <b-list-group-item v-if="$isAllowed('app/invoices/pay')">
          <b-input-group prepend="$">
            <b-form-input
              v-model.number="newPay"
              :disabled="needToPay <= 0"
              :state="$v.newPay.$dirty ? !$v.newPay.$error : null"
              type="number"
              min="0"
              :max="needToPay"
              step="1"
              placeholder="Enter payment amount"
            />

            <b-form-invalid-feedback v-if="$v.newPay.$error">
              Please specify amount between 0 and {{ needToPay }}.
            </b-form-invalid-feedback>
          </b-input-group>

          <b-input-group class="mt-2">
            <b-form-input
              v-model="newPayNotes"
              :disabled="needToPay <= 0"
              :state="$v.newPayNotes.$dirty ? !$v.newPayNotes.$error : null"
              placeholder="Notes"
            />
          </b-input-group>

          <div class="mt-2 text-right">
            <b-button
              type="button"
              variant="primary"
              class="ml-auto"
              :disabled="needToPay <= 0"
              @click="addPay"
            >
              Add New Payment
            </b-button>
          </div>
        </b-list-group-item>

        <b-list-group-item>
          <h6 class="mb-3">Payments</h6>

          <b-table
            :fields="paymentsTableFields"
            :items="invoice.payments"
            head-variant="light"
            :empty-text="isLoading ? '' : 'No data available in table'"
            show-empty
            responsive
            bordered
            striped
            foot-clone
          >
            <template #cell(createdAt)="data">
              {{ $dayjs(data.item.createdAt).format('MM/DD/YYYY h:mm A') }}
            </template>

            <template #cell(amount)="data">
              {{ $getFormattedPrice(data.item.amount) }}
            </template>

            <template #cell(actions)="data">
              <div
                class="d-flex flex-nowrap align-items-center"
                style="margin: -0.25rem;"
              >
                <EditButton class="mb-2 mr-2" @click="editPayment(data.item)" />

                <DeleteButton class="mb-2" @click="deletePayment(data.item)" />
              </div>
            </template>

            <template #foot()>
              {{ '' }}
            </template>

            <template #foot(createdAt)>
              <div class="text-right">Total:</div>
            </template>

            <template #foot(amount)>
              <div class="text-left">
                {{ $getFormattedPrice(alreadyPay) }}
              </div>
            </template>
          </b-table>
        </b-list-group-item>

        <b-list-group-item>
          <invoice-items :invoice="invoice" @update="invoice = $event" />
        </b-list-group-item>
      </b-list-group>

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

    <PaymentModal ref="payment-modal" @updated="getInvoice" />

    <InvoiceCreationModal
      ref="invoice-creation-modal"
      :location-id="invoice.location.id"
      :invoice-id="invoice.id"
      :since="invoice.since"
      :until="invoice.until"
      @updated="onInvoiceUpdated"
    />
  </div>
</template>

<script>
import { required, minValue, maxValue } from 'vuelidate/lib/validators'
import QuickEdit from 'vue-quick-edit'
import { BPopover } from 'bootstrap-vue'
import EditButton from '@/components/buttons/EditButton'
import DeleteButton from '@/components/buttons/DeleteButton'
import Breadcrumb from '~/components/Breadcrumb'
import PaymentModal from '~/components/modals/Payment'
import InvoiceItems from '~/components/InvoiceItems'
import InvoiceCreationModal from '~/components/modals/InvoiceCreation'

export default {
  components: {
    Breadcrumb,
    PaymentModal,
    QuickEdit,
    BPopover,
    InvoiceItems,
    EditButton,
    DeleteButton,
    InvoiceCreationModal,
  },
  async asyncData({ $axios, $getErrorMessage, route }) {
    try {
      const { data } = await $axios.post('/app/invoices/get', {
        id: +route.params.id,
      })

      if (data.success) {
        return {
          invoice: data.invoice,
          newInvoiceNotes: data.invoice.notes,
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
      isLoading: false,
      invalidSplitPercentMsg: false,
      invalidTotalInMsg: false,
      invalidTotalOutMsg: false,
      error: null,
      success: null,
      invoice: null,
      newInvoiceNotes: null,
      newPay: null,
      newPayNotes: null,
      paymentsTableFields: [
        {
          key: 'createdAt',
          label: 'Created At',
        },
        {
          key: 'amount',
          label: 'Amount',
        },
        {
          key: 'notes',
          label: 'Notes',
        },
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
      quickEditClasses: {
        wrapper: 'form-inline',
        input: 'form-control input-sm mr-2',
        buttonOk: 'btn btn-primary',
        buttonCancel: 'btn btn-outline-light',
      },
      quickEditInvoiceClasses: {
        wrapper: 'form-inline',
        input: 'form-control input-sm mr-2',
        buttonOk: 'btn btn-primary btn-sm',
        buttonCancel: 'btn btn-outline-light btn-sm',
      },
    }
  },
  validations() {
    return {
      newPay: {
        required,
        minValue: minValue(0),
        maxValue: maxValue(this.needToPay),
      },
      newPayNotes: {},
    }
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
  methods: {
    async getInvoice() {
      try {
        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/invoices/get', {
          id: +this.$route.params.id,
        })

        if (data.success) {
          this.invoice = data.invoice
          this.newInvoiceNotes = this.invoice.notes
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async addPay() {
      this.error = null
      this.success = null

      this.$v.$touch()

      if (!this.$v.newPay.$invalid && !this.$v.newPayNotes.$invalid) {
        try {
          this.isLoading = true

          const { data } = await this.$axios.post('/app/invoices/pay', {
            id: this.$route.params.id,
            amount: this.newPay,
            notes: this.newPayNotes,
          })

          if (data.success) {
            this.newPay = null
            this.newPayNotes = null

            this.$v.$reset()

            this.getInvoice()

            this.success = 'Payment successfully completed'
          }

          this.isLoading = false
        } catch (error) {
          this.error = this.$getErrorMessage(error)

          this.isLoading = false
        }
      }
    },
    editPayment(payment) {
      this.$refs['payment-modal'].showModal(payment)
    },
    deletePayment(payment) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete ${this.$getFormattedPrice(
            payment.amount
          )} payment created on ${this.$dayjs(payment.createdAt).format(
            'MM/DD/YYYY h:mm A'
          )}?`,
          {
            title: 'Delete Payment',
            headerBgVariant: 'danger',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post('/app/payments/delete', {
                id: payment.id,
              })

              this.isLoading = false

              if (data.success) {
                this.getInvoice()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
    splitPercentValidate(event) {
      return +event === 0 || +event > 100
    },
    totalInValidate(event) {
      return +event === 0
    },
    async rebuildSplitPercent(event) {
      this.invalidSplitPercentMsg = false
      try {
        this.isLoading = true
        const { data } = await this.$axios.post('/app/invoices/rebuild', {
          id: this.$route.params.id,
          splitPercent: +event,
        })
        if (data.success) {
          this.invoice = data.invoice
        }
        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    changeTotalIn(id, newValue) {
      const index = this.invoice.invoiceItems.findIndex(
        (item) => item.id === id
      )
      const currentValue = this.invoice.invoiceItems[index].totalIn

      this.$set(this.invoice.invoiceItems, index, {
        ...this.invoice.invoiceItems[index],
        totalIn: +newValue,
      })

      this.confirmChanges().then((value) => {
        this.$set(this.invoice.invoiceItems, index, {
          ...this.invoice.invoiceItems[index],
          totalIn: value ? +newValue : currentValue,
        })

        if (value) {
          this.rebuildInvoice(id, newValue, 'totalIn')
        }
      })
    },
    changeTotalOut(id, newValue) {
      const index = this.invoice.invoiceItems.findIndex(
        (item) => item.id === id
      )
      const currentValue = this.invoice.invoiceItems[index].totalOut

      this.$set(this.invoice.invoiceItems, index, {
        ...this.invoice.invoiceItems[index],
        totalOut: +newValue,
      })

      this.confirmChanges().then((value) => {
        this.$set(this.invoice.invoiceItems, index, {
          ...this.invoice.invoiceItems[index],
          totalOut: value ? +newValue : currentValue,
        })

        if (value) {
          this.rebuildInvoice(id, newValue, 'totalOut')
        }
      })
    },
    confirmChanges() {
      return this.$bvModal
        .msgBoxConfirm('Are you sure you want to make this change?', {
          title: 'Please confirm',
          headerBgVariant: 'primary',
          centered: true,
          okTitle: 'Okay',
        })
        .catch((error) => {
          console.error(error)
        })
    },
    async rebuildInvoice(id, newValue, field) {
      this.invalidTotalInMsg = false
      try {
        this.isLoading = true
        const update = await this.$axios.post('/app/invoice-items/update', {
          id,
          invoiceItem: {
            [field]: +newValue,
          },
        })
        if (update.data.success) {
          await this.getInvoice()
        }
        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    invalidSplitPercent() {
      this.invalidSplitPercentMsg = true
      setTimeout(() => (this.invalidSplitPercentMsg = false), 5000)
    },
    invalidTotalIn() {
      this.invalidTotalInMsg = true
      setTimeout(() => (this.invalidTotalInMsg = false), 5000)
    },
    invalidTotalOut() {
      this.invalidTotalOutMsg = true
      setTimeout(() => (this.invalidTotalOutMsg = false), 5000)
    },
    changeInvoiceDates() {
      if (this.invoice.location.invoicingMode === 'blank') {
        this.$refs['invoice-creation-modal'].showModal()
      }
    },
    onInvoiceUpdated(invoice) {
      this.invoice = invoice
    },
  },
}
</script>
<style lang="scss" scoped>
.popover {
  left: -45px !important;
  max-width: 280px;
}
</style>
