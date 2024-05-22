<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <P class="m-0">vCard Users</P>
        <div
          class="d-flex align-items-center text-nowrap"
          style="font-family: 'Circular Std Book', serif; color: #71748d;"
        >
          <div class="input-group">
            <b-form-input
              :value="$route.query.query"
              type="search"
              size="sm"
              class="form-control rounded-0 border-right-0"
              placeholder="Search"
              debounce="500"
              @update="onSearch"
            />
            <div class="input-group-append">
              <div class="input-group-text rounded-0">
                <svg
                  width="1em"
                  height="1.0625rem"
                  viewBox="0 0 16 16"
                  class="bi bi-search"
                  fill="currentColor"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"
                  />
                  <path
                    fill-rule="evenodd"
                    d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <b-alert :show="typeof success === 'string'" variant="success" dismissible>
      {{ success }}
    </b-alert>

    <div
      class="d-flex align-items-baseline justify-content-between mb-3 flex-wrap"
      style="margin-top: -16px;"
    >
      <div class="row mr-auto">
        <div class="col-12 d-flex flex-column">
          <div class="d-flex align-items-center mr-3 mb-1 mb-sm-0 mt-3">
            <div class="d-flex align-items-center">
              Show
              <b-form-select
                :value="+$route.query.limit"
                :options="[
                  { value: 10, text: '10' },
                  { value: 25, text: '25' },
                  { value: 50, text: '50' },
                  { value: 100, text: '100' },
                  { value: -1, text: 'All' },
                ]"
                size="sm"
                class="mx-1"
                @input="onLimitChanged"
              />
              entries
            </div>
          </div>

          <b-pagination
            v-if="$route.query.limit > 0 && usersCount > +$route.query.limit"
            :value="+$route.query.page"
            :total-rows="usersCount"
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
      </div>

      <div
        v-if="$isAllowed('app/users/create')"
        class="d-flex align-items-center align-self-end mt-3"
      >
        <b-button
          type="button"
          variant="outline-light"
          class="align-self-end text-nowrap"
          size="sm"
          to="/users/create"
        >
          New User
        </b-button>
      </div>
    </div>

    <scroll-wrapper ref="scroll-wrapper">
      <b-table
        :fields="userTableFields"
        :items="users"
        :sort-by="sortBy"
        :sort-desc="sortDesc"
        head-variant="light"
        thead-class="text-uppercase"
        :empty-text="isLoading ? '' : 'No data available in table'"
        show-empty
        responsive
        bordered
        striped
        no-footer-sorting
        no-local-sorting
        no-sort-reset
        @sort-changed="sortingChanged"
      >
        <template #cell(states)="data">
          {{
            data.item.operatesInStates && data.item.operatesInStates.join(', ')
          }}
        </template>

        <template #cell(role)="data">
          <span
            class="text-capitalize"
            :class="{
              'text-danger': data.item.role === 'admin',
            }"
            >{{ data.item.role }}</span
          >
        </template>

        <template #cell(createdAt)="data">
          {{
            data.item.createdAt &&
            $dayjs(data.item.createdAt).format('MM/DD/YYYY h:mm A')
          }}
        </template>

        <template #cell(isActive)="data">
          <span
            :class="{
              'text-danger': !data.item.isActive,
              'text-success': data.item.isActive,
            }"
          >
            {{ data.item.isActive ? 'Active' : 'Blocked' }}
          </span>
        </template>

        <template #cell(actions)="data">
          <div
            class="d-flex flex-nowrap align-items-center"
            style="margin: -0.25rem;"
          >
            <b-dropdown
              boundary="window"
              class="m-1"
              text="Manage"
              size="sm"
              variant="outline-light"
            >
              <b-dropdown-item @click="resetPassword(data.item)">
                Reset Password
              </b-dropdown-item>

              <b-dropdown-item
                v-if="data.item.isActive"
                @click="lockAccount(data.item)"
              >
                Lock Account
              </b-dropdown-item>

              <b-dropdown-item
                v-if="!data.item.isActive"
                @click="unlockAccount(data.item)"
              >
                Unlock Account
              </b-dropdown-item>

              <b-dropdown-item :to="`/users/${data.item.id}/settings`">
                Change user
              </b-dropdown-item>

              <!--<b-dropdown-item
              v-if="data.item.role !== 'admin'"
              @click="makeAdmin(data.item)"
            >
              Make Admin
            </b-dropdown-item>

            <b-dropdown-item
              v-if="data.item.role === 'admin'"
              @click="removeAdmin(data.item)"
            >
              Remove Admin
            </b-dropdown-item>-->

              <b-dropdown-divider />

              <b-dropdown-item @click="deleteUser(data.item)">
                Delete User
              </b-dropdown-item>
            </b-dropdown>
          </div>
        </template>
      </b-table>
    </scroll-wrapper>

    <div v-if="$route.query.limit > 0" class="app-card__table-footer">
      <div class="mt-3 mr-3 mb-1 mb-sm-0">
        Showing
        {{
          Math.min(
            (+$route.query.page - 1) * +$route.query.limit + 1,
            usersCount
          )
        }}
        to
        {{ Math.min(+$route.query.page * +$route.query.limit, usersCount) }}
        of {{ usersCount }} entries
      </div>

      <b-pagination
        v-if="usersCount > +$route.query.limit"
        :value="+$route.query.page"
        :total-rows="usersCount"
        :per-page="+$route.query.limit"
        first-text="First"
        prev-text="Previous"
        next-text="Next"
        last-text="Last"
        class="mt-3 mb-0"
        pills
        @change="onCurrentPageChanged"
      />
    </div>

    <div
      v-if="isLoading"
      class="d-flex align-items-center justify-content-center position-absolute"
      style="
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(255, 255, 255, 0.5);
        z-index: 99;
      "
    >
      <b-spinner variant="primary" label="Loading..." />
    </div>

    <ResetPasswordModal
      ref="reset-password-modal"
      @updated="onPasswordChanged"
    />
  </b-card>
