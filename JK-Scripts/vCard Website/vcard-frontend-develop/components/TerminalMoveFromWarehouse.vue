<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <P class="m-0">Move Terminal From Warehouse</P>
      </div>
    </template>
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>
    <b-alert :show="typeof success === 'string'" variant="success">
      {{ success }}
    </b-alert>

    <b-form @submit.prevent="onSubmit">
      <b-tabs
        v-model="tabIndex"
        content-class="mt-3"
        active-tab-class="border-bottom-0"
      >
        <b-tab :title-link-class="linkClass(0)" title="Location" active>
          <div class="row">
            <div class="col-12">
              <div class="w-100">
                <OldLocationSelector :grid="true" />
                <div
                  v-if="$v.location.id.$error"
                  class="col-8 ml-auto p-0 mt-1"
                  style="font-size: 0.75rem; color: #dc3545;"
                >
                  The location must not be empty.
                </div>
              </div>
              <div
                class="d-flex align-items-center flex-sm-nowrap flex-wrap mt-3 w-100"
              >
                <span class="col-4 p-0" style="white-space: nowrap;">
                  Terminal Number:
                </span>
                <div class="w-100">
                  <b-form-input
                    v-model.number="terminalNumber"
                    :value="terminal.number"
                    :state="
                      $v.terminalNumber.$dirty
                        ? !$v.terminalNumber.$error
                        : null
                    "
                    type="number"
                    min="0"
                    :max="location.maxTerminalNumber"
                    step="1"
                    style="min-width: 15rem;"
                    class="ml-0"
                    autofocus
                  />
                  <b-form-invalid-feedback v-if="$v.terminalNumber.$error">
                    Please specify terminal number between 0 and
                    {{ location.maxTerminalNumber }}.
                  </b-form-invalid-feedback>
                </div>
              </div>
            </div>
          </div>
        </b-tab>
        <b-tab :title-link-class="linkClass(1)" title="Warehouse">
          <div class="row">
            <div class="col-12">
              <div class="d-flex align-items-center flex-sm-nowrap flex-wrap">
                <span class="col-4 p-0" style="white-space: nowrap;"
                  >Select Warehouse:</span
                >
                <div class="w-100">
                  <b-form-select
                    v-model="selectedWarehouse"
                    :options="warehouses"
                    :state="
                      $v.selectedWarehouse.$dirty
                        ? !$v.selectedWarehouse.$error
                        : null
                    "
                    style="min-width: 15rem;"
                  />
                  <b-form-invalid-feedback v-if="$v.selectedWarehouse.$error">
                    The warehouse must not be empty.
                  </b-form-invalid-feedback>
                </div>
              </div>
            </div>
          </div>
        </b-tab>
      </b-tabs>

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
import { maxValue, minValue, required } from 'vuelidate/lib/validators'
import OldLocationSelector from '~/components/selectors/OldLocationSelector'

export default {
  components: {
    OldLocationSelector,
  },
  props: {
    initialTerminal: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      error: null,
      success: null,
      warehouses: null,
      terminal: { ...this.initialTerminal },
      selectedWarehouse: undefined,
      location: {
        maxTerminalNumber: null,
        id: null,
      },
      terminalNumber: null,
      tabIndex: 0,
      isLoading: false,
    }
  },
  validations() {
    if (this.tabIndex === 0) {
      return {
        terminalNumber: {
          required,
          minValue: minValue(0),
          maxValue: maxValue(this.location.maxTerminalNumber),
        },
        selectedWarehouse: {},
        location: {
          id: {
            required,
          },
        },
      }
    } else {
      return {
        terminalNumber: {},
        selectedWarehouse: { required },
        location: {
          id: {},
        },
      }
    }
  },
  watch: {
    tabIndex() {
      this.error = null
    },
    '$route.query.location'() {
      this.getLocation()
    },
    initialTerminal(terminal) {
      this.terminal = terminal
    },
  },
  created() {
    this.getWarehouses()
    this.getLocation()
  },
  methods: {
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return ['bg-white', 'text-primary', 'mr-2', 'border-bottom-0']
      } else {
        return ['text-black-50', 'mr-2', 'bg-black-10', 'border-bottom-0']
      }
    },
    async getWarehouses() {
      try {
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
    async getLocation() {
      if (+this.$route.query.location) {
        try {
          this.error = null
          this.isLoading = true

          const location = await this.$axios.post('/app/locations/get', {
            id: +this.$route.query.location,
          })

          if (location.data.success) {
            this.location = location.data.location
          }

          this.isLoading = false
        } catch (error) {
          this.error = this.$getErrorMessage(error)

          this.isLoading = false
        }
      }
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          let requestData
          if (this.tabIndex === 0) {
            requestData = {
              id: this.terminal.id,
              terminal: {
                ...this.terminal,
                locationId: this.location.id,
                warehouseId: null,
                number: this.terminalNumber,
              },
            }
          } else if (this.tabIndex === 1) {
            requestData = {
              id: this.terminal.id,
              terminal: {
                ...this.terminal,
                warehouseId: this.selectedWarehouse,
              },
            }
          }

          const { data } = await this.$axios.post(
            '/app/terminals/update',
            requestData
          )

          if (data.success) {
            this.$nuxt.$loading.finish()
            this.success = 'Terminal is moved'
            this.$v.$reset()

            this.$emit('updated', data.terminal)
          }
        } catch (error) {
          this.error = this.$getErrorMessage(error)
          this.$nuxt.$loading.finish()
        }
      }
    },
  },
}
</script>
