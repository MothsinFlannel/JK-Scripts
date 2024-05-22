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

      <BCard class="shadow-sm" style="width: 24rem;">
        <BForm @submit.prevent="onSubmit">
          <BFormGroup label="Password">
            <BFormInput
              v-model="password"
              :state="$v.password.$dirty ? !$v.password.$error : null"
              type="password"
            />
          </BFormGroup>
          <BFormGroup label="Confirm Password">
            <BFormInput
              v-model="confirmPassword"
              :state="
                $v.confirmPassword.$dirty ? !$v.confirmPassword.$error : null
              "
              type="password"
            />
          </BFormGroup>

          <BAlert :show="!!error" variant="danger">{{ error }}</BAlert>
          <BAlert :show="passwordChanged" variant="info"
            >Password Changed</BAlert
          >

          <div class="d-flex justify-content-end">
            <BButton type="submit" variant="primary">
              Change password
            </BButton>
          </div>
        </BForm>
      </BCard>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { required } from 'vuelidate/lib/validators'

export default {
  layout: 'sign-in',
  data() {
    return {
      error: null,
      password: null,
      confirmPassword: null,
      passwordChanged: false,
    }
  },
  validations: {
    password: {
      required,
    },
    confirmPassword: {
      required,
      coincide(val, { password }) {
        return val === password
      },
    },
  },
  methods: {
    ...mapActions({
      login: 'user/login',
    }),
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.passwordChanged = false

          this.$nuxt.$loading.start()

          const { data } = await this.$axios.post('/app/password/recover', {
            recoveryToken: this.$route.query.token,
            password: this.password,
          })
          if (data.success) {
            this.password = null
            this.confirmPassword = null
            this.passwordChanged = true

            this.$v.$reset()
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
