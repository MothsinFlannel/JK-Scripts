<template>
  <b-form-select
    :value="statusValue"
    :options="options"
    @input="onChangeInvoiceStatus"
  />
</template>

<script>
export default {
  props: {
    value: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      statusValue: null,
      options: [
        { value: null, text: 'All' },
        { value: 'paid', text: 'Paid' },
        { value: 'partial', text: 'Partial' },
        { value: 'unpaid', text: 'Unpaid' },
        // { value: 'incomplete', text: 'Incomplete' },
      ],
    }
  },
  methods: {
    onChangeInvoiceStatus(status) {
      this.$emit('input', status)
    },
  },
}
</script>
