<template>
  <b-modal
    ref="task-modal"
    :title="typeof task.id === 'number' ? 'Edit task' : 'Add task'"
    :ok-title="typeof task.id === 'number' ? 'Save' : 'Add'"
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
        <b-form-group label="Summary">
          <b-form-input
            v-model.trim="task.summary"
            :state="$v.task.summary.$dirty ? !$v.task.summary.$error : null"
            placeholder="Enter summary"
            autofocus
          />
        </b-form-group>
      </div>

      <div class="col-12">
        <b-form-group label="Description">
          <b-form-textarea
            v-model.trim="task.description"
            :state="
              $v.task.description.$dirty ? !$v.task.description.$error : null
            "
            placeholder="Enter description"
            rows="6"
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
    boardId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      error: null,
      task: {
        summary: null,
        description: null,
      },
      column: null,
    }
  },
  validations() {
    return {
      task: {
        summary: {
          required,
        },
        description: {},
      },
    }
  },
  methods: {
    showModal(task, column) {
      this.error = null
      this.column = column

      if (task) {
        this.task = Object.assign({}, task)
      } else {
        this.task = {
          summary: null,
          description: null,
        }
      }

      this.$v.$reset()
      this.$refs['task-modal'].show()
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            task: {
              boardId: this.boardId,
              ...this.task,
            },
          }

          if (typeof this.task.id === 'number') {
            requestData.id = this.task.id
            requestData.task.column = undefined
          } else {
            requestData.task.column = this.column
          }

          const { data } = await this.$axios.post(
            typeof this.task.id === 'number'
              ? '/app/tasks/update'
              : '/app/tasks/create',
            requestData
          )

          if (data.success) {
            this.$emit('updated')

            this.$refs['task-modal'].hide()
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
