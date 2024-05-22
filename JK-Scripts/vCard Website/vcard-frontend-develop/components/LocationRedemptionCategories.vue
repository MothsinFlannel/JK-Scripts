<template>
  <b-card class="mb-4 shadow-sm" no-body>
    <template #header>
      <div class="d-flex align-items-baseline justify-content-between">
        <P class="m-0">Redemption Categories</P>
        <div
          class="d-flex align-items-baseline text-nowrap"
          style="font-family: 'Circular Std Book', serif; color: #71748d;"
        >
          <b-button
            v-if="$isAllowed('app/categories/export')"
            type="button"
            variant="outline-light"
            size="sm"
            @click="exportCategories"
          >
            Export
          </b-button>
        </div>
      </div>
    </template>
    <b-list-group flush>
      <b-list-group-item v-if="$isAllowed('app/categories/create')">
        <b-input-group>
          <b-form-input
            v-model.trim="newCategory"
            :state="$v.newCategory.$dirty ? !$v.newCategory.$error : null"
            placeholder="Enter category name"
          />

          <b-input-group-append>
            <b-button type="button" variant="primary" @click="addCategory">
              Add new category
            </b-button>
          </b-input-group-append>

          <b-form-invalid-feedback v-if="$v.newCategory.$error">
            Please provide a valid category name.
          </b-form-invalid-feedback>
        </b-input-group>
      </b-list-group-item>

      <b-list-group-item>
        <b-alert :show="typeof error === 'string'" variant="danger">
          {{ error }}
        </b-alert>

        <b-table
          :fields="categoriesTableFields"
          :items="categories"
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
                @click="deleteCategory(data.item)"
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
import { required } from 'vuelidate/lib/validators'
import { saveAs } from 'file-saver'

export default {
  props: {
    locationId: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      newCategory: null,
      categories: null,
      isLoading: false,
      error: null,
    }
  },
  validations: {
    newCategory: {
      required,
    },
  },
  computed: {
    categoriesTableFields() {
      const fields = [
        {
          key: 'name',
          label: 'Category',
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
    this.getCategories()
  },
  methods: {
    async getCategories() {
      try {
        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/categories/list', {
          locationId: this.locationId,
          offset: 0,
          limit: -1,
        })

        if (data.success) {
          this.categories = data.results
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
    async addCategory() {
      this.error = null

      this.$v.$touch()

      if (!this.$v.$invalid) {
        try {
          this.isLoading = true

          const { data } = await this.$axios.post('/app/categories/create', {
            category: {
              locationId: this.locationId,
              name: this.newCategory,
            },
          })

          if (data.success) {
            this.newCategory = null

            this.$v.$reset()

            this.getCategories()
          }

          this.isLoading = false
        } catch (error) {
          this.error = this.$getErrorMessage(error)

          this.isLoading = false
        }
      }
    },
    deleteCategory(item) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete the category ${item.name}?`,
          {
            buttonSize: 'sm',
            centered: true,
            okTitle: 'Delete',
          }
        )
        .then(async (value) => {
          if (value) {
            try {
              const { data } = await this.$axios.post(
                '/app/categories/delete',
                {
                  id: item.id,
                }
              )

              if (data.success) {
                this.getCategories()
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
    async exportCategories() {
      try {
        this.$nuxt.$loading.start()

        const { data } = await this.$axios.post('/app/categories/export', {
          filters: {
            query: this.$route.query.query,
          },
          locationId: this.locationId,
        })

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, `categories-location${this.locationId}.csv`)
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
  },
}
</script>
