<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">
        {{ location.name }},
        <span class="text-uppercase">{{ location.state }}</span>
      </h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Locations', link: '/locations/all' },
          {
            title: `${location.name}, ${location.state.toUpperCase()}`,
          },
        ]"
      />
    </div>

    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-12 col-xl-5 mb-4">
        <b-card class="shadow-sm" no-body>
          <b-list-group flush>
            <b-list-group-item>
              <div class="d-flex flex-column">
                <h3>
                  {{ location.name }},
                  <span class="text-uppercase">{{ location.state }}</span>
                </h3>

                <span v-if="location.isLive === false" class="text-danger">
                  Test Mode Enabled
                </span>

                <span>
                  {{ location.address }}, {{ $cityStateZip(location) }}
                </span>

                <span v-if="isFullMode">Tel: {{ location.contactPhone }}</span>

                <span v-if="isFullMode">
                  Contact Name: {{ location.contactName }}
                </span>

                <span v-if="isFullMode" class="d-flex">
                  POS Serial #: {{ location.serial }}

                  <b-button
                    v-if="
                      $isAllowed('app/audit/by-attribute') && location.serial
                    "
                    type="button"
                    variant="outline-light"
                    size="sm"
                    class="d-inline-flex align-items-center btn-xs ml-auto"
                    @click="openHistory"
                  >
                    <svg
                      height="12px"
                      version="1.1"
                      viewBox="0 0 20 21"
                      width="20px"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <g
                        fill="none"
                        fill-rule="evenodd"
                        stroke="none"
                        stroke-width="1"
                      >
                        <g
                          fill="currentColor"
                          opacity="1"
                          transform="translate(-464.000000, -254.000000)"
                        >
                          <g
                            id="history"
                            transform="translate(464.000000, 254.500000)"
                          >
                            <path
                              d="M10.5,0 C7,0 3.9,1.9 2.3,4.8 L0,2.5 L0,9 L6.5,9 L3.7,6.2 C5,3.7 7.5,2 10.5,2 C14.6,2 18,5.4 18,9.5 C18,13.6 14.6,17 10.5,17 C7.2,17 4.5,14.9 3.4,12 L1.3,12 C2.4,16 6.1,19 10.5,19 C15.8,19 20,14.7 20,9.5 C20,4.3 15.7,0 10.5,0 L10.5,0 Z M9,5 L9,10.1 L13.7,12.9 L14.5,11.6 L10.5,9.2 L10.5,5 L9,5 L9,5 Z"
                            />
                          </g>
                        </g>
                      </g>
                    </svg>

                    <span>History</span>
                  </b-button>
                </span>

                <span v-if="isFullMode"
                  >Operator: {{ location.operatorEmail }}</span
                >

                <span v-if="isFullMode">
                  License #: {{ location.licenseNumber }}
                </span>

                <span v-if="isFullMode">GPS #: {{ location.gpsNumber }}</span>
              </div>
            </b-list-group-item>

            <b-list-group-item v-if="isFullMode">
              <div class="d-flex flex-column">
                <span>Arrears: {{ $getFormattedPrice(location.due) }}</span>
              </div>
            </b-list-group-item>

            <b-list-group-item
              v-if="
                isFullMode ||
                ($isAllowed('app/locations/update') &&
                  user.role !== 'location' &&
                  user.role !== 'technician') ||
                (user.role === 'technician' && !location.isLive)
              "
            >
              <b-button
                v-if="
                  ($isAllowed('app/locations/update') &&
                    user.role !== 'location' &&
                    user.role !== 'technician') ||
                  (user.role === 'technician' && !location.isLive)
                "
                :to="`/locations/${$route.params.id}/settings`"
                type="button"
                variant="secondary"
                class="mr-2"
              >
                Edit Settings
              </b-button>

              <b-button
                v-if="isFullMode"
                :to="`/invoices?location=${$route.params.id}`"
                type="button"
                variant="secondary"
              >
                Statements
              </b-button>
            </b-list-group-item>
          </b-list-group>
        </b-card>
      </div>

      <div v-if="isFullMode" class="col-12 col-xl-7 mb-4">
        <LocationExtra
          :location="location"
          @update="({ key, value }) => (location[key] = value)"
        />
      </div>
    </div>

    <LocationAttachments
      v-if="user.role !== 'technician'"
      :location-id="location.id"
      :initial-attachments="location.attachments"
    />

    <LocationClerks :location="location" />

    <TerminalInformation v-if="isFullMode" :location="location" />

    <LocationSoftware v-if="isFullMode" :location="location" />

    <RevenueReport :location="location" :location-id="+$route.params.id" />

    <HistoryModal ref="history-modal" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumb from '~/components/Breadcrumb'
import TerminalInformation from '~/components/TerminalInformation'
import LocationClerks from '~/components/LocationClerks'
import RevenueReport from '~/components/RevenueReport'
import LocationExtra from '~/components/LocationExtra'
import LocationAttachments from '~/components/LocationAttachments'
import LocationSoftware from '~/components/LocationSoftware'
import HistoryModal from '~/components/modals/History'

export default {
  components: {
    Breadcrumb,
    TerminalInformation,
    LocationClerks,
    RevenueReport,
    LocationExtra,
    LocationAttachments,
    LocationSoftware,
    HistoryModal,
  },
  async asyncData({ $axios, route, redirect }) {
    try {
      const { data } = await $axios.post('/app/locations/get', {
        id: +route.params.id,
      })

      if (data.success) {
        return {
          location: data.location,
        }
      }
    } catch (error) {
      if (error.response.data.exception.status === 400)
        return redirect({
          name: 'locations-all',
          query: {
            error: 'Location not found',
          },
        })
    }
  },
  data() {
    return {
      error: null,
      location: null,
    }
  },
  computed: {
    ...mapGetters({
      user: 'user/user',
    }),
    isFullMode() {
      return this.$isAllowed('app/locations/get+all')
    },
  },
  methods: {
    openHistory() {
      this.$refs['history-modal'].open('serial', this.location.serial)
    },
  },
}
</script>
