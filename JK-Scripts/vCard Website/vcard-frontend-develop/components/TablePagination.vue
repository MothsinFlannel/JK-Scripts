<template>
  <div v-if="$route.query.limit > 0" class="app-card__table-footer">
    <div class="mt-3 mr-3">
      Showing
      {{ Math.min((+$route.query.page - 1) * +$route.query.limit + 1, count) }}
      to
      {{ Math.min(+$route.query.page * +$route.query.limit, count) }}
      of {{ count }} entries
    </div>

    <b-pagination
      v-if="count > +$route.query.limit"
      :value="+$route.query.page"
      :total-rows="count"
      :per-page="+$route.query.limit"
      first-text="First"
      prev-text="Previous"
      next-text="Next"
      last-text="Last"
      class="mb-0 mt-3"
      pills
      @change="onCurrentPageChanged"
    />
  </div>
</template>

<script>
export default {
  props: {
    count: {
      type: Number,
      default: 0,
    },
  },
  methods: {
    onCurrentPageChanged(page) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: `${page}`,
        },
      })
    },
  },
}
</script>
