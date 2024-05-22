<template>
  <b-modal
    ref="clerk-modal"
    :title="typeof clerk.id === 'number' ? 'Edit clerk' : 'Add clerk'"
    :ok-title="typeof clerk.id === 'number' ? 'Save' : 'Add'"
    header-bg-variant="primary"
    header-text-variant="white"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-12">
        <b-form-group label="Name*">
          <b-form-input
            v-model.trim="clerk.fullName"
            :state="$v.clerk.fullName.$dirty ? !$v.clerk.fullName.$error : null"
            placeholder="Enter clerk name"
            autofocus
          />

          <b-form-invalid-feedback v-if="$v.clerk.fullName.required">
            Please provide a valid name.
          </b-form-invalid-feedback>
        </b-form-group>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <b-form-group label="PIN*">
          <b-form-input
            v-model.trim="clerk.pin"
            :state="$v.clerk.pin.$dirty ? !$v.clerk.pin.$error : null"
            placeholder="Enter clerk PIN"
          />

          <b-form-invalid-feedback v-if="!$v.clerk.pin.required">
            Please provide a valid PIN code.
          </b-form-invalid-feedback>
        </b-form-group>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <b-form-group label="Role*">
          <b-form-select
            v-model="clerk.isManager"
            :options="[
              { value: false, text: 'Clerk' },
              { value: true, text: 'Manager' },
            ]"
            :state="
              $v.clerk.isManager.$dirty ? !$v.clerk.isManager.$error : null
            "
          />

          <b-form-invalid-feedback v-if="!$v.clerk.isManager.required">
            Please select a role.
          </b-form-invalid-feedback>
        </b-form-group>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  props: {
    location: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      error: null,
      clerk: {
        fullName: null,
        pin: null,
        isManager: false,
      },
    }
  },
  validations() {
    return {
      clerk: {
        fullName: {
          required,
        },
        pin: {
          required,
        },
        isManager: {
          required,
        },
      },
    }
  },
  methods: {
    showModal(clerk) {
      this.error = null

      if (clerk) {
        this.clerk = Object.assign({}, clerk)
      } else {
        this.clerk = {
          fullName: null,
          pin: null,
          isManager: false,
        }
      }

      this.$v.$reset()

      this.$refs['clerk-modal'].show()
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            clerk: {
              ...this.clerk,
              locationId: this.location.id,
            },
          }

          if (typeof this.clerk.id === 'number') {
            requestData.id = this.clerk.id
          }

          const { data } = await this.$axios.post(
            typeof this.clerk.id === 'number'
              ? '/app/clerks/update'
              : '/app/clerks/create',
            requestData
          )

          if (data.success) {
            this.$emit('updated')

            this.$refs['clerk-modal'].hide()
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
