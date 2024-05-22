<template>
  <div
    class="d-flex align-items-center justify-content-center min-vw-100 min-vh-100"
  >
    <div>
      <b-card class="shadow-sm" style="width: 24rem;">
        <b-form @submit.prevent="onSubmit">
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

          <b-form-group label="Password" label-for="password">
            <b-form-input
              id="password"
              v-model.trim="user.password"
              :state="$v.user.password.$dirty ? !$v.user.password.$error : null"
              type="password"
              placeholder="Enter password"
            />
          </b-form-group>

          <div v-if="typeof error === 'string'" class="mb-3 text-danger">
            {{ error }}
          </div>

          <div class="d-flex justify-content-end">
            <b-button type="submit" variant="primary" block>Sign in</b-button>
          </div>
        </b-form>
      </b-card>

      <div class="d-flex mt-1 justify-content-end" style="font-size: 1rem;">
        <nuxt-link
          :to="{
            name: 'password-forgot',
          }"
          class="d-flex align-tems-center text-light"
        >
          Forgot your password?
          <img
            src="~/assets/icons/arrow-right.svg"
            width="24"
            height="24"
            alt=""
            class="ml-1"
          />
        </nuxt-link>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { required, email } from 'vuelidate/lib/validators'

export default {
  layout: 'sign-in',
  data() {
    return {
      error: false,
      user: {
        email: '',
        password: '',
      },
    }
  },
  validations: {
    user: {
      email: {
        required,
        email,
      },
      password: {
        required,
      },
    },
  },
  methods: {
    ...mapActions({
      login: 'user/login',
    }),
    async onSubmit() {
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.error = false

          this.$nuxt.$loading.start()

          const { data } = await this.$axios.post('/app/account/sign-in', {
            email: this.user.email,
            password: this.user.password,
          })

          this.$nuxt.$loading.finish()

          if (data.success) {
            this.login({
              ...data.user,
              permissions: data.permissions,
            })
          }
        } catch (error) {
          this.$nuxt.$loading.finish()

          this.$v.$reset()

          this.error = this.$getErrorMessage(error)
        }
      }
    },
  },
}
</script>
