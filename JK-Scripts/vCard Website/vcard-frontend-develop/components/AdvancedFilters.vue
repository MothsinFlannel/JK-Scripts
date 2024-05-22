<template>
  <component
    :is="flat ? 'div' : 'b-card'"
    :header-class="{ 'border-bottom-0': !visible }"
    :class="flat ? 'mb-2' : 'mb-4'"
    no-body
  >
    <template #header>
      <div
        class="d-flex align-items-center"
        style="cursor: pointer;"
        @click="toggleVisibility"
      >
        <h6 class="mb-0">
          Advanced filters
        </h6>

        <component
          :is="visible ? 'b-icon-chevron-up' : 'b-icon-chevron-down'"
          class="ml-auto"
        />
      </div>
    </template>

    <component :is="flat ? 'div' : 'b-collapse'" v-model="visible">
      <component :is="flat ? 'div' : 'b-card-body'">
        <b-form @submit.prevent="submit" @reset="reset">
          <b-tabs content-class="mt-3">
            <b-tab active title-item-class="position-relative">
              <template #title>
                General
                <span
                  v-if="generalFiltersCounter"
                  class="counter translate-middle badge rounded-pill bg-danger text-white"
                >
                  {{ generalFiltersCounter }}
                </span>
              </template>
              <div class="row">
                <div
                  v-if="filters.includes('since') && filters.includes('until')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Select a date range:
                    </div>

                    <div class="col-8">
                      <DatePicker
                        :value="dateRangeValue"
                        :editable="false"
                        :clearable="false"
                        :range="true"
                        format="MM/DD/YYYY"
                        type="date"
                        placeholder="Select a date range"
                        class="w-100"
                        style="margin: 2px 0;"
                        @input="onDateRangeChanged"
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('query')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Search:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.query"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('name')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Name:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.name"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('number')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Number:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.number="localValue.number"
                        type="number"
                        min="0"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('locationId')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Location:
                    </div>

                    <div class="col-8">
                      <LocationSelector
                        v-model="localValue.locationId"
                        only-live-locations
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('warehouseId')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Warehouse:
                    </div>

                    <div class="col-8">
                      <WarehouseSelector v-model="localValue.warehouseId" />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('routeId')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Route:
                    </div>

                    <div class="col-8">
                      <RouteSelector v-model="localValue.routeId" />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('invoiceStatus')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Invoice status:
                    </div>

                    <div class="col-8">
                      <InvoiceStatusSelector
                        v-model="localValue.invoiceStatus"
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('companyName')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Company name:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.companyName"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('operatorEmail')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Operator email:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.operatorEmail"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('machineTypeId')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Machine Type:
                    </div>

                    <div class="col-8">
                      <MachineTypeSelector v-model="localValue.machineTypeId" />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('cabinetTypeId')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Cabinet Type:
                    </div>

                    <div class="col-8">
                      <CabinetTypeSelector v-model="localValue.cabinetTypeId" />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('programName')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Game Title:
                    </div>

                    <div class="col-8">
                      <GameSelector
                        v-model="localValue.programName"
                        :multiple="multipleGames"
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('software')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Software:
                    </div>

                    <div class="col-8">
                      <SoftwareSelector
                        v-model="localValue.software"
                        :multiple="multipleSoftwares"
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('licenseNumber')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      License Number:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.licenseNumber"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('gpsNumber')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      GPS #:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.gpsNumber"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('active')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Status:
                    </div>

                    <div class="col-8">
                      <b-form-select
                        v-model="localValue.active"
                        :options="[
                          { value: null, text: 'All' },
                          { value: 1, text: 'Active' },
                          { value: 0, text: 'Inactive' },
                        ]"
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('live')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Is Live:
                    </div>

                    <div class="col-8">
                      <b-form-select
                        v-model="localValue.live"
                        :options="[
                          { value: null, text: 'All' },
                          { value: 1, text: 'Live' },
                          { value: 0, text: 'Test' },
                        ]"
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('onHold')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      On-Hold:
                    </div>

                    <div class="col-8">
                      <b-form-select
                        v-model="localValue.onHold"
                        :options="[
                          { value: null, text: 'All' },
                          { value: 1, text: 'On hold' },
                          { value: 0, text: 'Not on hold' },
                        ]"
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('boardAssetNumber')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Board Asset Number:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.number="localValue.boardAssetNumber"
                        type="number"
                        min="0"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('cabinetAssetNumber')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Cabinet Asset Number:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.number="localValue.cabinetAssetNumber"
                        type="number"
                        min="0"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="filters.includes('referenceNumber')"
                  class="col-12 col-lg-6"
                >
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Reference Number:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.referenceNumber"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>
              </div>
            </b-tab>

            <b-tab
              v-if="isAddressFiltersOn"
              title-item-class="position-relative"
            >
              <template #title>
                Address
                <span
                  v-if="addressFiltersCounter"
                  class="counter translate-middle badge rounded-pill bg-danger text-white"
                >
                  {{ addressFiltersCounter }}
                </span>
              </template>
              <div class="row">
                <div v-if="filters.includes('address')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      Address:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.address"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('city')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      City:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.city"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('state')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      State:
                    </div>

                    <div class="col-8">
                      <state-selector v-model="localValue.state" />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('zipCode')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      ZIP:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.zipCode"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>

                <div v-if="filters.includes('county')" class="col-12 col-lg-6">
                  <div class="row align-items-center mb-3">
                    <div class="col-4">
                      County:
                    </div>

                    <div class="col-8">
                      <b-form-input
                        v-model.trim="localValue.county"
                        placeholder=""
                      />
                    </div>
                  </div>
                </div>
              </div>
            </b-tab>
          </b-tabs>

          <div
            v-if="!flat"
            class="d-flex align-items-center justify-content-end"
          >
            <b-button type="reset" variant="outline-primary" class="mr-2">
              Reset
            </b-button>

            <b-button type="submit" variant="primary">
              Search
            </b-button>
          </div>
        </b-form>
      </component>
    </component>
  </component>
