<template>
  <b-modal
    ref="board-modal"
    :title="typeof board.id === 'number' ? 'Edit board' : 'Add board'"
    :ok-title="typeof board.id === 'number' ? 'Save' : 'Add'"
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
            v-model.trim="board.title"
            :state="$v.board.title.$dirty ? !$v.board.title.$error : null"
            placeholder="Enter title"
            autofocus
          />
        </b-form-group>
      </div>

      <div class="col-12">
        <label>Columns</label>
      </div>

      <div v-for="(column, index) of board.columns" :key="index" class="col-12">
        <div class="d-flex align-items-center">
          <b-form-input
            :value="column"
            :state="
              $v.board.columns.$each[index].$dirty
                ? !$v.board.columns.$each[index].$error
                : null
            "
            placeholder="Enter column title"
            class="mb-2"
            @change="changeColumn(index, $event)"
          />

          <DeleteButton class="ml-2" @click="removeColumn(index)" />
        </div>
      </div>

      <div class="col-12 mb-3">
        <b-button type="button" variant="primary" block @click="addColumn">
          Add column
        </b-button>
      </div>

      <div class="col-12">
        <b-form-checkbox
          v-model="board.isActive"
          :state="$v.board.isActive.$dirty ? !$v.board.isActive.$error : null"
          :value="true"
          :unchecked-value="false"
        >
          Active
        </b-form-checkbox>
      </div>
    </div>
  </b-modal>
</template>

<script>
import { required, minLength } from 'vuelidate/lib/validators'
import DeleteButton from '@/components/buttons/DeleteButton'

export default {
  components: {
    DeleteButton,
  },
  data() {
    return {
      error: null,
      board: {
        title: null,
        isActive: true,
        columns: [''],
      },
    }
  },
  validations() {
    return {
      board: {
        title: {
          required,
        },
        isActive: {},
        columns: {
          required,
          minLength: minLength(1),
          $each: {
            required,
          },
        },
      },
    }
  },
  watch: {
    'board.columns'(newValue) {
      if (newValue.length === 0) {
        this.addColumn()
      }
    },
  },
  methods: {
    showModal(board) {
      this.error = null

      if (board) {
        this.board = Object.assign({}, board)
      } else {
        this.board = {
          title: null,
          isActive: true,
          columns: [''],
        }
      }
      this.$v.$reset()
      this.$refs['board-modal'].show()
    },
    changeColumn(index, column) {
      this.board.columns.splice(index, 1, column)
    },
    addColumn() {
      this.board.columns.push('')
    },
    removeColumn(index) {
      this.board.columns.splice(index, 1)
    },
    async onSubmit() {
      this.error = null
      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.$nuxt.$loading.start()

          const requestData = {
            board: {
              ...this.board,
            },
          }

          if (typeof this.board.id === 'number') {
            requestData.id = this.board.id
          }

          const { data } = await this.$axios.post(
            typeof this.board.id === 'number'
              ? '/app/boards/update'
              : '/app/boards/create',
            requestData
          )

          if (data.success) {
            this.$emit('updated')

            this.$refs['board-modal'].hide()
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
