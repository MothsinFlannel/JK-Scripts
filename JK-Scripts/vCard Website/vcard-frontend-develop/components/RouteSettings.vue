<template>
  <b-card
    :header="typeof route.id === 'number' ? 'Route Settings' : 'New Route'"
    class="mb-4 shadow-sm"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-alert :show="typeof success === 'string'" variant="success">
      {{ success }}
    </b-alert>

    <b-form @submit.prevent="onSubmit">
      <div class="row">
        <div class="col-12">
          <b-form-group label="Name*">
            <b-form-input
              v-model.trim="route.name"
              :state="$v.route.name.$dirty ? !$v.route.name.$error : null"
              placeholder="Enter Route Name"
            />

            <b-form-invalid-feedback v-if="!$v.route.name.required">
              Please provide a valid route name.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <b-button type="submit" variant="primary">
        {{ typeof route.id === 'number' ? 'Update' : 'Create' }}
      </b-button>
    </b-form>
  </b-card>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  props: {
    initialRoute: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      error: null,
      success: null,
      route: this.initialRoute || {
        name: null,
      },
    }
  },
  validations: {
    route: {
      name: {
        required,
      },
    },
  },
  watch: {
    initialRoute(route) {
      this.route = route
    },
  },
  methods: {
    async onSubmit() {
      this.error = null
      this.success = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            route: this.route,
          }

          if (typeof this.route.id === 'number') {
            requestData.id = this.route.id
          }

          const { data } = await this.$axios.post(
            typeof this.route.id === 'number'
              ? '/app/routes/update'
              : '/app/routes/create',
            requestData
          )

          if (data.success) {
            if (typeof this.route.id === 'number') {
              this.success = 'Route settings updated'

              this.$v.$reset()

              this.$emit('updated', data.route)
            } else {
              this.$router.push('/routes')
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
