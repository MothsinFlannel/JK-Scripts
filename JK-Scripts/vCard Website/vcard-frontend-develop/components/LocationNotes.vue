<template>
  <b-card header="Notes" class="mb-4 shadow-sm">
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-alert :show="typeof success === 'string'" variant="success" dismissible>
      {{ success }}
    </b-alert>

    <b-form @submit.prevent="onSubmit">
      <div class="row">
        <div class="col-12">
          <b-form-group>
            <b-form-textarea
              v-model="locationNotes"
              :state="$v.notes.$dirty ? !$v.notes.$error : null"
              :disabled="!$isAllowed('app/locations/update')"
              placeholder="For notes"
              rows="6"
            >
            </b-form-textarea>
          </b-form-group>
        </div>
      </div>
      <b-button
        v-if="$isAllowed('app/locations/update')"
        type="submit"
        variant="primary"
      >
        Save
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
    }
  },
  validations: {
    notes: {},
  },
  computed: {
    locationNotes: {
      get() {
        return this.location.notes
      },
      set(value) {
        this.$emit('updated', value)
      },
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
            id: this.location.id,
            location: {
              ...this.location,
              notes:
                this.location.notes && this.location.notes.length > 0
                  ? this.location.notes
                  : null,
            },
          }

          const { data } = await this.$axios.post(
            '/app/locations/update',
            requestData
          )

          if (data.success) {
            this.success = 'Location notes saved'
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