</template>

<script>
import { mapGetters } from 'vuex'

import ResetPasswordModal from '~/components/modals/ResetPassword'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    ResetPasswordModal,
    ScrollWrapper,
  },
  data() {
    return {
      users: null,
      usersCount: 0,
      isLoading: false,
      error: null,
      success: null,
    }
  },
  computed: {
    ...mapGetters({
      companyId: 'base/companyId',
    }),
    userTableFields() {
      const userTableFields = [
        {
          key: 'fullName',
          label: 'Name',
          sortable: true,
        },
        {
          key: 'role',
          label: 'Role',
          sortable: true,
        },
        {
          key: 'states',
          label: 'States',
          sortable: true,
        },
        {
          key: 'email',
          label: 'Email',
          sortable: true,
        },
        {
          key: 'phone',
          label: 'Phone',
          sortable: true,
        },
        {
          key: 'locationsCount',
          label: '# Locations',
          sortable: false,
        },
        {
          key: 'createdAt',
          label: 'Created',
          sortable: true,
        },
        {
          key: 'isActive',
          label: 'Status',
          sortable: true,
        },
      ]

      if (this.$isAllowed('app/users/update')) {
        userTableFields.push({
          key: 'actions',
          label: 'Manage',
          tdClass: 'py-0',
          thStyle: { width: '1%' },
        })
      }

      return userTableFields
    },
    sortBy() {
      if (this.$route.query.sort.endsWith('+desc')) {
        return this.$route.query.sort.slice(0, -5)
      }
      if (this.$route.query.sort.endsWith('+asc')) {
        return this.$route.query.sort.slice(0, -4)
      }
      return this.$route.query.sort
    },
    sortDesc() {
      return this.$route.query.sort.endsWith('+desc')
    },
  },
  watch: {
    '$route.query'() {
      this.getUsers()
    },
    companyId() {
      this.getUsers()
    },
    error(val) {
      this.onShowErrorPopup(val)
    },
  },
  created() {
    this.getUsers()
  },
  methods: {
    onShowErrorPopup(error) {
      this.$bvToast.toast(error, {
        title: 'Error',
        variant: 'danger',
        solid: true,
      })
    },
    async getUsers() {
      try {
        this.error = null
        this.success = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/users/list', {
          filters: {
            query: this.$route.query.query,
            companyId: this.companyId,
          },
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success) {
          this.users = data.results
          this.usersCount = data.count
        }

        this.isLoading = false

        this.$refs['scroll-wrapper'].getScrollbarWidth()
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    onLimitChanged(limit) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: '1',
          limit: `${limit}`,
        },
      })
    },
    onCurrentPageChanged(page) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: `${page}`,
        },
      })
    },
    onSearch(query) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          page: '1',
          query,
        },
      })
    },
    sortingChanged(sort) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          sort: `${
            sort.sortDesc ? `${sort.sortBy}+desc` : `${sort.sortBy}+asc`
          }`,
        },
      })
    },
    resetPassword(user) {
      this.success = null

      this.$refs['reset-password-modal'].show(user)
    },
    onPasswordChanged(user) {
      window.scroll(0, 0)

      this.success = `Password for ${user.email} changed`
    },
    async lockAccount(user) {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/users/update', {
          id: user.id,
          user: {
            isActive: false,
          },
        })

        if (data.success) {
          user.isActive = false
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async unlockAccount(user) {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/users/update', {
          id: user.id,
          user: {
            isActive: true,
          },
        })

        if (data.success) {
          user.isActive = true
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async makeAdmin(user) {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/users/update', {
          id: user.id,
          user: {
            role: 'admin',
          },
        })

        if (data.success) {
          user.role = 'admin'
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async removeAdmin(user) {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/users/update', {
          id: user.id,
          user: {
            role: 'user',
          },
        })

        if (data.success) {
          user.role = 'user'
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    deleteUser(user) {
      this.$bvModal
        .msgBoxConfirm(`Are you sure you want to delete ${user.fullName}?`, {
          title: 'Delete User',
          headerBgVariant: 'danger',
          centered: true,
          okTitle: 'Delete',
        })
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post('/app/users/delete', {
                id: user.id,
              })

              this.isLoading = false

              if (data.success) {
                this.getUsers()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
  },
}
</script>
