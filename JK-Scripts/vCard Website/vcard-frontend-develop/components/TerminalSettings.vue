<template>
  <b-card
    :header="
      typeof terminal.id === 'number' ? 'Settings' : 'New Terminal Settings'
    "
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
        <div class="col-md-6 col-12">
          <b-form-group label="Game Title">
            <b-form-select
              v-model="terminal.programName"
              :disabled="!$isAllowed('app/terminals/update')"
              :options="gameTitles"
              :state="
                $v.terminal.programName.$dirty
                  ? !$v.terminal.programName.$error
                  : null
              "
            />
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Group name">
            <b-form-input
              v-model.trim="terminal.groupName"
              :disabled="!$isAllowed('app/terminals/update')"
              :state="
                $v.terminal.groupName.$dirty
                  ? !$v.terminal.groupName.$error
                  : null
              "
              placeholder="Enter group name"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Board asset tag number">
            <b-form-input
              v-model.trim="terminal.boardAssetNumber"
              :disabled="!$isAllowed('app/terminals/update')"
              :state="
                $v.terminal.boardAssetNumber.$dirty
                  ? !$v.terminal.boardAssetNumber.$error
                  : null
              "
              placeholder="Enter board asset tag number"
            />
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Cabinet asset tag number">
            <b-form-input
              v-model.trim="terminal.cabinetAssetNumber"
              :disabled="!$isAllowed('app/terminals/update')"
              :state="
                $v.terminal.cabinetAssetNumber.$dirty
                  ? !$v.terminal.cabinetAssetNumber.$error
                  : null
              "
              placeholder="Enter cabinet asset tag number"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="License number">
            <b-form-input
              v-model.trim="terminal.licenseNumber"
              :disabled="!$isAllowed('app/terminals/update')"
              :state="
                $v.terminal.licenseNumber.$dirty
                  ? !$v.terminal.licenseNumber.$error
                  : null
              "
              placeholder="Enter license number"
            />
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Machine Type">
            <b-form-select
              v-model="terminal.machineTypeId"
              :disabled="!$isAllowed('app/terminals/update')"
              :options="machineTypes"
              :state="
                $v.terminal.machineTypeId.$dirty
                  ? !$v.terminal.machineTypeId.$error
                  : null
              "
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Placement date">
            <b-form-datepicker
              v-model="terminal.placementDate"
              :disabled="!$isAllowed('app/terminals/update')"
              :state="
                $v.terminal.placementDate.$dirty
                  ? !$v.terminal.placementDate.$error
                  : null
              "
              placeholder="Choose a date"
            />
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Padlock">
            <b-form-select
              v-model="terminal.padlockId"
              :disabled="!$isAllowed('app/terminals/update')"
              :options="padlocks"
              :state="
                $v.terminal.padlockId.$dirty
                  ? !$v.terminal.padlockId.$error
                  : null
              "
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Door lock">
            <b-form-select
              v-model="terminal.doorLockId"
              :disabled="!$isAllowed('app/terminals/update')"
              :options="doorLocks"
              :state="
                $v.terminal.doorLockId.$dirty
                  ? !$v.terminal.doorLockId.$error
                  : null
              "
            />
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Reference Number">
            <b-form-input
              v-model.trim="terminal.referenceNumber"
              :state="
                $v.terminal.referenceNumber.$dirty
                  ? !$v.terminal.referenceNumber.$error
                  : null
              "
              placeholder="Enter reference number"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Refill date">
            <b-form-datepicker
              v-model="terminal.refillDate"
              :disabled="!$isAllowed('app/terminals/update')"
              :state="
                $v.terminal.refillDate.$dirty
                  ? !$v.terminal.refillDate.$error
                  : null
              "
              placeholder="Choose a date"
            />
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <div>
            <div class="col-form-label pt-0">Location*</div>

            <location-selector
              v-model="location.id"
              :class="{
                'is-invalid': $v.location.id.$error,
                'is-valid': !$v.location.id.$error && $v.location.id.$dirty,
              }"
              :disabled="!$isAllowed('app/terminals/update')"
              @input="getLocation"
            />
            <div class="invalid-feedback mb-3">
              Please select location
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Split Percent*">
            <b-input-group
              :class="{ 'is-invalid': $v.terminal.splitPercent.$error }"
              prepend="%"
            >
              <b-form-input
                v-model.number="terminal.splitPercent"
                :disabled="!$isAllowed('app/terminals/update')"
                :state="
                  $v.terminal.splitPercent.$dirty
                    ? !$v.terminal.splitPercent.$error
                    : null
                "
                max="100"
                min="0"
                placeholder="Number between 0 - 100"
                step="0.1"
                type="number"
              />
            </b-input-group>

            <b-form-invalid-feedback v-if="$v.terminal.splitPercent.$error">
              Please specify split percent between 0 and 100.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Terminal #*">
            <b-form-input
              v-model.number="terminal.number"
              :max="location.maxTerminalNumber"
              :state="
                $v.terminal.number.$dirty
                  ? ($v.terminal.number.required && !$v.location.id.required) ||
                    !$v.terminal.number.$error
                  : null
              "
              min="0"
              placeholder="Enter terminal number"
              step="1"
              type="number"
            />

            <b-form-invalid-feedback
              v-if="!$v.terminal.number.required && !$v.location.id.required"
            >
              Please specify terminal number
            </b-form-invalid-feedback>
            <b-form-invalid-feedback
              v-else-if="
                !$v.terminal.number.isTerminalNumberValid &&
                $v.location.id.required
              "
            >
              Please specify terminal number between 0 and
              {{ location.maxTerminalNumber }}.
            </b-form-invalid-feedback>

            <b-form-invalid-feedback
              v-else-if="
                !$v.terminal.number.minValue && $v.location.id.required
              "
            >
              Please specify terminal number greater than or equal to 0.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Cabinet Type*">
            <b-form-select
              v-model="terminal.cabinetTypeId"
              :disabled="!$isAllowed('app/terminals/update')"
              :options="cabinetTypes"
              :state="
                $v.terminal.cabinetTypeId.$dirty
                  ? !$v.terminal.cabinetTypeId.$error
                  : null
              "
            />

            <b-form-invalid-feedback v-if="!$v.terminal.cabinetTypeId.required">
              Please select a cabinet type.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="GPS">
            <b-form-input
              v-model.trim="terminal.gps"
              :disabled="!$isAllowed('app/terminals/update')"
              :state="$v.terminal.gps.$dirty ? !$v.terminal.gps.$error : null"
              placeholder="Enter GPS"
            />
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Notes">
            <b-form-input
              v-model.trim="terminal.notes"
              :disabled="!$isAllowed('app/terminals/update')"
              :state="
                $v.terminal.notes.$dirty ? !$v.terminal.notes.$error : null
              "
              placeholder="Enter notes"
            />
          </b-form-group>
        </div>
      </div>

      <b-button
        v-if="$isAllowed('app/terminals/update')"
        type="submit"
        variant="primary"
      >
        {{ typeof terminal.id === 'number' ? 'Update' : 'Create' }}
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
      <b-spinner label="Loading..." variant="primary" />
    </div>
  </b-card>
