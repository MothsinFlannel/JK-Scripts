<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Account</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[{ title: 'vCard Dashboard', link: '/' }, { title: 'Account' }]"
      />
    </div>

    <div class="row">
      <div class="col-md-6 col-12">
        <b-card header="Account Settings" class="mb-4 shadow-sm">
          <b-alert :show="typeof error === 'string'" variant="danger">
            {{ error }}
          </b-alert>

          <b-alert
            :show="typeof success === 'string'"
            variant="success"
            dismissible
          >
            {{ success }}
          </b-alert>

          <b-form @submit.prevent="saveSettings">
            <div class="row">
              <div class="col-12">
                <b-form-group label="Email (you can't change email)">
                  <b-form-input
                    v-model.trim="user.email"
                    :state="$v.user.email.$dirty ? !$v.user.email.$error : null"
                    type="email"
                    placeholder="Enter Email"
                    disabled
                  />

                  <b-form-invalid-feedback v-if="$v.user.email.$error">
                    Please provide a valid email.
                  </b-form-invalid-feedback>
                </b-form-group>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <b-form-group label="Your Name*">
                  <b-form-input
                    v-model.trim="user.fullName"
                    :state="
                      $v.user.fullName.$dirty ? !$v.user.fullName.$error : null
                    "
                    :disabled="!$isAllowed('app/account/update')"
                    placeholder="Enter Your Name"
                  />

                  <b-form-invalid-feedback v-if="!$v.user.fullName.required">
                    Please provide a valid name.
                  </b-form-invalid-feedback>
                </b-form-group>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <b-form-group label="Your Phone">
                  <b-form-input
                    v-model.trim="user.phone"
                    :state="$v.user.phone.$dirty ? !$v.user.phone.$error : null"
                    :disabled="!$isAllowed('app/account/update')"
                    type="tel"
                    placeholder="Enter Your Phone"
                  />
                </b-form-group>
              </div>
            </div>

            <b-button
              v-if="$isAllowed('app/account/update')"
              type="submit"
              variant="primary"
            >
              Save Settings
            </b-button>
          </b-form>
        </b-card>
      </div>

      <div class="col-md-6 col-12">
        <ChangePassword />
      </div>
    </div>
  </div>
</template>

<script>
import { required, email } from 'vuelidate/lib/validators'
import Breadcrumb from '~/components/Breadcrumb'
import ChangePassword from '~/components/ChangePassword'

export default {
  components: {
    Breadcrumb,
    ChangePassword,
  },
  async asyncData({ $axios, $getErrorMessage }) {
    try {
      const { data } = await $axios.post('/app/account/get', {})

      if (data.success) {
        return {
          user: data.user,
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
      error: null,
      success: null,
      user: null,
    }
  },
  validations: {
    user: {
      email: { required, email },
      fullName: { required },
      phone: {},
    },
  },
  methods: {
    async saveSettings() {
      this.error = null
      this.success = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const { data } = await this.$axios.post('/app/account/update', {
            user: {
              fullName: this.user.fullName,
              phone: this.user.phone,
            },
          })

          if (data.success) {
            this.user = data.user

            this.success = 'Account updated'

            this.$v.$reset()
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
