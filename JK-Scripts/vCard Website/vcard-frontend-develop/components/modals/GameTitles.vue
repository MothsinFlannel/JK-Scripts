<template>
  <b-modal
    ref="game-titles-modal"
    :title="
      typeof warehouse.id === 'number' ? 'Edit game title' : 'Add game title'
    "
    :ok-title="typeof warehouse.id === 'number' ? 'Save' : 'Add'"
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
        <b-form-group label="Game name">
          <b-form-input
            v-model.trim="warehouse.name"
            :state="$v.warehouse.name.$dirty ? !$v.warehouse.name.$error : null"
            placeholder="Enter game name"
            autofocus
          />
        </b-form-group>
      </div>

      <div class="col-md-6 col-12"></div>
    </div>
  </b-modal>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  data() {
    return {
      error: null,
      warehouse: {
        name: null,
      },
    }
  },
  validations() {
    return {
      warehouse: {
        name: { required },
      },
    }
  },
  methods: {
    showModal(warehouse) {
      this.error = null

      if (warehouse) {
        this.warehouse = Object.assign({}, warehouse)
      } else {
        this.warehouse = {
          name: null,
        }
      }
      this.$v.$reset()
      this.$refs['game-titles-modal'].show()
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            item: {
              ...this.warehouse,
            },
          }

          if (typeof this.warehouse.id === 'number') {
            requestData.id = this.warehouse.id
          }

          const { data } = await this.$axios.post(
            typeof this.warehouse.id === 'number'
              ? '/app/games/update'
              : '/app/games/create',
            requestData
          )

          if (data.success) {
            this.$emit('updated')

            this.$refs['game-titles-modal'].hide()
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