</template>

<script>
import { maxValue, minValue, required } from 'vuelidate/lib/validators'

import LocationSelector from '~/components/selectors/LocationSelector'

const isTerminalNumberValid = (param) => (value) => {
  if (!param) return true

  return value > 0 && value <= param
}

export default {
  components: {
    LocationSelector,
  },
  props: {
    initialTerminal: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      error: null,
      success: null,
      cabinetTypes: null,
      machineTypes: null,
      gameTitles: null,
      padlocks: null,
      doorLocks: null,
      terminal: this.initialTerminal || {
        number: null,
        cabinetTypeId: null,
        programName: null,
        splitPercent: null,
        flatFee: null,
        groupName: null,
        cabinetAssetNumber: null,
        boardAssetNumber: null,
        licenseNumber: null,
        referenceNumber: null,
        machineTypeId: null,
        padlockId: null,
        doorLockId: null,
        placementDate: null,
        refillDate: null,
        gps: null,
        notes: null,
      },
      location: {
        id: null,
        maxTerminalNumber: null,
      },
    }
  },
  validations() {
    return {
      terminal: {
        number: {
          required,
          minValue: minValue(0),
          isTerminalNumberValid: isTerminalNumberValid(
            this.location.maxTerminalNumber
          ),
        },
        cabinetTypeId: {
          required,
        },
        programName: {},
        splitPercent: {
          required,
          minValue: minValue(0),
          maxValue: maxValue(100),
        },
        flatFee: {
          minValue: minValue(0),
        },
        groupName: {},
        cabinetAssetNumber: {},
        boardAssetNumber: {},
        referenceNumber: {},
        licenseNumber: {},
        machineTypeId: {},
        padlockId: {},
        doorLockId: {},
        placementDate: {},
        refillDate: {},
        gps: {},
        notes: {},
      },
      location: {
        id: { required },
      },
    }
  },
  computed: {},
  watch: {
    initialTerminal(terminal) {
      this.terminal = terminal
    },
  },
  beforeCreate() {},
  created() {
    this.getCabinetTypes()
    this.getMachineTypes()
    this.getGameTitles()
    this.getPadlocks()
    this.getDoorLocks()
    this.getLocation(this.terminal.locationId)
  },
  methods: {
    async getCabinetTypes() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/cabinet-types/list', {
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.cabinetTypes = [
            { value: null, text: 'Select a cabinet type' },
            ...data.results.map((type) => {
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
    async getMachineTypes() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/machine-types/list', {
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.machineTypes = [
            { value: null, text: 'Select a machine type' },
            ...data.results.map((type) => {
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
    async getGameTitles() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/games/list', {})

        if (data.success) {
          this.gameTitles = [
            { value: null, text: 'Select a game' },
            ...data.results.map((type) => {
              return {
                // TODO вернуть  value: type.id,
                value: type.name,
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
    async getPadlocks() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/padlocks/list', {
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.padlocks = [
            { value: null, text: 'Select a padlock' },
            ...data.results.map((type) => {
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
    async getDoorLocks() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/door-locks/list', {
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.doorLocks = [
            { value: null, text: 'Select a door lock' },
            ...data.results.map((type) => {
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
    async getLocation(locationId) {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/locations/get', {
          id: locationId,
        })

        if (data.success) {
          this.location = data.location
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async onSubmit() {
      this.error = null
      this.success = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            terminal: {
              ...this.terminal,
              locationId: this.location.id,
            },
          }

          if (typeof this.terminal.id === 'number') {
            requestData.id = this.terminal.id
          }

          const { data } = await this.$axios.post(
            typeof this.terminal.id === 'number'
              ? '/app/terminals/update'
              : '/app/terminals/create',
            requestData
          )

          if (data.success) {
            if (typeof this.terminal.id === 'number') {
              this.success = 'Terminal settings updated'

              this.$v.$reset()

              this.$emit('updated', data.terminal)
            } else {
              this.$router.push(`/terminals/${data.terminal.id}/settings`)
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
