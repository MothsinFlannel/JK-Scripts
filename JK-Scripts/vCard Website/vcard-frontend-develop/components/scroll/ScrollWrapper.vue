<template>
  <div class="mb-3">
    <vuescroll
      ref="default-scroll"
      :ops="defaultScrollOptions"
      @handle-scroll="handleDefaultScroll"
    >
      <slot></slot>
    </vuescroll>

    <div
      style="position: sticky; bottom: 0; margin-top: -26.8px; z-index: 1000;"
    >
      <vuescroll
        ref="sticky-scroll"
        :ops="stickyScrollOptions"
        @handle-scroll="handleStickyScroll"
      >
        <div :style="styles"></div>
      </vuescroll>
    </div>
  </div>
</template>

<script>
import vuescroll from 'vuescroll'

export default {
  components: {
    vuescroll,
  },
  data() {
    return {
      styles: {
        width: '200%',
        height: '10px',
      },
      defaultScrollOptions: {
        bar: {
          background: 'transparent',
          size: '20px',
          keepShow: true,
        },
        scrollPanel: {
          scrollingY: false,
        },
        vuescroll: {
          mode: 'native',
          zooming: false,
          locking: true,
          detectResize: true,
          disable: true,
        },
      },
      stickyScrollOptions: {
        bar: {
          background: '#7e7e7e',
          size: '15px',
          keepShow: true,
        },
        scrollPanel: {
          scrollingY: false,
        },
        vuescroll: {
          mode: 'native',
          zooming: false,
          locking: true,
          detectResize: true,
          disable: true,
        },
      },
    }
  },
  mounted() {
    this.getScrollbarWidth()
  },
  methods: {
    getScrollbarWidth() {
      const pannelWidth = this.$refs['default-scroll'].scrollPanelElm
        .scrollWidth
      this.styles.width = pannelWidth * 0.1 + pannelWidth + 'px'
    },
    handleDefaultScroll(verctical, horizontal) {
      const pannel = this.$refs['sticky-scroll']
      const x = horizontal.process * 100 + '%'

      pannel.scrollTo({ x }, 0)
    },
    handleStickyScroll(verctical, horizontal) {
      const pannel = this.$refs['default-scroll']
      const x = horizontal.process * 100 + '%'

      pannel.scrollTo({ x }, 0)
    },
  },
}
</script>
