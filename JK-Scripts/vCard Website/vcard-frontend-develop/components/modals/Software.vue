<template>
  <b-modal
    ref="software-modal"
    :title="typeof software.id === 'number' ? 'Edit software' : 'Add software'"
    :ok-title="typeof software.id === 'number' ? 'Save' : 'Add'"
    header-bg-variant="primary"
    header-text-variant="white"
    size="lg"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-md-6 col-12">
        <b-form-group label="Software name">
          <b-form-select
            v-model="software.name"
            :options="softwareTitles"
            :state="$v.software.name.$dirty ? !$v.software.name.$error : null"
            autofocus
          />
        </b-form-group>
      </div>

      <div class="col-md-6 col-12">
        <b-form-group label="Server Number">
          <b-input-group :class="{ 'is-invalid': $v.software.serverNo.$error }">
            <b-form-input
              v-model="software.serverNo"
              :state="
                $v.software.serverNo.$dirty
                  ? !$v.software.serverNo.$error
                  : null
              "
              placeholder="Enter server number"
            />
          </b-input-group>
        </b-form-group>
      </div>

      <div class="col-md-6 col-12">
        <b-form-group label="Split Percent">
          <b-input-group
            :class="{ 'is-invalid': $v.software.split.$error }"
            prepend="%"
          >
            <b-form-input
              v-model.number="software.split"
              :state="
                $v.software.split.$dirty ? !$v.software.split.$error : null
              "
              type="number"
              min="0"
              max="100"
              step="0.1"
              placeholder="Number between 0 - 100"
            />
          </b-input-group>

          <b-form-invalid-feedback v-if="$v.software.split.$error">
            Please specify split percent between 0 and 100.
          </b-form-invalid-feedback>
        </b-form-group>
      </div>

      <div class="col-md-6 col-12">
        <b-form-group label="Max Machine Count">
          <b-input-group
            :class="{ 'is-invalid': $v.software.maxMachineCount.$error }"
          >
            <b-form-input
              v-model.number="software.maxMachineCount"
              :state="
                $v.software.maxMachineCount.$dirty
                  ? !$v.software.maxMachineCount.$error
                  : null
              "
              type="number"
              min="0"
              step="1"
              placeholder="Enter a number"
            />
          </b-input-group>
        </b-form-group>
      </div>

      <div class="col-md-6 col-12">
        <div class="form-group">
          <legend class="bv-no-focus-ring col-form-label pt-0">
            Install Date
          </legend>

          <DatePicker
            v-model="software.installDate"
            type="date"
            format="MM/DD/YYYY"
            placeholder="Enter Install Date"
            :input-class="{
              'form-control': true,
              'is-invalid':
                $v.software.installDate.$dirty &&
                $v.software.installDate.$error,
              'is-valid':
                $v.software.installDate.$dirty &&
                !$v.software.installDate.$error,
            }"
            style="width: 100%;"
          />
        </div>
      </div>

      <div class="col-12">
        <b-form-group label="Notes">
          <b-form-textarea
            v-model.trim="software.notes"
            :state="$v.software.notes.$dirty ? !$v.software.notes.$error : null"
            placeholder="Enter notes"
            rows="3"
          />
        </b-form-group>
      </div>

      <div class="col-md-6 col-12">
        <b-form-checkbox
          v-model="software.isMobileOnly"
          :state="
            $v.software.isMobileOnly.$dirty
              ? !$v.software.isMobileOnly.$error
              : null
          "
          class="mb-3 text-nowrap"
        >
          Mobile Only
        </b-form-checkbox>
      </div>

      <div class="col-md-6 col-12">
        <b-form-checkbox
          v-model="software.isFrozen"
          :state="
            $v.software.isFrozen.$dirty ? !$v.software.isFrozen.$error : null
          "
          class="mb-3 text-nowrap"
        >
          Frozen
        </b-form-checkbox>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required, minValue, maxValue } from 'vuelidate/lib/validators'
import DatePicker from 'vue2-datepicker'

export default {
  components: {
    DatePicker,
  },
  props: {
    locationId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      error: null,
      software: {
        name: null,
        split: null,
        notes: null,
        installDate: null,
        maxMachineCount: null,
        isMobileOnly: false,
        isFrozen: false,
        serverNo: null,
      },
      softwareTitles: null,
    }
  },
  validations() {
    return {
      software: {
        name: {
          required,
        },
        split: {
          minValue: minValue(0),
          maxValue: maxValue(100),
        },
        notes: {},
        installDate: {},
        maxMachineCount: {},
        isMobileOnly: {},
        isFrozen: {},
        serverNo: {},
      },
    }
  },
  methods: {
    async showModal(software) {
      this.error = null

      if (software) {
        this.software = Object.assign(
          {},
          {
            ...software,
            installDate: software.installDate
              ? this.$dayjs(software.installDate, 'YYYY-MM-DD').toDate()
              : null,
          }
        )
      } else {
        this.software = {
          name: null,
          split: null,
          notes: null,
          installDate: null,
          maxMachineCount: null,
          isMobileOnly: false,
          isFrozen: false,
          serverNo: null,
        }
      }

      this.$v.$reset()

      try {
        this.$nuxt.$loading.start()

        const { data } = await this.$axios.post('/app/software-titles/list', {
          limit: -1,
        })

        this.softwareTitles = [
          {
            value: null,
            text: 'Select a Software Title',
          },
          ...data.results.map((softwareTitle) => ({
            value: softwareTitle.name,
            text: softwareTitle.name,
          })),
        ]
      } catch (error) {
        console.error(error)
      } finally {
        this.$nuxt.$loading.finish()
      }

      this.$refs['software-modal'].show()
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            software: {
              ...this.software,
              installDate: this.software.installDate
                ? this.$dayjs(this.software.installDate).format('YYYY-MM-DD')
                : null,
              locationId: this.locationId,
            },
          }

          if (typeof this.software.id === 'number') {
            requestData.id = this.software.id
          }

          const { data } = await this.$axios.post(
            typeof this.software.id === 'number'
              ? '/app/software/update'
              : '/app/software/create',
            requestData
          )

          if (data.success) {
            this.$emit('updated')

            this.$refs['software-modal'].hide()
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
