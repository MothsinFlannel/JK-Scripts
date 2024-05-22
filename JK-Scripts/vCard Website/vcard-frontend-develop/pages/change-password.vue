<template>
  <div
    class="d-flex align-items-center justify-content-center min-vw-100 min-vh-100"
  >
    <div>
      <b-card class="shadow-sm" style="width: 24rem;">
        <b-form @submit.prevent="onSubmit">
          <h5 class="mb-3">
            Your password is expired, please create a new one.
          </h5>

          <b-form-group label="Current Password" label-for="password">
            <b-form-input
              id="password"
              v-model.trim="user.oldPassword"
              autofocus
              :state="
                $v.user.oldPassword.$dirty
                  ? !$v.user.oldPassword.$error && !error.oldPassword
                  : null
              "
              type="password"
              placeholder="Enter password"
            />

            <b-form-invalid-feedback
              v-if="error.oldPassword"
              id="newPassword-feedback"
            >
              <div v-for="item of error.oldPassword" :key="item">
                {{ item }}
              </div>
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-group label="New Password" label-for="newPassword">
            <b-form-input
              id="password"
              v-model.trim="user.newPassword"
              :state="
                $v.user.newPassword.$dirty
                  ? !$v.user.newPassword.$error && !error.newPassword
                  : null
              "
              type="password"
              placeholder="Enter new password"
            />

            <b-form-invalid-feedback
              v-if="error.newPassword"
              id="newPassword-feedback"
            >
              <div v-for="(item, index) of error.newPassword" :key="item">
                <span v-if="index > 0" class="ml-2">- </span>{{ item }}
              </div>
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-group
            label="Repeat New Password"
            label-for="repeatNewPassword"
          >
            <b-form-input
              id="password"
              v-model.trim="user.repeatNewPassword"
              :state="
                $v.user.repeatNewPassword.$dirty
                  ? !$v.user.repeatNewPassword.$error
                  : null
              "
              type="password"
              placeholder="Repeat new password"
            />
          </b-form-group>

          <div
            class="d-flex flex-column justify-content-center align-items-center"
          >
            <b-button type="submit" variant="primary" block>
              Change Password
            </b-button>

            <div class="logout-link text-primary mt-2" @click="logout">
              Sign in with different account
            </div>
          </div>
        </b-form>
      </b-card>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { required, sameAs } from 'vuelidate/lib/validators'

export default {
  layout: 'change-password',
  data() {
    return {
      error: false,
      user: {
        oldPassword: '',
        newPassword: '',
        repeatNewPassword: '',
      },
    }
  },
  validations: {
    user: {
      oldPassword: {
        required,
      },
      newPassword: {
        required,
      },
      repeatNewPassword: {
        required,
        sameAsPassword: sameAs(function () {
          return this.user.newPassword
        }),
      },
    },
  },
  methods: {
    ...mapActions({
      logout: 'user/logout',
    }),
    async onSubmit() {
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.error = false

          this.$nuxt.$loading.start()

          const { data } = await this.$axios.post('/app/password/change', {
            oldPassword: this.user.oldPassword,
            newPassword: this.user.newPassword,
          })

          this.$nuxt.$loading.finish()

          if (data.success) {
            this.$router.push('/')
          }
        } catch (error) {
          this.$nuxt.$loading.finish()

          this.error = this.$getErrorData(error)
        }
      }
    },
  },
}
</script>

<style scoped lang="scss">
.logout-link {
  cursor: pointer;

  &:hover {
    text-decoration: underline;
  }
}
</style>
