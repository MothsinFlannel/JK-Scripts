<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">New User</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Users', link: '/users' },
          {
            title: 'New User',
          },
        ]"
      />
    </div>

    <UserSettings />
  </div>
</template>

<script>
import Breadcrumb from '~/components/Breadcrumb'
import UserSettings from '~/components/UserSettings'

export default {
  components: {
    Breadcrumb,
    UserSettings,
  },
}
</script>
