<template>
  <div class="w-100 h-100 row overflow-auto flex-nowrap mb-4">
    <div
      v-for="(column, index) in columns"
      :key="column.title"
      :class="{
        'mr-2': index !== columns.length - 1,
      }"
      class="rounded px-3 pb-3 column-width"
    >
      <p class="font-weight-normal">
        {{ column.title }}
      </p>

      <Draggable
        :list="column.tasks"
        :animation="200"
        ghost-class="ghost-card"
        group="tasks"
        @change="onChange($event, column.title)"
      >
        <task-card
          v-for="task in column.tasks"
          :key="task.id"
          :task="task"
          class="mt-3 cursor-move"
          @updated="getTasks"
        ></task-card>
      </Draggable>

      <div
        v-if="column.tasks.length === 0"
        class="bg-white-50 border rounded mt-3 p-3"
      >
        No tasks
      </div>

      <div class="py-3 pr-3 pl-0 cursor-pointer" @click="addTask(column.title)">
        Add a task
      </div>
    </div>

    <TaskModal ref="task-modal" :board-id="board.id" @updated="getTasks" />
  </div>
</template>

<script>
import Draggable from 'vuedraggable'
import TaskCard from '@/components/TaskCard.vue'
import TaskModal from '@/components/modals/Task.vue'

export default {
  components: {
    TaskModal,
    TaskCard,
    Draggable,
  },
  props: {
    board: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      columns: [],
    }
  },
  watch: {
    board() {
      this.getTasks()
    },
  },
  created() {
    this.getTasks()
  },
  methods: {
    async getTasks() {
      try {
        this.columns = this.board.columns.map((column) => ({
          title: column,
          tasks: [],
        }))

        const { data } = await this.$axios.post('/app/tasks/list', {
          boardId: this.board.id,
        })

        data.results.forEach((task) => {
          const column = this.columns.find((c) => c.title === task.column)

          column.tasks.push(task)
        })
      } catch (error) {
        console.error(error)
      }
    },
    async updateTask(task) {
      try {
        await this.$axios.post('/app/tasks/update', {
          id: task.id,
          task,
        })
      } catch (error) {
        console.error(error)
      }
    },
    addTask(column) {
      this.$refs['task-modal'].showModal(null, column)
    },
    onChange({ added, moved }, column) {
      if (moved) {
        this.updateTask({
          id: moved.element.id,
          position: moved.newIndex,
        })
      } else if (added) {
        this.updateTask({
          id: added.element.id,
          position: added.newIndex,
          column,
        })
      }
    },
  },
}
</script>

<style scoped>
.column-width {
  min-width: 320px;
  width: 320px;
}

.ghost-card {
  opacity: 0.5;
  background: #f7fafc;
  border: 1px solid #4299e1;
}
</style>
