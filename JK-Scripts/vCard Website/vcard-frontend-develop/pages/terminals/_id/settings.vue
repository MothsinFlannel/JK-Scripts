<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Setup Terminal '#{{ terminal.number }}'</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Terminals', link: '/terminals/all' },
          {
            title: `#${terminal.number}`,
            link: `/terminals/${$route.params.id}`,
          },
          {
            title: `Setup \'#${terminal.number}\'`,
          },
        ]"
      />
    </div>

    <TerminalSettings
      :initial-terminal="terminal"
      @updated="onTerminalUpdated"
    />

    <div class="row">
      <div class="col-md-6 col-12">
        <TerminalMovementHistory
          ref="terminal-movement-history"
          :terminal-id="+$route.params.id"
        />
      </div>

      <div class="col-md-6 col-12">
        <TerminalMoveToWarehouse
          v-if="$isAllowed('app/terminals/movements') && !terminal.warehouseId"
          :initial-terminal="terminal"
          @updated="onTerminalUpdated"
        />
        <TerminalMoveFromWarehouse
          v-if="$isAllowed('app/terminals/movements') && terminal.warehouseId"
          :initial-terminal="terminal"
          @updated="onTerminalUpdated"
        />
      </div>
    </div>
    <destructiveSettings
      v-if="$isAllowed('app/terminals/delete')"
      @deleted="onTerminalDeleted"
    />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import DestructiveSettings from '~/components/DestructiveSettings'
import TerminalSettings from '~/components/TerminalSettings'
import TerminalMovementHistory from '~/components/TerminalMovementHistory'
import TerminalMoveToWarehouse from '~/components/TerminalMoveToWarehouse'
export default {
  components: {
    Breadcrumb,
    DestructiveSettings,
    TerminalSettings,
    TerminalMovementHistory,
    TerminalMoveToWarehouse,
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
          mode: 'spa',
        })
    }
  },
  data() {
    return {
      error: null,
      terminal: null,
    }
  },
  methods: {
    onTerminalUpdated(terminal) {
      this.$refs['terminal-movement-history'].getMovements()
      this.terminal = Object.assign({}, terminal)
    },
    onTerminalDeleted() {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete the terminal #${this.terminal.number}?`,
          {
            buttonSize: 'sm',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              const { data } = await this.$axios.post('/app/terminals/delete', {
                id: this.terminal.id,
              })

              if (data.success) {
                this.$router.push({
                  name: 'terminals-all',
                })
              }
            } catch (error) {
              this.$bvToast.toast(this.$getErrorMessage(error), {
                title: 'Error!',
                variant: 'danger',
              })
            }
          }
        })
        .catch((error) => {
          console.error(error)
        })
    },
  },
}
</script>
