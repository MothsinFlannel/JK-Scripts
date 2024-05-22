<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <div>Attachments</div>

        <div class="d-flex align-items-center text-nowrap">
          <b-button
            v-if="$isAllowed('app/attachments/create')"
            type="button"
            variant="outline-light"
            @click="createAttachment"
          >
            New Attachment
          </b-button>
        </div>
      </div>
    </template>

    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
    </b-alert>

    <b-table
      :fields="tableFields"
      :items="attachments"
      head-variant="light"
      thead-class="text-uppercase"
      :empty-text="isLoading ? '' : 'No attachments'"
      show-empty
      responsive
      bordered
      striped
    >
      <template #cell(createdAt)="data">
        {{
          data.item.createdAt &&
          $dayjs(data.item.createdAt).format('MM/DD/YYYY h:mm A')
        }}
      </template>

      <template #cell(actions)="data">
        <div
          class="d-flex flex-nowrap align-items-center"
          style="margin: -0.25rem;"
        >
          <b-button
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2 mr-2"
            @click="viewAttachment(data.item)"
          >
            <svg
              width="1em"
              height="21px"
              viewBox="0 0 16 16"
              class="bi bi-eye"
              fill="currentColor"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"
              />
              <path
                fill-rule="evenodd"
                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"
              />
            </svg>
          </b-button>

          <b-button
            v-if="$isAllowed('app/attachments/update')"
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2 mr-2"
            @click="editAttachment(data.item)"
          >
            <svg
              width="1em"
              height="21px"
              viewBox="0 0 16 16"
              class="bi bi-pencil"
              fill="currentColor"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"
              />
            </svg>
          </b-button>

          <b-button
            v-if="$isAllowed('app/attachments/delete')"
            type="button"
            variant="outline-light"
            size="sm"
            class="mb-2 mr-2"
            @click="deleteAttachment(data.item)"
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

    <attachment-modal
      ref="attachment-modal"
      :reference-id="locationId"
      reference-type="location"
      @added="onAttachmentAdded"
      @updated="onAttachmentUpdated"
    />
  </b-card>
</template>

<script>
import AttachmentModal from '~/components/modals/Attachment'

export default {
  components: {
    AttachmentModal,
  },
  props: {
    locationId: {
      type: Number,
      required: true,
    },
    initialAttachments: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      attachments: this.initialAttachments,
      tableFields: [
        {
          key: 'title',
          label: 'Title',
        },
        {
          key: 'createdAt',
          label: 'Created',
        },
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
      isLoading: false,
      error: null,
    }
  },
  methods: {
    viewAttachment(attachment) {
      window.open(attachment.file, '_blank')
    },
    createAttachment() {
      this.$refs['attachment-modal'].showModal()
    },
    editAttachment(attachment) {
      this.$refs['attachment-modal'].showModal(attachment)
    },
    deleteAttachment(attachment) {
      this.$bvModal
        .msgBoxConfirm(
          `Are you sure you want to delete the attachment "${attachment.title}"?`,
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
                '/app/attachments/delete',
                {
                  id: attachment.id,
                }
              )

              if (data.success) {
                this.attachments = this.attachments.filter(
                  (a) => a.id !== attachment.id
                )
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
    onAttachmentAdded(attachment) {
      this.attachments.push(attachment)
    },
    onAttachmentUpdated(attachment) {
      const index = this.attachments.findIndex((a) => a.id === attachment.id)

      this.attachments.splice(index, 1, attachment)
    },
  },
}
</script>
