<template>
  <b-card header="Change Password" class="mb-4 shadow-sm">
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-alert :show="typeof success === 'string'" variant="success" dismissible>
      {{ success }}
    </b-alert>

    <b-form @submit.prevent="changePassword">
      <div class="row">
        <div class="col-12">
          <b-form-group label="Password*">
            <b-form-input
              v-model.trim="password"
              :state="$v.password.$dirty ? !$v.password.$error : null"
              type="password"
              placeholder="Enter Password"
            />

            <b-form-invalid-feedback v-if="!$v.password.required">
              Please provide a password.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b-form-group label="Repeat Password*">
            <b-form-input
              v-model.trim="rePassword"
              :state="$v.rePassword.$dirty ? !$v.rePassword.$error : null"
              type="password"
              placeholder="Repeat Password"
            />

            <b-form-invalid-feedback v-if="!$v.rePassword.required">
              Please provide a password.
            </b-form-invalid-feedback>

            <b-form-invalid-feedback v-else-if="!$v.rePassword.sameAs">
              Passwords are not identical.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <b-button type="submit" variant="primary">
        Change Password
      </b-button>
    </b-form>
  </b-card>
</template>

<script>
import { required, sameAs } from 'vuelidate/lib/validators'

export default {
  data() {
    return {
      error: null,
      success: null,
      password: null,
      rePassword: null,
    }
  },
  validations: {
    password: {
      required,
    },
    rePassword: {
      required,
      sameAs: sameAs('password'),
    },
  },
  methods: {
    async changePassword() {
      this.error = null
      this.success = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const { data } = await this.$axios.post('/app/account/update', {
            user: {
              password: this.password,
            },
          })

          if (data.success) {
            this.password = null
            this.rePassword = null

            this.success = 'Password updated'

            this.$v.$reset()
          }

          this.$nuxt.$loading.finish()
        } catch (error) {
          this.error = this.getErrorMessage(error)

          this.$nuxt.$loading.finish()
        }
      }
    },
  },
}
</script>
