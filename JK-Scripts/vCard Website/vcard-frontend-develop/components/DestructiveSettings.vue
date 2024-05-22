<template>
  <b-card
    header="
     Delete Section
    "
    class="mb-4 shadow-sm"
  >
    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-button type="button" variant="danger" @click="deleted">
      Delete
    </b-button>
    <div
      v-if="isLoading"
      class="d-flex align-items-center justify-content-center position-absolute"
      style="
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(255, 255, 255, 0.5);
        z-index: 99;
      "
    >
      <b-spinner variant="primary" label="Loading..." />
    </div>
  </b-card>
</template>

<script>
export default {
  props: {
    location: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      error: null,
    }
  },
  methods: {
    deleted() {
      this.$emit('deleted')
    },
  },
}
</script>
