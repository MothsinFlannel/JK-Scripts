<template>
  <b-modal
    ref="terminal-modal"
    :title="typeof terminal.id === 'number' ? 'Edit terminal' : 'Add terminal'"
    :ok-title="typeof terminal.id === 'number' ? 'Save' : 'Add'"
    header-bg-variant="primary"
    header-text-variant="white"
    size="lg"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
    @close="onClose"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-md-6 col-12">
        <b-form-group label="Game Title">
          <b-form-select
            v-model="terminal.programName"
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
            :disabled="disableLocation"
            :class="{
              'is-invalid': $v.location.id.$error,
              'is-valid': !$v.location.id.$error && $v.location.id.$dirty,
            }"
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
              :state="
                $v.terminal.splitPercent.$dirty
                  ? !$v.terminal.splitPercent.$error
                  : null
              "
              type="number"
              min="0"
              max="100"
              step="0.1"
              placeholder="Number between 0 - 100"
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
            :state="
              $v.terminal.number.$dirty
                ? ($v.terminal.number.required && !$v.location.id.required) ||
                  !$v.terminal.number.$error
                : null
            "
            type="number"
            min="0"
            :max="location.maxTerminalNumber"
            step="1"
            placeholder="Enter terminal number"
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
            v-else-if="!$v.terminal.number.minValue && $v.location.id.required"
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
            :state="$v.terminal.gps.$dirty ? !$v.terminal.gps.$error : null"
            placeholder="Enter GPS"
          />
        </b-form-group>
      </div>

      <div class="col-md-6 col-12">
        <b-form-group label="Notes">
          <b-form-input
            v-model.trim="terminal.notes"
            :state="$v.terminal.notes.$dirty ? !$v.terminal.notes.$error : null"
            placeholder="Enter notes"
          />
        </b-form-group>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required, minValue, maxValue } from 'vuelidate/lib/validators'

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
    disableLocation: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      error: null,
      cabinetTypes: null,
      machineTypes: null,
      gameTitles: null,
      padlocks: null,
      doorLocks: null,
      terminal: {
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
        licenseNumber: {},
        referenceNumber: {},
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
  methods: {
    async getLocation(locationId) {
      if (!locationId) return

      try {
        const loc = await this.$axios.post('/app/locations/get', {
          id: locationId,
        })

        if (loc.data.success) {
          this.location.maxTerminalNumber = loc.data.location.maxTerminalNumber
        }
      } catch (error) {
        console.log(error)
      }
    },
    async showModal(terminal, location) {
      this.error = null

      if (terminal) {
        this.terminal = Object.assign({}, terminal)

        if (!this.terminal.programName) {
          this.terminal.programName = null
        }
      } else {
        this.terminal = {
          number: null,
          cabinetTypeId: null,
          programName: null,
          splitPercent: null,
          flatFee: null,
          groupName: null,
          cabinetAssetNumber: null,
          boardAssetNumber: null,
          licenseNumber: null,
          machineTypeId: null,
          padlockId: null,
          doorLockId: null,
          placementDate: null,
          refillDate: null,
          gps: null,
          notes: null,
          referenceNumber: null,
        }
      }

      this.$v.$reset()

      try {
        this.$nuxt.$loading.start()

        const cabinetTypes = await this.$axios.post('/app/cabinet-types/list', {
          offset: 0,
          limit: -1,
          filters: {
            activeOnly: true,
          },
        })

        const machineTypes = await this.$axios.post('/app/machine-types/list', {
          offset: 0,
          limit: -1,
        })

        const gameTitles = await this.$axios.post('/app/games/list', {
          limit: -1,
        })

        const padlocks = await this.$axios.post('/app/padlocks/list', {
          offset: 0,
          limit: -1,
        })

        const doorLocks = await this.$axios.post('/app/door-locks/list', {
          offset: 0,
          limit: -1,
        })

        if (location) {
          this.location = location
        } else if (terminal) {
          const loc = await this.$axios.post('/app/locations/get', {
            id: terminal.locationId,
          })

          if (loc.data.success) {
            this.location = loc.data.location
          }
        }

        if (cabinetTypes.data.success) {
          this.cabinetTypes = [
            { value: null, text: 'Select a cabinet type' },
            ...cabinetTypes.data.results.map((type) => {
              return {
                value: type.id,
                text: type.name,
              }
            }),
          ]
        }

        if (machineTypes.data.success) {
          this.machineTypes = [
            { value: null, text: 'Select a machine type' },
            ...machineTypes.data.results.map((type) => {
              return {
                value: type.id,
                text: type.name,
              }
            }),
          ]
        }

        if (gameTitles.data.success) {
          this.gameTitles = [
            { value: null, text: 'Select a game' },
            ...gameTitles.data.results.map((type) => {
              return {
                // TODO вернуть  value: type.id,
                value: type.name,
                text: type.name,
              }
            }),
          ]
        }

        if (padlocks.data.success) {
          this.padlocks = [
            { value: null, text: 'Select a padlock' },
            ...padlocks.data.results.map((type) => {
              return {
                value: type.id,
                text: type.name,
              }
            }),
          ]
        }

        if (doorLocks.data.success) {
          this.doorLocks = [
            { value: null, text: 'Select a door lock' },
            ...doorLocks.data.results.map((type) => {
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

      this.$refs['terminal-modal'].show()
    },
    async onSubmit() {
      this.error = null
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
            this.$emit('updated')

            this.$refs['terminal-modal'].hide()
          }

          this.$nuxt.$loading.finish()
        } catch (error) {
          this.error = this.$getErrorMessage(error)

          this.$nuxt.$loading.finish()
        }
      }
    },
    onClose() {
      this.location = {
        id: null,
        maxTerminalNumber: null,
      }
    },
  },
}
</script>
