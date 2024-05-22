<template>
  <b-modal
    ref="software-title-modal"
    :title="
      typeof softwareTitle.id === 'number'
        ? 'Edit software title'
        : 'Add software title'
    "
    :ok-title="typeof softwareTitle.id === 'number' ? 'Save' : 'Add'"
    header-bg-variant="primary"
    header-text-variant="white"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-12">
        <b-form-group label="Software name">
          <b-form-input
            v-model.trim="softwareTitle.name"
            :state="
              $v.softwareTitle.name.$dirty
                ? !$v.softwareTitle.name.$error
                : null
            "
            placeholder="Enter software name"
            autofocus
          />
        </b-form-group>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  data() {
    return {
      error: null,
      softwareTitle: {
        name: null,
      },
    }
  },
  validations() {
    return {
      softwareTitle: {
        name: {
          required,
        },
      },
    }
  },
  methods: {
    showModal(softwareTitle) {
      this.error = null

      if (softwareTitle) {
        this.softwareTitle = Object.assign({}, softwareTitle)
      } else {
        this.softwareTitle = {
          name: null,
        }
      }

      this.$v.$reset()
      this.$refs['software-title-modal'].show()
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            item: this.softwareTitle,
          }

          if (typeof this.softwareTitle.id === 'number') {
            requestData.id = this.softwareTitle.id
          }

          const { data } = await this.$axios.post(
            typeof this.softwareTitle.id === 'number'
              ? '/app/software-titles/update'
              : '/app/software-titles/create',
            requestData
          )

          if (data.success) {
            this.$emit('updated')

            this.$refs['software-title-modal'].hide()
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
