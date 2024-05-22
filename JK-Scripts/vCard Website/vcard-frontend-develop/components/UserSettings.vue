<template>
  <b-card
    :header="typeof user.id === 'number' ? 'User settings' : 'Add new user'"
    class="mb-4 shadow-sm"
  >
    <b-alert :show="$route.name === 'users-create'" variant="info">
      Temporary password will be automatically generated.
    </b-alert>

    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-alert :show="typeof success === 'string'" variant="success" dismissible>
      {{ success }}
    </b-alert>

    <b-form @submit.prevent="onSubmit">
      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Email*">
            <b-form-input
              v-model.trim="user.email"
              :state="$v.user.email.$dirty ? !$v.user.email.$error : null"
              type="email"
              placeholder="Enter Email"
            />

            <b-form-invalid-feedback v-if="$v.user.email.$error">
              Please provide a valid email.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Name*">
            <b-form-input
              v-model.trim="user.fullName"
              :state="$v.user.fullName.$dirty ? !$v.user.fullName.$error : null"
              placeholder="Enter Name"
            />

            <b-form-invalid-feedback v-if="!$v.user.fullName.required">
              Please provide a valid name.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Phone">
            <b-form-input
              v-model.trim="user.phone"
              :state="$v.user.phone.$dirty ? !$v.user.phone.$error : null"
              type="tel"
              placeholder="Enter Phone"
            />
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Role*">
            <b-form-select
              v-model="user.role"
              :options="[
                { value: null, text: 'Select a role' },
                { value: 'admin', text: 'Admin' },
                { value: 'routeman', text: 'Routeman' },
                { value: 'location', text: 'Location' },
                { value: 'technician', text: 'Technician' },
              ]"
              :state="$v.user.role.$dirty ? !$v.user.role.$error : null"
            />

            <b-form-invalid-feedback v-if="!$v.user.role.required">
              Please select a role.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Company*">
            <b-form-select v-model="user.companyId" :options="companies" />
          </b-form-group>
        </div>

        <div v-if="user.role === 'technician'" class="col-md-6 col-12">
          <b-form-group
            :label="
              $v.user.operatesInStates.$params.required ? 'State*' : 'State'
            "
          >
            <state-selector
              v-model="user.operatesInStates"
              :state="$v.user.operatesInStates"
              multiple
            />
          </b-form-group>
        </div>
      </div>

      <b-button type="submit" variant="primary">
        {{ typeof user.id === 'number' ? 'Update' : 'Create' }}
      </b-button>
    </b-form>

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
</template>

<script>
import { required, email } from 'vuelidate/lib/validators'
import StateSelector from '~/components/selectors/StateSelector'

export default {
  components: {
    StateSelector,
  },
  data() {
    return {
      isLoading: false,
      error: null,
      success: null,
      companies: [],
      user: {
        email: null,
        operatesInStates: [],
        fullName: null,
        phone: null,
        role: null,
        companyId: null,
      },
    }
  },
  validations() {
    return {
      user: {
        email: { required, email },
        operatesInStates: this.user.role === 'technician' ? { required } : {},
        fullName: { required },
        phone: {},
        role: { required },
      },
    }
  },
  watch: {
    'user.role'(newValue) {
      console.log(newValue)
      if (newValue === 'technician') {
        this.user.operatesInStates = this.user.operatesInStates || []
      } else {
        this.user.operatesInStates = null
      }
    },
  },
  created() {
    if (this.$route.name === 'users-id-settings') {
      this.getUser()
    }
    this.getCompanies()
  },
  methods: {
    async getUser() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/users/get', {
          id: +this.$route.params.id,
        })

        if (data.success) {
          this.user = data.user
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async getCompanies() {
      try {
        const { data } = await this.$axios.post('/app/companies/list', {
          offset: 0,
          limit: -1,
        })
        if (data.success) {
          this.companies = [
            { value: null, text: 'All' },
            ...data.results.map((type) => {
              return {
                value: type.id,
                text: type.name,
              }
            }),
          ]
        }
      } catch (error) {
        console.error(error)
      }
    },
    async onSubmit() {
      this.error = null
      this.success = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            user: this.user,
          }

          if (typeof this.user.id === 'number') {
            requestData.id = this.user.id
          }

          const { data } = await this.$axios.post(
            typeof this.user.id === 'number'
              ? '/app/users/update'
              : '/app/users/create',
            requestData
          )

          if (data.success) {
            if (this.$route.name === 'users-id-settings') {
              this.$router.push('/users')
            } else {
              window.scroll(0, 0)

              this.success = `Temporary password for ${data.user.email} is ${data.password}`

              this.user.email = null
              this.user.fullName = null
              this.user.phone = null
              this.user.role = null

              this.$v.$reset()
            }
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