</template>

<script>
/* eslint-disable vue/no-unused-components */
import { BIconChevronUp, BIconChevronDown } from 'bootstrap-vue'
import DatePicker from 'vue2-datepicker'

import LocationSelector from '~/components/selectors/LocationSelector'
import WarehouseSelector from '~/components/selectors/WarehouseSelector'
import MachineTypeSelector from '~/components/selectors/MachineTypeSelector'
import CabinetTypeSelector from '~/components/selectors/CabinetTypeSelector'
import GameSelector from '~/components/selectors/GameSelector'
import SoftwareSelector from '~/components/selectors/SoftwareSelector'
import InvoiceStatusSelector from '~/components/selectors/InvoiceStatusSelector'
import RouteSelector from '~/components/selectors/RouteSelector'
import StateSelector from '~/components/selectors/StateSelector'

export default {
  components: {
    DatePicker,
    BIconChevronUp,
    BIconChevronDown,
    LocationSelector,
    WarehouseSelector,
    MachineTypeSelector,
    CabinetTypeSelector,
    GameSelector,
    SoftwareSelector,
    InvoiceStatusSelector,
    RouteSelector,
    StateSelector,
  },
  props: {
    value: {
      type: Object,
      required: true,
    },
    multipleGames: {
      type: Boolean,
      default: false,
    },
    multipleSoftwares: {
      type: Boolean,
      default: false,
    },
    flat: {
      type: Boolean,
      default: false,
    },
    onlyLiveLocations: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      visible: false,
      localValue: {
        ...this.value,
      },
      generalFilters: [
        'since',
        'query',
        'name',
        'number',
        'locationId',
        'warehouseId',
        'routeId',
        'invoiceStatus',
        'companyName',
        'operatorEmail',
        'machineTypeId',
        'cabinetTypeId',
        'programName',
        'software',
        'licenseNumber',
        'gpsNumber',
        'active',
        'live',
        'onHold',
        'boardAssetNumber',
        'cabinetAssetNumber',
        'referenceNumber',
      ],
      addressFilters: ['city', 'state', 'zipCode', 'county'],
    }
  },
  computed: {
    filters() {
      return Object.keys(this.value)
    },
    isAddressFiltersOn() {
      let result = false

      this.addressFilters.forEach((name) => {
        if (this.filters.includes(name)) {
          result = true
        }
      })

      return result
    },
    generalFiltersCounter() {
      let counter = 0

      this.generalFilters.forEach((name) => {
        if (
          this.filters.includes(name) &&
          (!!this.localValue[name] || this.localValue[name] === 0)
        ) {
          counter++
        }
      })

      return counter
    },
    addressFiltersCounter() {
      let counter = 0

      this.addressFilters.forEach((name) => {
        if (
          this.filters.includes(name) &&
          (!!this.localValue[name] || this.localValue[name] === 0)
        ) {
          counter++
        }
      })

      return counter
    },
    dateRangeValue() {
      return [
        this.localValue.since
          ? this.$dayjs(this.localValue.since, 'YYYY-MM-DD').toDate()
          : null,
        this.$route.query.until
          ? this.$dayjs(this.localValue.until, 'YYYY-MM-DD').toDate()
          : null,
      ]
    },
  },
  watch: {
    value(value) {
      this.localValue = {
        ...value,
      }
    },
  },
  methods: {
    toggleVisibility() {
      this.visible = !this.visible
    },
    submit() {
      this.$emit('input', { ...this.localValue })
    },
    reset() {
      for (const key in this.localValue) {
        this.localValue[key] = null
      }

      if (!this.flat) {
        this.submit()
      }
    },
    onDateRangeChanged(dates) {
      this.localValue.since = dates[0]
        ? this.$dayjs(dates[0]).format('YYYY-MM-DD')
        : null
      this.localValue.until = dates[1]
        ? this.$dayjs(dates[1]).format('YYYY-MM-DD')
        : null
    },
  },
}
</script>

<style lang="scss" scoped>
.counter {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 1000;
  transform: translate(30%, -30%);
}
</style>
