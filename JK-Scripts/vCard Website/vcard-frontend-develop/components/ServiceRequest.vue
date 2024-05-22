<template>
  <b-card header="Service Request" class="mb-4 shadow-sm">
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-alert :show="typeof success === 'string'" variant="success" dismissible>
      {{ success }}
    </b-alert>

    <div v-if="!isEditing && serviceRequest" class="mb-3">
      Service requested: {{ serviceRequest }}
    </div>

    <b-form v-if="isEditing" @submit.prevent="onSubmit">
      <div class="row">
        <div class="col-12">
          <b-form-group>
            <b-form-textarea
              v-model="serviceRequest"
              :state="
                $v.serviceRequest.$dirty ? !$v.serviceRequest.$error : null
              "
              :disabled="!$isAllowed('app/locations/update')"
              placeholder="Enter service reqest"
              rows="6"
            >
            </b-form-textarea>
          </b-form-group>
        </div>
      </div>
      <div class="d-flex align-items-center">
        <b-button
          v-if="$isAllowed('app/locations/update')"
          type="submit"
          variant="primary"
        >
          Send
        </b-button>
      </div>
    </b-form>

    <div v-if="!isEditing" class="d-flex align-items-center">
      <b-button
        v-if="$isAllowed('app/locations/update')"
        type="button"
        variant="primary"
        @click="requireService"
      >
        {{ serviceRequest ? 'Update Service Reqest' : 'Require Service' }}
      </b-button>

      <b-button
        v-if="$isAllowed('app/locations/update') && serviceRequest"
        type="button"
        variant="primary"
        class="ml-auto"
        @click="resolve"
      >
        Resolve
      </b-button>
    </div>

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
export default {
  props: {
    location: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      error: null,
      success: null,
      isEditing: false,
    }
  },
  validations: {
    serviceRequest: {},
  },
  computed: {
    serviceRequest: {
      get() {
        return this.location.serviceRequest
      },
      set(value) {
        this.$emit('updated', value)
      },
    },
  },
  methods: {
    requireService() {
      this.isEditing = true
    },
    async onSubmit() {
      this.error = null
      this.success = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            id: this.location.id,
            location: {
              ...this.location,
              serviceRequest:
                this.location.serviceRequest &&
                this.location.serviceRequest.length > 0
                  ? this.location.serviceRequest
                  : null,
            },
          }

          const { data } = await this.$axios.post(
            '/app/locations/update',
            requestData
          )

          if (data.success) {
            this.isEditing = false
            this.success = 'Service request sent successfully'
            this.$v.$reset()
          }
          this.$nuxt.$loading.finish()
        } catch (error) {
          this.error = this.$getErrorMessage(error)
          this.$nuxt.$loading.finish()
        }
      }
    },
    async resolve() {
      this.error = null
      this.success = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            id: this.location.id,
            location: {
              ...this.location,
              serviceRequest: null,
            },
          }

          const { data } = await this.$axios.post(
            '/app/locations/update',
            requestData
          )

          if (data.success) {
            this.success = 'Service request resolved successfully'
            this.serviceRequest = null
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
