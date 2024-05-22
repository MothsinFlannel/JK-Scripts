<template>
  <b-modal
    ref="reset-password-modal"
    title="Reset password"
    ok-title="Reset"
    header-bg-variant="primary"
    header-text-variant="white"
    size="md"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-12">
        <b-form-group label="Password">
          <b-form-input
            v-model.trim="password"
            :state="$v.password.$dirty ? !$v.password.$error : null"
            placeholder="Enter password"
            type="password"
            autofocus
          />
        </b-form-group>
      </div>

      <div class="col-12">
        <b-form-group label="Repeat Password">
          <b-form-input
            v-model.trim="rePassword"
            :state="$v.rePassword.$dirty ? !$v.rePassword.$error : null"
            placeholder="Enter password"
            type="password"
          />
        </b-form-group>
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
      password: null,
      rePassword: null,
      user: null,
    }
  },
  validations: {
    password: {
      required,
    },
    rePassword: {
      required,
      coincide(value, { password }) {
        return value === password
      },
    },
  },
  methods: {
    show(user) {
      this.error = null
      this.password = null
      this.rePassword = null
      this.user = user

      this.$v.$reset()

      this.$refs['reset-password-modal'].show()
    },
    async onSubmit() {
      this.error = null

      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const { data } = await this.$axios.post('/app/password/reset', {
            userId: this.user.id,
            password: this.password,
          })

          if (data.success) {
            this.$emit('updated', this.user)

            this.$refs['reset-password-modal'].hide()
          }
        } catch (error) {
          this.error = this.$getErrorMessage(error)
        } finally {
          this.$nuxt.$loading.finish()
        }
      }
    },
  },
}
</script>
