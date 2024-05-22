<template>
  <div class="d-flex align-items-center flex-sm-nowrap flex-wrap">
    <span class="company-selector__label">Company:</span>
    <b-form-select
      :value="companyId"
      :options="companies"
      :disabled="isLoading"
      style="min-width: 15rem;"
      @input="onCompanyChanged"
    />
  </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'

export default {
  data() {
    return {
      isLoading: false,
      companies: [],
    }
  },
  computed: {
    ...mapGetters({
      companyId: 'base/companyId',
    }),
  },
  created() {
    this.getCompanies()
  },
  methods: {
    ...mapMutations({
      changeCompanyId: 'base/changeCompanyId',
    }),
    async getCompanies() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/companies/list', {
          offset: 0,
          limit: -1,
        })
        if (data.success) {
          this.companies = [
            { value: null, text: 'All companies' },
            ...data.results.map((type) => {
              return {
                value: type.id,
                text: type.name,
              }
            }),
          ]
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    onCompanyChanged(company) {
      this.changeCompanyId(company)
    },
  },
}
</script>

<style lang="scss" scoped>
.company-selector {
  &__label {
    white-space: nowrap;
    margin-right: 8px;

    @media (max-width: 767px) {
      display: none;
    }
  }
}
</style>
