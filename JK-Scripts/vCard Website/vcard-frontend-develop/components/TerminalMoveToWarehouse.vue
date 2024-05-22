<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <P class="m-0">Move Terminal To Warehouse</P>
      </div>
    </template>
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>
    <b-alert :show="typeof success === 'string'" variant="success">
      {{ success }}
    </b-alert>

    <b-form @submit.prevent="onSubmit">
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
      <b-button type="submit" variant="primary">
        Send
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
import { required } from 'vuelidate/lib/validators'

export default {
  props: {
    initialTerminal: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      error: null,
      terminal: { ...this.initialTerminal },
      success: null,
      warehouses: null,
      isLoading: false,
    }
  },
  watch: {
    initialTerminal(terminal) {
      this.terminal = terminal
    },
  },
  validations() {
    return {
      terminal: {
        warehouseId: { required },
      },
    }
  },
  created() {
    this.getWarehouses()
  },
  methods: {
    async getWarehouses() {
      try {
        if (this.isLoading) {
          return
        }
        this.error = null
        this.isLoading = true

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
            this.success = 'Terminal is moved to warehouse'
            this.$v.$reset()

            this.$emit('updated', data.terminal)
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
