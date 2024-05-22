<template>
  <b-modal
    ref="attachment-modal"
    :title="
      typeof attachment.id === 'number' ? 'Edit attachment' : 'Add attachment'
    "
    :ok-title="typeof attachment.id === 'number' ? 'Save' : 'Add'"
    header-bg-variant="primary"
    header-text-variant="white"
    size="md"
    no-close-on-backdrop
    centered
    @ok.prevent="onSubmit"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <div class="row">
      <div class="col-12">
        <b-form-group label="Title">
          <b-form-input
            v-model.trim="attachment.title"
            :state="
              $v.attachment.title.$dirty ? !$v.attachment.title.$error : null
            "
            placeholder="Enter title"
            autofocus
          />
        </b-form-group>
      </div>

      <div class="col-12">
        <b-input-group v-if="typeof attachment.file === 'string'">
          <b-form-input
            v-model="
              attachment.file.split('/')[attachment.file.split('/').length - 1]
            "
            :state="
              $v.attachment.file.$dirty ? !$v.attachment.file.$error : null
            "
            placeholder="Select a file"
            readonly
          />

          <b-input-group-append>
            <b-button
              variant="dark"
              style="color: #495057; background-color: #e9ecef;"
              @click="clearFile"
            >
              Browse
            </b-button>
          </b-input-group-append>
        </b-input-group>

        <b-form-group v-if="typeof attachment.file !== 'string'" label="File">
          <b-form-file
            ref="file-input"
            v-model="attachment.file"
            :state="
              $v.attachment.file.$dirty ? !$v.attachment.file.$error : null
            "
            placeholder="Select a file"
          />
        </b-form-group>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  props: {
    referenceType: {
      type: String,
      required: true,
    },
    referenceId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      error: null,
      attachment: {
        title: null,
        file: null,
      },
    }
  },
  validations() {
    return {
      attachment: {
        title: { required },
        file: { required },
      },
    }
  },
  methods: {
    showModal(attachment) {
      this.error = null

      if (attachment) {
        this.attachment = Object.assign({}, attachment)
      } else {
        this.attachment = {
          title: null,
          file: null,
        }
      }
      this.$v.$reset()
      this.$refs['attachment-modal'].show()
    },
    async clearFile() {
      this.attachment.file = null

      await this.$nextTick()

      this.$refs['file-input'].$el.children[0].click()
    },
    getBase64(file) {
      return new Promise((resolve, reject) => {
        try {
          const reader = new FileReader()

          reader.addEventListener('load', () => {
            resolve(reader.result)
          })

          reader.readAsDataURL(file)
        } catch (error) {
          reject(error)
        }
      })
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            attachment: {
              ...this.attachment,
              referenceType: this.referenceType,
              referenceId: this.referenceId,
            },
          }

          if (typeof this.attachment.id === 'number') {
            requestData.id = this.attachment.id
          }

          if (
            this.attachment.file !== null &&
            typeof this.attachment.file !== 'string'
          ) {
            requestData.attachment.type = this.attachment.file.type.split(
              '/'
            )[1]
            requestData.file = await this.getBase64(this.attachment.file)
          }

          const { data } = await this.$axios.post(
            typeof this.attachment.id === 'number'
              ? '/app/attachments/update'
              : '/app/attachments/create',
            requestData
          )

          if (data.success) {
            if (typeof this.attachment.id === 'number') {
              this.$emit('updated', data.attachment)
            } else {
              this.$emit('added', data.attachment)
            }

            this.$refs['attachment-modal'].hide()
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
