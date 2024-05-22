<template>
  <b-card
    :header="
      typeof location.id === 'number' ? 'Settings' : 'New Location Settings'
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
        <div class="col-12">
          <b-form-group label="Location name*">
            <b-form-input
              v-model.trim="location.name"
              :state="$v.location.name.$dirty ? !$v.location.name.$error : null"
              placeholder="Enter Location Name"
              :disabled="!$isAllowed('app/locations/update+full')"
            />

            <b-form-invalid-feedback v-if="!$v.location.name.required">
              Please provide a valid location name.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b-form-group label="Route">
            <b-form-select
              v-if="$isAllowed('app/locations/update+full')"
              v-model="location.routeId"
              :options="routes"
              :state="
                $v.location.routeId.$dirty ? !$v.location.routeId.$error : null
              "
              :disabled="!$isAllowed('app/locations/update+full')"
            />
            <b-form-input
              v-if="!$isAllowed('app/locations/update+full')"
              :value="location.route && location.route.name"
              :state="
                $v.location.routeId.$dirty ? !$v.location.routeId.$error : null
              "
              disabled
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b-form-group label="Company Name">
            <b-form-select
              v-if="$isAllowed('app/locations/update+full')"
              v-model="location.companyId"
              :options="companies"
              :state="
                $v.location.companyId.$dirty
                  ? !$v.location.companyId.$error
                  : null
              "
            />
            <b-form-input
              v-if="!$isAllowed('app/locations/update+full')"
              :value="location.company && location.company.name"
              :state="
                $v.location.companyId.$dirty
                  ? !$v.location.companyId.$error
                  : null
              "
              disabled
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b-form-group label="Operator Email (operator@email.com)">
            <b-form-select
              v-if="$isAllowed('app/locations/update+full')"
              v-model="location.operatorEmail"
              :options="users"
              :state="
                $v.location.operatorEmail.$dirty
                  ? !$v.location.operatorEmail.$error
                  : null
              "
            />
            <b-form-input
              v-if="!$isAllowed('app/locations/update+full')"
              v-model.trim="location.operatorEmail"
              :state="
                $v.location.operatorEmail.$dirty
                  ? !$v.location.operatorEmail.$error
                  : null
              "
              disabled
            />
            <b-form-invalid-feedback v-if="!$v.location.operatorEmail.required">
              Please provide a valid email address.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b-form-group label="POS Serial # (e.g. 0000000012345678)">
            <b-form-input
              v-model.trim="location.serial"
              :state="
                $v.location.serial.$dirty ? !$v.location.serial.$error : null
              "
              placeholder="Enter POS Serial"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b-form-group label="License #">
            <b-form-input
              v-model.trim="location.licenseNumber"
              :state="
                $v.location.licenseNumber.$dirty
                  ? !$v.location.licenseNumber.$error
                  : null
              "
              placeholder="Enter License Number"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b-form-group label="Contact Name">
            <b-form-input
              v-model.trim="location.contactName"
              :state="
                $v.location.contactName.$dirty
                  ? !$v.location.contactName.$error
                  : null
              "
              placeholder="Enter Contact Name"
              :disabled="!$isAllowed('app/locations/update+full')"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <b-form-group label="Contact Phone">
            <b-form-input
              v-model.trim="location.contactPhone"
              :state="
                $v.location.contactPhone.$dirty
                  ? !$v.location.contactPhone.$error
                  : null
              "
              type="tel"
              placeholder="Enter Contact Phone"
              :disabled="!$isAllowed('app/locations/update+full')"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="form-group">
            <legend class="bv-no-focus-ring col-form-label pt-0">
              Install Date
            </legend>

            <DatePicker
              v-model="location.installedAt"
              :disabled="!$isAllowed('app/locations/update+full')"
              type="datetime"
              format="MM/DD/YYYY h:mm A"
              placeholder="Enter Install Date"
              :input-class="{
                'form-control': true,
                'is-invalid':
                  $v.location.installedAt.$dirty &&
                  $v.location.installedAt.$error,
                'is-valid':
                  $v.location.installedAt.$dirty &&
                  !$v.location.installedAt.$error,
              }"
              style="width: 100%;"
              use12h
            />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Location Time Zone*">
            <b-form-select
              v-model="location.timezone"
              :options="[
                { value: null, text: 'Select a timezone' },
                { value: 'US/Hawaii', text: 'US/Hawaii' },
                { value: 'US/Alaska', text: 'US/Alaska' },
                { value: 'US/Pacific', text: 'US/Pacific' },
                { value: 'US/Arizona', text: 'US/Arizona' },
                { value: 'US/Mountain', text: 'US/Mountain' },
                { value: 'US/Central', text: 'US/Central' },
                { value: 'US/Indiana-Starke', text: 'US/Indiana-Starke' },
                { value: 'US/East-Indiana', text: 'US/East-Indiana' },
                { value: 'US/Eastern', text: 'US/Eastern' },
                { value: 'US/Michigan', text: 'US/Michigan' },
              ]"
              :state="
                $v.location.timezone.$dirty
                  ? !$v.location.timezone.$error
                  : null
              "
            />

            <b-form-invalid-feedback v-if="!$v.location.timezone.required">
              Please select a timezone.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
        <div class="col-md-6 col-12">
          <b-form-group label="GPS #">
            <b-form-input
              v-model.trim="location.gpsNumber"
              :state="
                $v.location.gpsNumber.$dirty
                  ? !$v.location.gpsNumber.$error
                  : null
              "
              placeholder="Enter GPS Number"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="Address*">
            <b-form-input
              v-model.trim="location.address"
              :state="
                $v.location.address.$dirty ? !$v.location.address.$error : null
              "
              placeholder="Enter Address"
            />

            <b-form-invalid-feedback v-if="!$v.location.address.required">
              Please provide a valid address.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="City*">
            <b-form-input
              v-model.trim="location.city"
              :state="$v.location.city.$dirty ? !$v.location.city.$error : null"
              placeholder="Enter City"
            />

            <b-form-invalid-feedback v-if="!$v.location.city.required">
              Please provide a valid city.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group
            :label="$v.location.state.$params.required ? 'State*' : 'State'"
          >
            <state-selector
              v-model="location.state"
              :state="$v.location.state"
            />

            <b-form-invalid-feedback v-if="!$v.location.state.required">
              Please select a state.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-6 col-12">
          <b-form-group label="Zip*">
            <b-form-input
              v-model.trim="location.zipCode"
              :state="
                $v.location.zipCode.$dirty ? !$v.location.zipCode.$error : null
              "
              placeholder="Enter Zip"
            />

            <b-form-invalid-feedback v-if="!$v.location.zipCode.required">
              Please provide a valid zip code.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-12">
          <b-form-group label="County">
            <b-form-input
              v-model.trim="location.county"
              :state="
                $v.location.county.$dirty ? !$v.location.county.$error : null
              "
              placeholder="Enter county"
            />

            <b-form-invalid-feedback v-if="!$v.location.county.required">
              Please provide a valid county.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 col-12">
          <b-form-group label="Split Percent*">
            <b-input-group
              :class="{ 'is-invalid': $v.location.splitPercent.$error }"
              prepend="%"
            >
              <b-form-input
                v-model.number="location.splitPercent"
                :state="
                  $v.location.splitPercent.$dirty
                    ? !$v.location.splitPercent.$error
                    : null
                "
                type="number"
                min="0"
                max="100"
                step="0.1"
                placeholder="Number between 0 - 100"
                :disabled="!$isAllowed('app/locations/update+full')"
              />
            </b-input-group>

            <b-form-invalid-feedback v-if="$v.location.splitPercent.$error">
              Please specify split percent between 0 and 100.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-4 col-12">
          <b-form-group label="Flat Fee (Dollars only)">
            <b-input-group
              :class="{ 'is-invalid': $v.location.flatFee.$error }"
              prepend="$"
            >
              <b-form-input
                v-model.number="location.flatFee"
                :state="
                  $v.location.flatFee.$dirty
                    ? !$v.location.flatFee.$error
                    : null
                "
                type="number"
                min="0"
                step="1"
                placeholder="Enter only digits"
                :disabled="!$isAllowed('app/locations/update+full')"
              />
            </b-input-group>

            <b-form-invalid-feedback v-if="!$v.location.flatFee.minValue">
              The minimum value is 0.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>
        <div class="col-md-4 col-12">
          <b-form-group label="Printer options">
            <b-form-select
              v-model="selectPrinterOption"
              :options="printerOptions"
            />
          </b-form-group>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 col-12">
          <b-form-group label="Max Offline Hours*">
            <b-input-group
              :class="{ 'is-invalid': $v.location.maxOfflineHours.$error }"
              prepend="H"
            >
              <b-form-input
                v-model.number="location.maxOfflineHours"
                :state="
                  $v.location.maxOfflineHours.$dirty
                    ? !$v.location.maxOfflineHours.$error
                    : null
                "
                type="number"
                min="0"
                max="120"
                step="1"
                placeholder="Number between 1 - 120"
                :disabled="!$isAllowed('app/locations/update+full')"
              />
            </b-input-group>

            <b-form-invalid-feedback v-if="$v.location.maxOfflineHours.$error">
              Please specify hours between 1 and 120.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-4 col-12">
          <b-form-group label="Max Add Credits Amount* (Dollars only)">
            <b-input-group
              :class="{
                'is-invalid': $v.location.maxAddCreditsAmount.$error,
              }"
              prepend="$"
            >
              <b-form-input
                v-model.number="location.maxAddCreditsAmount"
                :state="
                  $v.location.maxAddCreditsAmount.$dirty
                    ? !$v.location.maxAddCreditsAmount.$error
                    : null
                "
                type="number"
                min="50"
                max="500"
                step="1"
                placeholder="Number between 50 - 500"
              />
            </b-input-group>

            <b-form-invalid-feedback
              v-if="$v.location.maxAddCreditsAmount.$error"
            >
              Please specify amount between 50 and 500.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-4 col-12">
          <b-form-group label="Max Terminal Number*">
            <b-input-group
              :class="{
                'is-invalid': $v.location.maxTerminalNumber.$error,
              }"
              prepend="#"
            >
              <b-form-input
                v-model.number="location.maxTerminalNumber"
                :state="
                  $v.location.maxTerminalNumber.$dirty
                    ? !$v.location.maxTerminalNumber.$error
                    : null
                "
                type="number"
                min="25"
                max="199"
                step="1"
                placeholder="Number between 25 - 199"
              />
            </b-input-group>

            <b-form-invalid-feedback
              v-if="$v.location.maxTerminalNumber.$error"
            >
              Please specify terminal number between 25 and 199.
            </b-form-invalid-feedback>
          </b-form-group>
        </div>

        <div class="col-md-4 col-12">
          <b-form-group label="Invoicing Mode">
            <b-form-select
              v-model="location.invoicingMode"
              :state="
                $v.location.invoicingMode.$dirty
                  ? !$v.location.invoicingMode.$error
                  : null
              "
              :options="[
                { value: 'automatic', text: 'Automatic' },
                // { value: 'blank', text: 'Blank' },
                { value: 'custom', text: 'Custom' },
              ]"
            />
          </b-form-group>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
          <b-form-checkbox
            v-model="location.enableCreditsReplay"
            :state="
              $v.location.enableCreditsReplay.$dirty
                ? !$v.location.enableCreditsReplay.$error
                : null
            "
            class="mb-3 col-md-3 col-12 text-nowrap"
          >
            Enable Credits Replay
          </b-form-checkbox>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <b-form-checkbox
            v-model="location.enableAddCredits"
            :state="
              $v.location.enableAddCredits.$dirty
                ? !$v.location.enableAddCredits.$error
                : null
            "
            class="mb-3 text-nowrap"
          >
            Enable Add Credits
          </b-form-checkbox>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <b-form-checkbox
            v-model="location.isActive"
            :state="
              $v.location.isActive.$dirty ? !$v.location.isActive.$error : null
            "
            :value="false"
            :unchecked-value="true"
            class="mb-3 text-nowrap"
          >
            Inactive Location
          </b-form-checkbox>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <b-form-checkbox
            v-model="location.onHold"
            :state="
              $v.location.onHold.$dirty ? !$v.location.onHold.$error : null
            "
            :value="true"
            :unchecked-value="false"
            class="mb-3 text-nowrap"
          >
            On-Hold Location
          </b-form-checkbox>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <b-form-checkbox
            v-model="location.isLive"
            :state="
              $v.location.isLive.$dirty ? !$v.location.isLive.$error : null
            "
            :value="false"
            :unchecked-value="true"
            class="mb-3"
          >
            Test Mode
          </b-form-checkbox>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <b-form-checkbox
            v-model="location.disableScreenLock"
            :state="
              $v.location.disableScreenLock.$dirty
                ? !$v.location.disableScreenLock.$error
                : null
            "
            class="mb-3 text-nowrap"
          >
            Disable Screen Lock
          </b-form-checkbox>
        </div>
      </div>

      <b-button type="submit" variant="primary">
        {{ typeof location.id === 'number' ? 'Update' : 'Create' }}
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
import { required, email, minValue, maxValue } from 'vuelidate/lib/validators'
import { mapGetters } from 'vuex'
import DatePicker from 'vue2-datepicker'
import StateSelector from '~/components/selectors/StateSelector'

