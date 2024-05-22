<template>
  <b-card
    :header="
      typeof company.id === 'number' ? 'Company settings' : 'Add new company'
    "
    class="mb-4 shadow-sm"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-form @submit.prevent="onSubmit">
      <div class="row">
        <div class="col-12">
          <b-form-group label="Name*">
            <b-form-input
              v-model.trim="company.name"
              :state="$v.company.name.$dirty ? !$v.company.name.$error : null"
              placeholder="Enter Company Name"
            />

            <b-form-invalid-feedback v-if="!$v.company.name.required">
              Please provide a valid name.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Contact Name*">
            <b-form-input
              v-model.trim="company.contactName"
              :state="
                $v.company.contactName.$dirty
                  ? !$v.company.contactName.$error
                  : null
              "
              placeholder="Enter Contact Name"
            />

            <b-form-invalid-feedback v-if="!$v.company.contactName.required">
              Please provide a valid contact name.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Contact Email*">
            <b-form-input
              v-model.trim="company.contactEmail"
              :state="
                $v.company.contactEmail.$dirty
                  ? !$v.company.contactEmail.$error
                  : null
              "
              type="email"
              placeholder="Enter Email"
            />

            <b-form-invalid-feedback v-if="$v.company.contactEmail.$error">
              Please provide a valid email.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
          <b-form-checkbox
            v-model="company.invoicingOnline"
            :state="
              $v.company.invoicingOnline.$dirty
                ? !$v.company.invoicingOnline.$error
                : null
            "
            :value="true"
            :unchecked-value="false"
            class="mb-3"
          >
            Invoicing Online
          </b-form-checkbox>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
          <b-form-checkbox
            v-model="company.isActive"
            :state="
              $v.company.isActive.$dirty ? !$v.company.isActive.$error : null
            "
            :value="false"
            :unchecked-value="true"
            class="mb-3"
          >
            Inactive Сompany
          </b-form-checkbox>
        </div>
      </div>

      <b-button type="submit" variant="primary">
        {{ typeof company.id === 'number' ? 'Update' : 'Create' }}
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

export default {
  data() {
    return {
      isLoading: false,
      error: null,
      company: {
        name: null,
        contactName: null,
        contactEmail: null,
        invoicingOnline: false,
        isActive: true,
      },
    }
  },
  validations: {
    company: {
      contactEmail: { required, email },
      name: { required },
      contactName: { required },
      isActive: {},
      invoicingOnline: {},
    },
  },
  created() {
    if (this.$route.name === 'companies-id-settings') {
      this.getCompanies()
    }
  },
  methods: {
    async getCompanies() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/companies/get', {
          id: +this.$route.params.id,
        })

        if (data.success) {
          this.company = data.company
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            company: this.company,
          }

          if (typeof this.company.id === 'number') {
            requestData.id = this.company.id
          }

          const { data } = await this.$axios.post(
            typeof this.company.id === 'number'
              ? '/app/companies/update'
              : '/app/companies/create',
            requestData
          )

          if (data.success) {
            this.$router.push('/companies')
          }

          this.$nuxt.$loading.finish()
        } catch (error) {
          window.scroll(0, 0)
          this.error = this.$getErrorMessage(error)

          this.$nuxt.$loading.finish()
        }
      }
    },
  },
}
</script>
