<template>
  <b-modal
    ref="payment-modal"
    title="Edit payment"
    ok-title="Save"
    header-bg-variant="primary"
    header-text-variant="white"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div v-if="payment" class="row">
      <div class="col-12">
        <b-input-group prepend="$">
          <b-form-input
            v-model.number="payment.amount"
            :state="$v.payment.amount.$dirty ? !$v.payment.amount.$error : null"
            type="number"
            min="0"
            step="1"
            placeholder="Enter payment amount"
          />

          <b-form-invalid-feedback v-if="$v.payment.amount.$error">
            Please enter a valid amount value.
          </b-form-invalid-feedback>
        </b-input-group>

        <b-input-group class="mt-2">
          <b-form-input
            v-model="payment.notes"
            :state="$v.payment.notes.$dirty ? !$v.payment.notes.$error : null"
            placeholder="Notes"
          />
        </b-input-group>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  data() {
    return {
      error: null,
      payment: null,
    }
  },
  validations: {
    payment: {
      amount: {
        required,
      },
      notes: {},
    },
  },
  methods: {
    showModal(payment) {
      this.error = null

      this.payment = { ...payment }

      this.$v.$reset()

      this.$refs['payment-modal'].show()
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const { data } = await this.$axios.post('/app/payments/update', {
            id: this.payment.id,
            payment: this.payment,
          })

          if (data.success) {
            this.$emit('updated')

            this.$refs['payment-modal'].hide()
          }

          this.$nuxt.$loading.finish()
        } catch (error) {
          this.error = this.$getErrorMessage(error)

          this.$nuxt.$loading.finish()
        }
      }
    },
  },
}
</script>
