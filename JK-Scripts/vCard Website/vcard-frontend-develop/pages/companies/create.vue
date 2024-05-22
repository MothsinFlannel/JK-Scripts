<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">New Company</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Companies', link: '/companies' },
          {
            title: 'New Company',
          },
        ]"
      />
    </div>

    <CompanySettings />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import CompanySettings from '~/components/CompanySettings'

export default {
  components: {
    Breadcrumb,
    CompanySettings,
  },
}
</script>
