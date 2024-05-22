<template>
  <div class="bg-white">
    <nuxt />
  </div>
</template>

<script>
export default {
  middleware({ store, redirect }) {
    if (!store.getters['user/token']) {
      return redirect('/sign-in')
    }
  },
}
</script>
