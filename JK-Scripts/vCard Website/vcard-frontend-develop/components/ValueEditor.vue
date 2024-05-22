<template>
  <b-modal
    ref="value-editor-modal"
    title="Edit value"
    ok-title="Save"
    header-bg-variant="primary"
    header-text-variant="white"
    no-close-on-backdrop
    centered
    @ok="onSubmit"
  >
    <div class="row">
      <div class="col-12">
        <b-input-group>
          <b-form-textarea
            ref="value-input"
            v-model="value"
            placeholder="Enter a value"
            rows="4"
            autofocus
          />
        </b-input-group>
      </div>
    </div>
  </b-modal>
</template>

<script>
export default {
  data() {
    return {
      value: null,
      key: null,
    }
  },
  methods: {
    open({ key, value }) {
      this.key = key
      this.value = value

      this.$refs['value-editor-modal'].show()
    },
    onSubmit() {
      this.$emit('update', {
        key: this.key,
        value: this.value,
      })
    },
  },
}
</script>
