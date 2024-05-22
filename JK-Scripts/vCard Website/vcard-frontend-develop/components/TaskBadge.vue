<template>
  <div class="px-2 rounded-full text-xs" :class="`bg-${color} text-${color}`">
    <span class="dot rounded-full mr-1" :class="`dot-${color}`"></span>
    <span>
      <slot></slot>
    </span>
  </div>
</template>
<script>
export default {
  props: {
    color: {
      type: String,
      default: 'teal',
    },
  },
}
</script>

<style scoped>
.bg-purple {
  background: rgb(250, 245, 255);
}

.dot-purple {
  background: rgb(183, 148, 244);
}

.text-purple {
  color: rgb(107, 70, 193);
}

.rounded-full {
  border-radius: 9999px;
}

.dot {
  width: 0.5rem;
  height: 0.5rem;
  display: inline-block;
}
.text-xs {
  font-size: 0.75rem;
}
</style>
