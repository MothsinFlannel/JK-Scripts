<template>
  <div
    class="bg-white shadow rounded border border-white cursor-pointer"
    @click="editTask"
  >
    <div class="mx-3 mt-3 row flex-nowrap justify-content-between">
      <p>
        {{ task.summary }}
      </p>
    </div>

    <div class="row mt-2 justify-content-between align-items-center mx-3 mb-3">
      <span>{{ task.date }}</span>
      <TaskBadge v-if="task.type" :color="badgeColor">{{
        task.type
      }}</TaskBadge>
    </div>

    <TaskModal ref="task-modal" :board-id="task.boardId" @updated="onUpdate" />
  </div>
</template>

<script>
import TaskBadge from '@/components/TaskBadge.vue'
import TaskModal from '@/components/modals/Task.vue'

export default {
  components: {
    TaskBadge,
    TaskModal,
  },
  props: {
    task: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    badgeColor() {
      const mappings = {
        Design: 'purple',
        'Feature Request': 'purple',
        Backend: 'purple',
        QA: 'purple',
        default: 'purple',
      }
      return mappings[this.task.type] || mappings.default
    },
  },
  methods: {
    editTask() {
      this.$refs['task-modal'].showModal(this.task)
    },
    onUpdate() {
      this.$emit('updated')
    },
  },
}
</script>
