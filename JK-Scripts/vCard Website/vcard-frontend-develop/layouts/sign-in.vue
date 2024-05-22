<template>
  <div>
    <nuxt />
  </div>
</template>

<script>
export default {
  middleware({ store, redirect }) {
    if (store.getters['user/token']) {
      return redirect('/')
    }
  },
}
</script>
