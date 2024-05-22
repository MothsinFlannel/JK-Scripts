<template>
  <b-form class="h-100 form" @submit.prevent="onSubmit">
    <div class="row text-field">
      <div class="col-12 h-100">
        <b-form-group class="h-100 mb-0">
          <b-form-textarea
            v-model="localValue"
            :disabled="!canEdit"
            placeholder="Enter a value"
            rows="8"
            class="h-100"
          >
          </b-form-textarea>
        </b-form-group>
      </div>
    </div>
    <div v-if="canEdit" class="row pt-3">
      <b-button type="submit" variant="primary" class="ml-auto mr-3">
        {{ label || 'Save' }}
      </b-button>
    </div>
  </b-form>
</template>

<script>
export default {
  props: {
    value: {
      type: [String, Array],
      required: false,
      default: null,
    },
    label: {
      type: String,
      default: null,
    },
    name: {
      type: String,
      required: true,
    },
    canEdit: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      localValue: this.value,
    }
  },
  watch: {
    value(val) {
      this.localValue = val
    },
  },
  methods: {
    clear() {
      this.localValue = null
    },
    onSubmit() {
      this.$emit('update', {
        key: this.name,
        value: this.localValue,
      })
    },
  },
}
</script>

<style scoped>
.form {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.form > .text-field {
  flex: 1;
}

>>> fieldset div {
  height: 100%;
}
</style>
