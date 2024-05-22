<template>
  <b-card class="h-100 shadow-sm">
    <b-tabs content-class="mt-3 flex-1" class="h-100 tabs-wrapper">
      <b-tab title="Notes" active>
        <ValueEditorForm
          :value="location.notes"
          :can-edit="$isAllowed('app/locations/update')"
          name="notes"
          @update="onUpdate"
        />
      </b-tab>
      <b-tab title="Service Request">
        <ValueEditorForm
          ref="service-reqest-form"
          :value="serviceRequest"
          :can-edit="$isAllowed('app/locations/service-request')"
          label="Send"
          name="serviceRequest"
          @update="onUpdateServiceRequest"
        />
      </b-tab>
    </b-tabs>
  </b-card>
</template>

<script>
import { mapGetters } from 'vuex'
import ValueEditorForm from '~/components/ValueEditorForm'

export default {
  components: {
    ValueEditorForm,
  },
  props: {
    location: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      serviceRequest: null,
    }
  },
  computed: {
    ...mapGetters({
      user: 'user/user',
    }),
  },
  methods: {
    async onUpdateServiceRequest({ key, value }) {
      try {
        this.$nuxt.$loading.start()

        await this.$axios.post('/app/locations/service-request', {
          id: this.location.id,
          note: value || null,
        })

        this.serviceRequest = null
        this.$refs['service-reqest-form'].clear()
      } catch (error) {
        console.error(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
    async onUpdate({ key, value }) {
      try {
        this.$nuxt.$loading.start()

        await this.$axios.post('/app/locations/update', {
          id: this.location.id,
          location: {
            ...this.location,
            [key]: value || null,
          },
        })

        this.$emit('update', { key, value })
      } catch (error) {
        console.error(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
  },
}
</script>

<style>
.tabs-wrapper {
  display: flex;
  flex-direction: column;
}

.tabs-wrapper > .flex-1 {
  flex: 1;
}

.flex-1 > div {
  height: 100%;
}
</style>
