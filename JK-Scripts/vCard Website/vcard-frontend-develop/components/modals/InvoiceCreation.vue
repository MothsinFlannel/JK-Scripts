<template>
  <b-modal
    ref="invoice-creation-modal"
    :title="invoiceId ? 'Update Invoice' : 'Create Invoice'"
    :ok-title="invoiceId ? 'Update' : 'Create'"
    header-bg-variant="primary"
    header-text-variant="white"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <legend class="bv-no-focus-ring col-form-label pt-0">
            Date range
          </legend>

          <DatePicker
            v-model="dateRange"
            type="date"
            format="MM/DD/YYYY"
            placeholder="Select date range"
            class="date-picker"
            :input-class="{
              'form-control': true,
              'is-invalid': $v.dateRange.$dirty && $v.dateRange.$error,
              'is-valid': $v.dateRange.$dirty && !$v.dateRange.$error,
            }"
            style="width: 100%;"
            range
          />
        </div>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required, minLength } from 'vuelidate/lib/validators'
import DatePicker from 'vue2-datepicker'

export default {
  components: {
    DatePicker,
  },
  props: {
    locationId: {
      type: Number,
      required: true,
    },
    invoiceId: {
      type: Number,
      default: null,
    },
    since: {
      type: String,
      default: null,
    },
    until: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      error: null,
      dateRange: [],
    }
  },
  validations: {
    dateRange: {
      required,
      minLength: minLength(2),
      filled: (value) => !value.includes(null),
    },
  },
  methods: {
    showModal() {
      this.error = null
      this.dateRange = []

      if (this.since && this.until) {
        this.dateRange.push(this.$dayjs(this.since, 'YYYY-MM-DD').toDate())
        this.dateRange.push(this.$dayjs(this.until, 'YYYY-MM-DD').toDate())
      }

      this.$v.$reset()

      this.$refs['invoice-creation-modal'].show()
    },
    async onSubmit() {
      try {
        this.error = null

        this.$v.$touch()

        if (!this.$v.$invalid) {
          this.$nuxt.$loading.start()

          if (this.invoiceId) {
            const { data } = await this.$axios.post('/app/invoices/update', {
              id: this.invoiceId,
              invoice: {
                since: this.$dayjs(this.dateRange[0]).format(
                  'YYYY-MM-DD HH:mm:ss'
                ),
                until: this.$dayjs(this.dateRange[1]).format(
                  'YYYY-MM-DD HH:mm:ss'
                ),
              },
            })

            this.$emit('updated', data.invoice)
          } else {
            const { data } = await this.$axios.post('/app/invoices/create', {
              invoice: {
                since: this.$dayjs(this.dateRange[0]).format(
                  'YYYY-MM-DD HH:mm:ss'
                ),
                until: this.$dayjs(this.dateRange[1]).format(
                  'YYYY-MM-DD HH:mm:ss'
                ),
                locationId: this.locationId,
              },
            })

            this.$router.push(`/invoices/${data.invoice.id}`)
          }

          this.$refs['invoice-creation-modal'].hide()
        }
      } catch (error) {
        this.error = this.$getErrorMessage(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
  },
}
</script>
