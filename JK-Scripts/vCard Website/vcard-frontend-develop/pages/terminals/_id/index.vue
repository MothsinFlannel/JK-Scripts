<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Terminal '#{{ terminal.number }}'</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Terminals', link: '/terminals/all' },
          {
            title: `#${terminal.number}`,
          },
        ]"
      />
    </div>

    <div class="row">
      <div class="col-12 col-xl-5">
        <b-card class="mb-4 shadow-sm" no-body>
          <b-list-group flush>
            <b-list-group-item>
              <div class="d-flex flex-column">
                <h3>#{{ terminal.number }}</h3>

                <span>
                  {{ terminal.location.address }},
                  {{ $cityStateZip(terminal.location) }}
                </span>
                <!--                <span v-if="terminal.warehouseId && terminal.warehouse"-->
                <!--                  >Warehouse:-->
                <!--                  <span>-->
                <!--                    {{ terminal.warehouse.name }}-->
                <!--                  </span></span-->
                <!--                >-->
              </div>
            </b-list-group-item>

            <b-list-group-item v-if="$isAllowed('app/terminals/update')">
              <b-button
                :to="`/terminals/${$route.params.id}/settings`"
                type="button"
                variant="secondary"
                class="mr-2"
              >
                Edit Settings
              </b-button>
            </b-list-group-item>
          </b-list-group>
        </b-card>
      </div>

      <div class="col-12 col-xl-7"></div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'

export default {
  components: {
    Breadcrumb,
  },
  async asyncData({ $axios, route, redirect }) {
    try {
      const { data } = await $axios.post('/app/terminals/get', {
        id: +route.params.id,
      })

      if (data.success) {
        return {
          terminal: data.terminal,
        }
      }
    } catch (error) {
      if (error.response.data.exception.status === 400)
        return redirect({
          name: 'terminal-all',
          query: {
            error: 'Terminal not found',
          },
        })
    }
  },
  data() {
    return {
      error: null,
      terminal: null,
    }
  },
  validations: {},
  computed: {},
  methods: {},
}
</script>
