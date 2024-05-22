<template>
  <div
    class="d-flex align-items-center justify-content-center min-vw-100 min-vh-100"
  >
    <div>
      <div class="d-flex mb-1 justify-content-start" style="font-size: 1rem;">
        <nuxt-link
          :to="{
            name: 'sign-in',
          }"
          class="d-flex align-tems-center text-light"
        >
          <img
            src="~/assets/icons/arrow-left.svg"
            width="24"
            height="24"
            alt=""
            class="mr-1"
            style="cursor: pointer;"
          />
          Log-in
        </nuxt-link>
      </div>
      <b-card class="shadow-sm" style="width: 24rem;">
        <b-card-text>
          Enter your email address and we will send you a password reset link.
        </b-card-text>
        <b-form class="mt-3" @submit.prevent="onSubmit">
          <b-form-group label="E-mail" label-for="email">
            <b-form-input
              id="email"
              v-model.trim="user.email"
              :state="$v.user.email.$dirty ? !$v.user.email.$error : null"
              type="email"
              placeholder="Enter e-mail"
              autofocus
            />
          </b-form-group>

          <div v-if="typeof error === 'string'" class="mb-3 text-danger">
            {{ error }}
          </div>

          <BAlert :show="!!checkEmailMsg" variant="info">
            Check your email for a link to reset your password. If it doesn’t
            appear within a few minutes, check your spam folder.
          </BAlert>

          <div class="d-flex justify-content-end">
            <b-button type="submit" variant="primary" block
              >Send reset email</b-button
            >
          </div>
        </b-form>
      </b-card>
    </div>
  </div>
</template>

<script>
import { required, email } from 'vuelidate/lib/validators'

export default {
  layout: 'sign-in',
  data() {
    return {
      error: false,
      user: {
        email: '',
      },
      checkEmailMsg: false,
    }
  },
  validations: {
    user: {
      email: {
        required,
        email,
      },
    },
  },
  methods: {
    async onSubmit() {
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.error = false
          this.checkEmailMsg = false

          this.$nuxt.$loading.start()

          const { data } = await this.$axios.post('/app/password/forgot', {
            email: this.user.email,
          })

          this.$nuxt.$loading.finish()

          if (data.success) {
            this.checkEmailMsg = true
          }
        } catch (error) {
          this.$nuxt.$loading.finish()

          this.error = this.$getErrorMessage(error)
        }
      }
    },
  },
}
</script>
