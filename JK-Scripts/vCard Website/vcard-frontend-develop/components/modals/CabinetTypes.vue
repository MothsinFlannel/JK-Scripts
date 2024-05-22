<template>
  <b-modal
    ref="cabinet-types-modal"
    :title="
      typeof cabinet.id === 'number' ? 'Edit cabinet type' : 'Add cabinet type'
    "
    :ok-title="typeof cabinet.id === 'number' ? 'Save' : 'Add'"
    header-bg-variant="primary"
    header-text-variant="white"
    size="md"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-12">
        <b-form-group label="Cabinet type name">
          <b-form-input
            v-model.trim="cabinet.name"
            :state="$v.cabinet.name.$dirty ? !$v.cabinet.name.$error : null"
            placeholder="Enter cabinet type name"
            autofocus
          />
        </b-form-group>
        <b-form-checkbox
          v-model="cabinet.isActive"
          :state="
            $v.cabinet.isActive.$dirty ? !$v.cabinet.isActive.$error : null
          "
          :value="true"
          :unchecked-value="false"
        >
          Active
        </b-form-checkbox>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  data() {
    return {
      error: null,
      cabinet: {
        name: null,
        isActive: true,
      },
    }
  },
  validations() {
    return {
      cabinet: {
        name: { required },
        isActive: { required },
      },
    }
  },
  methods: {
    showModal(cabinet) {
      this.error = null

      if (cabinet) {
        this.cabinet = Object.assign({}, cabinet)
      } else {
        this.cabinet = {
          name: null,
          isActive: true,
        }
      }
      this.$v.$reset()
      this.$refs['cabinet-types-modal'].show()
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            item: {
              ...this.cabinet,
            },
          }

          if (typeof this.cabinet.id === 'number') {
            requestData.id = this.cabinet.id
          }

          const { data } = await this.$axios.post(
            typeof this.cabinet.id === 'number'
              ? '/app/cabinet-types/update'
              : '/app/cabinet-types/create',
            requestData
          )

          if (data.success) {
            this.$emit('updated')

            this.$refs['cabinet-types-modal'].hide()
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
