<template>
  <div v-if="jobs">
    <b-alert
      v-for="job of jobs"
      :key="job.id"
      :variant="getVariant(job.state)"
      class="d-flex align-items-center"
      show
    >
      <div class="mr-auto">{{ job.title }}</div>

      <div v-if="job.state === 'pending'" class="text-info">
        Pending...
      </div>
      <div v-if="job.state === 'in-progress'" class="text-info">
        {{ job.progress }}%
      </div>
      <div v-if="job.state === 'succeeded'">
        <BButton :href="job.output" target="_blank" variant="success">
          Download
        </BButton>
      </div>
      <div v-if="job.state === 'failed'">{{ job.output }}</div>
      <DeleteButton
        v-if="job.state === 'in-progress' || job.state === 'pending'"
        button-classes="mb-0 ml-2"
        @click="onDeleteExport(job.id)"
      />
    </b-alert>
  </div>
</template>

<script>
import DeleteButton from '@/components/buttons/DeleteButton'
export default {
  components: {
    DeleteButton,
  },
  props: {
    category: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      timerId: null,
      jobs: null,
      jobsCount: 0,
      isLoading: false,
      tableFields: [
        {
          key: 'id',
          label: '#',
        },
        {
          key: 'initiatorId',
          label: 'Initiator',
        },
        {
          key: 'title',
          label: 'Title',
        },
        {
          key: 'createdAt',
          label: 'Created',
        },
        {
          key: 'endedAt',
          label: 'Ended',
        },
        {
          key: 'result',
          label: 'Result',
        },
      ],
    }
  },
  mounted() {
    this.getJobs()

    this.timerId = window.setInterval(() => {
      this.getJobs()
    }, 5000)
  },
  beforeDestroy() {
    window.clearInterval(this.timerId)
  },
  methods: {
    onDeleteExport(id) {
      this.$bvModal
        .msgBoxConfirm(`Are you sure you want to delete export #${id}?`, {
          title: 'Delete export',
          headerBgVariant: 'danger',
          centered: true,
          okTitle: 'Delete',
        })
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post('/app/jobs/cancel', {
                id,
              })

              this.isLoading = false

              if (data.success) {
                await this.getJobs()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
    async getJobs() {
      try {
        this.isLoading = true

        const { data } = await this.$axios.post('/app/jobs/recent', {
          category: this.category,
        })

        if (data.success) {
          this.jobs = data.results
          this.jobsCount = data.count
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.isLoading = false
      }
    },
    getVariant(state) {
      if (state === 'pending' || state === 'in-progress') {
        return 'primary'
      } else if (state === 'succeeded') {
        return 'success'
      } else if (state === 'failed') {
        return 'danger'
      }

      return 'dark'
    },
  },
}
</script>
