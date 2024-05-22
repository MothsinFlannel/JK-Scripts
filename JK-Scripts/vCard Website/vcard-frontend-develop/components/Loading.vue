<template>
  <div
    v-if="isVisible"
    class="align-items-center justify-content-center position-fixed"
    style="
      display: flex;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1100;
      background-color: rgba(255, 255, 255, 0.5);
    "
  >
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isVisible: false,
      timeoutID: undefined,
    }
  },
  methods: {
    start() {
      if (typeof this.timeoutID === 'number') {
        window.clearTimeout(this.timeoutID)
      }
      this.timeoutID = window.setTimeout(() => {
        this.isVisible = true
      }, 350)
    },
    finish() {
      if (typeof this.timeoutID === 'number') {
        window.clearTimeout(this.timeoutID)
      }
      this.isVisible = false
    },
  },
}
</script>
