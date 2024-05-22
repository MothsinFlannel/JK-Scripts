<template>
  <b-card header="Emails" class="mb-4 shadow-sm" no-body>
    <b-list-group flush>
      <b-list-group-item v-if="$isAllowed('app/emails/create')">
        <b-input-group>
          <b-form-input
            v-model.trim="newEmail"
            :state="$v.newEmail.$dirty ? !$v.newEmail.$error : null"
            type="email"
            placeholder="Enter email"
          />

          <b-input-group-append>
            <b-button type="button" variant="primary" @click="addEmail">
              Add new email
            </b-button>
          </b-input-group-append>

          <b-form-invalid-feedback v-if="$v.newEmail.$error">
            Please provide a valid email address.
          </b-form-invalid-feedback>
        </b-input-group>
      </b-list-group-item>

      <b-list-group-item>
        <b-alert :show="typeof error === 'string'" variant="danger">
          {{ error }}
        </b-alert>

        <b-table
          :fields="emailsTableFields"
          :items="emails"
          head-variant="light"
          thead-class="text-uppercase"
          :empty-text="isLoading ? '' : 'No data available in table'"
          show-empty
          responsive
          bordered
          striped
        >
          <template #cell(actions)="data">
            <div
              class="d-flex flex-nowrap align-items-center"
              style="margin: -0.25rem;"
            >
              <b-button
                type="button"
                variant="outline-light"
                size="sm"
                class="mb-2"
                @click="deleteEmail(data.item)"
              >
                <svg
                  width="1em"
                  height="21px"
                  viewBox="0 0 16 16"
                  class="bi bi-trash"
                  fill="currentColor"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"
                  />
                  <path
                    fill-rule="evenodd"
                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"
                  />
                </svg>
              </b-button>
            </div>
          </template>
        </b-table>
      </b-list-group-item>
    </b-list-group>

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
  </b-card>
</template>

<script>
import { required, email } from 'vuelidate/lib/validators'

export default {
  props: {
    locationId: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      newEmail: null,
      emails: null,
      isLoading: false,
      error: null,
    }
  },
  validations: {
    newEmail: {
      required,
      email,
    },
  },
  computed: {
    emailsTableFields() {
      const fields = [
        {
          key: 'email',
          label: 'Email',
        },
      ]

      if (this.$isAllowed('app/emails/delete')) {
        fields.push({
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        })
      }

      return fields
    },
  },
  created() {
    this.getEmails()
  },
  methods: {
    async getEmails() {
      try {
        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/emails/list', {
          locationId: this.locationId,
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.emails = data.results
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async addEmail() {
      this.error = null

      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.isLoading = true

          const { data } = await this.$axios.post('/app/emails/create', {
            email: {
              locationId: this.locationId,
              email: this.newEmail,
            },
          })

          if (data.success) {
            this.newEmail = null

            this.$v.$reset()

            this.getEmails()
          }

          this.isLoading = false
        } catch (error) {
          this.error = this.$getErrorMessage(error)

          this.isLoading = false
        }
      }
    },
    deleteEmail(item) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete the email ${item.email}?`,
          {
            buttonSize: 'sm',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              const { data } = await this.$axios.post('/app/emails/delete', {
                id: item.id,
              })

              if (data.success) {
                this.getEmails()
              }
            } catch (error) {
              this.$bvToast.toast(this.$getErrorMessage(error), {
                title: 'Error!',
                variant: 'danger',
              })
            }
          }
        })
        .catch((error) => {
          console.error(error)
        })
    },
  },
}
</script>
