<template>
  <b-modal
    ref="inWarehouse-modal"
    :title="`Move the terminal ${terminal.number} to the warehouse`"
    ok-title="Send"
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
        <b-form-group label="Warehouse">
          <b-form-select
            v-model="terminal.warehouseId"
            :options="warehouses"
            :state="
              $v.terminal.warehouseId.$dirty
                ? !$v.terminal.warehouseId.$error
                : null
            "
          />
        </b-form-group>
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
      warehouses: null,
      terminal: {
        warehouseId: null,
      },
    }
  },
  validations() {
    return {
      terminal: {
        warehouseId: { required },
      },
    }
  },
  methods: {
    async showModal(terminal) {
      this.error = null

      this.terminal = Object.assign({}, terminal)

      this.$v.$reset()

      try {
        this.$nuxt.$loading.start()

        const warehouses = await this.$axios.post('/app/warehouses/list', {})

        if (warehouses.data.success) {
          this.warehouses = [
            { value: null, text: 'Select a warehouse' },
            ...warehouses.data.results.map((type) => {
              return {
                value: type.id,
                text: type.name,
              }
            }),
          ]
        }

        this.$nuxt.$loading.finish()
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.$nuxt.$loading.finish()
      }

      this.$refs['inWarehouse-modal'].show()
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            id: this.terminal.id,
            terminal: {
              ...this.terminal,
            },
          }

          const { data } = await this.$axios.post(
            '/app/terminals/update',

            requestData
          )

          if (data.success) {
            this.$emit('updated')

            this.$refs['inWarehouse-modal'].hide()
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