export default {
  components: {
    DatePicker,
    StateSelector,
  },
  props: {
    initialLocation: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      error: null,
      success: null,
      routes: null,
      users: null,
      companies: null,
      printerOptions: [
        {
          value: { enableRedemption: false, disablePrinting: false },
          text: 'Automatic printing',
        },
        {
          value: { enableRedemption: true, disablePrinting: false },
          text: 'Selective Redemption Print',
        },
        {
          value: { enableRedemption: true, disablePrinting: true },
          text: 'Selective Redemption No Print',
        },
        {
          value: { enableRedemption: false, disablePrinting: true },
          text: 'Monitoring Only',
        },
      ],
      selectPrinterOption: null,
      location: this.initialLocation
        ? {
            ...this.initialLocation,
            installedAt: this.initialLocation.installedAt
              ? this.$dayjs(
                  this.initialLocation.installedAt,
                  'YYYY-MM-DD HH:mm:ss'
                ).toDate()
              : null,
          }
        : {
            name: null,
            routeId: null,
            contactName: null,
            contactPhone: null,
            installedAt: null,
            timezone: 'US/Eastern',
            address: null,
            city: null,
            state: null,
            zipCode: null,
            county: null,
            splitPercent: 50,
            flatFee: null,
            isActive: true,
            onHold: false,
            isLive: false,
            operatorEmail: null,
            serial: null,
            maxOfflineHours: 12,
            maxAddCreditsAmount: 100,
            maxTerminalNumber: 50,
            enableAddCredits: false,
            enableRedemption: false,
            disablePrinting: false,
            disableScreenLock: false,
            enableCreditsReplay: false,
            companyId: null,
            gpsNumber: null,
            licenseNumber: null,
            invoicingMode: 'automatic',
          },
    }
  },
  validations: {
    location: {
      name: {
        required,
      },
      routeId: {},
      contactName: {},
      contactPhone: {},
      installedAt: {},
      timezone: {
        required,
      },
      address: {
        required,
      },
      city: {
        required,
      },
      state: {
        required,
      },
      zipCode: {
        required,
      },
      county: {},
      splitPercent: {
        required,
        minValue: minValue(0),
        maxValue: maxValue(100),
      },
      flatFee: {
        minValue: minValue(0),
      },
      isActive: {},
      onHold: {},
      isLive: {},
      operatorEmail: {
        email,
      },
      serial: {},
      maxOfflineHours: {
        required,
        minValue: minValue(0),
        maxValue: maxValue(120),
      },
      maxAddCreditsAmount: {
        required,
        minValue: minValue(50),
        maxValue: maxValue(500),
      },
      maxTerminalNumber: {
        required,
        minValue: minValue(25),
        maxValue: maxValue(199),
      },
      enableAddCredits: {},
      disablePrinting: {},
      disableScreenLock: {},
      enableRedemption: {},
      enableCreditsReplay: {},
      companyId: {},
      licenseNumber: {},
      gpsNumber: {},
      invoicingMode: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters({
      user: 'user/user',
    }),
  },
  watch: {
    selectPrinterOption() {
      this.location.enableRedemption = this.selectPrinterOption.enableRedemption
      this.location.disablePrinting = this.selectPrinterOption.disablePrinting
    },
    initialLocation(location) {
      this.location = location
    },
    'location.onHold'(newValue) {
      if (newValue) {
        this.location.isActive = false
      }
    },
    'location.isActive'(newValue) {
      if (newValue) {
        this.location.onHold = false
      }
    },
  },
  created() {
    this.selectPrinterOption = {
      enableRedemption: this.location.enableRedemption,
      disablePrinting: this.location.disablePrinting,
    }
    if (this.$route.query.installationId) {
      this.getInstallation()
    }

    if (this.$isAllowed('app/routes/list')) {
      this.getRoutes()
    }

    if (this.$isAllowed('app/users/list')) {
      this.getUsers()
    }

    if (this.$isAllowed('app/companies/list')) {
      this.getCompanies()
    }
  },
  methods: {
    async getInstallation() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/installations/get', {
          id: +this.$route.query.installationId,
        })

        if (data.success) {
          this.location.serial = data.installation.serial
          this.location.operatorEmail = data.installation.email
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async getRoutes() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/routes/list', {
          offset: 0,
          limit: -1,
          sort: 'name+asc',
        })

        if (data.success) {
          this.routes = [
            { value: null, text: 'Select a route' },
            ...data.results.map((route) => {
              return {
                value: route.id,
                text: route.name,
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
    async getUsers() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/users/list', {
          offset: 0,
          limit: -1,
          sort: 'fullName+asc',
        })

        if (data.success) {
          this.users = [
            { value: null, text: 'Select a operator' },
            ...data.results.map((user) => {
              return {
                value: user.email,
                text: user.fullName,
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
    async getCompanies() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/companies/list', {
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.location.companyId =
            data.results.find((company) => company.isDefault)?.id || null

          this.companies = [
            { value: null, text: 'Select a company' },
            ...data.results.map((company) => {
              return {
                value: company.id,
                text: company.name,
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
      this.success = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            location: {
              ...this.location,
              serial:
                this.location.serial && this.location.serial.length > 0
                  ? this.location.serial
                  : null,
              licenseNumber:
                this.location.licenseNumber &&
                this.location.licenseNumber.length > 0
                  ? this.location.licenseNumber
                  : null,
              gpsNumber:
                this.location.gpsNumber && this.location.gpsNumber.length > 0
                  ? this.location.gpsNumber
                  : null,
              installedAt: this.location.installedAt
                ? this.$dayjs(this.location.installedAt).format(
                    'YYYY-MM-DD HH:mm:ss'
                  )
                : null,
            },
          }

          if (typeof this.location.id === 'number') {
            requestData.id = this.location.id
          }

          if (this.$route.query.installationId) {
            requestData.installationId = +this.$route.query.installationId
          }

          const { data } = await this.$axios.post(
            typeof this.location.id === 'number'
              ? '/app/locations/update'
              : '/app/locations/create',
            requestData
          )

          if (data.success) {
            if (typeof this.location.id === 'number') {
              this.success = 'Location settings updated'

              this.$v.$reset()

              this.$emit('updated', data.location)
            } else {
              this.$router.push(`/locations/${data.location.id}/settings`)
            }
          }

          this.$nuxt.$loading.finish()
        } catch (error) {
          this.error = this.$getErrorMessage(error)
          if (this.user.role === 'technician') {
            this.$router.push({
              name: 'locations-all',
              query: {
                error: 'Location not found',
              },
            })
          }
          this.$nuxt.$loading.finish()
        }
      }
    },
  },
}
</script>
