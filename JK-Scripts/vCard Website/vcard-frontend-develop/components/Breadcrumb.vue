<template>
  <div class="breadcrumbs">
    <template v-for="item in items">
      <nuxt-link
        v-if="item.link"
        :key="item.title"
        :to="item.link"
        class="breadcrumb-link"
      >
        {{ item.title }}
      </nuxt-link>

      <span v-if="!item.link" :key="item.title" class="breadcrumb-text">
        {{ item.title }}
      </span>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
}
</script>

<style lang="scss" scoped>
.breadcrumbs {
  font-size: 0.75rem;
}

.breadcrumb-link {
  position: relative;
  display: inline-block;
  padding-right: 1.375rem;
  color: #a6a6b7;
  text-decoration: none;

  &:hover {
    color: #5969ff;
  }

  &::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 1.125rem;
    height: 1.125rem;
    background-image: url('../assets/icons/chevron-right.svg');
    background-repeat: no-repeat;
    background-size: 1.125rem;
    background-position: center;
  }
}

.breadcrumb-text {
  color: #71728e;

  &:not(:last-child) {
    position: relative;
    display: inline-block;
    padding-right: 1.375rem;
    color: #a6a6b7;

    &::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 1.125rem;
      height: 1.125rem;
      background-image: url('../assets/icons/chevron-right.svg');
      background-repeat: no-repeat;
      background-size: 1.125rem;
      background-position: center;
    }
  }
}
</style>
