<template>
  <div>
    <div class="mb-4">
      <div
        class="d-flex align-items-center justify-content-between"
        style="margin-bottom: -8px;"
      >
        <h3 class="mb-2">{{ board.title }}</h3>

        <div class="d-flex align-items-center">
          <EditButton class="mb-2 mr-2 btn-link-hover" @click="editBoard()" />

          <DeleteButton @click="deleteBoard()" />
        </div>
      </div>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          { title: 'Boards', link: '/boards' },
          { title: board.title },
        ]"
      />
    </div>

    <Tasks :board="board" />

    <BoardModal ref="board-modal" @updated="onUpdate" />
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import BoardModal from '@/components/modals/Board'
import EditButton from '@/components/buttons/EditButton'
import DeleteButton from '@/components/buttons/DeleteButton'
import Breadcrumb from '~/components/Breadcrumb'
import Tasks from '~/components/Tasks'

export default {
  components: {
    Breadcrumb,
    Tasks,
    BoardModal,
    EditButton,
    DeleteButton,
  },
  async asyncData({ $axios, route }) {
    try {
      const { data } = await $axios.post('/app/boards/get', {
        id: +route.params.id,
      })

      if (data.success) {
        return {
          board: data.board,
        }
      }
    } catch (error) {
      return {
        error,
      }
    }
  },
  data() {
    return {
      error: null,
      board: null,
    }
  },
  methods: {
    ...mapActions({
      getBoardsForMenu: 'boards/getBoards',
    }),
    deleteBoard() {
      this.$bvModal
        .msgBoxConfirm(`Are you sure you want to delete ${this.board.title}?`, {
          title: 'Delete board',
          headerBgVariant: 'danger',
          centered: true,
          okTitle: 'Delete',
        })
        .then(async (value) => {
          if (value) {
            try {
              this.isLoading = true

              const { data } = await this.$axios.post('/app/boards/delete', {
                id: this.board.id,
              })

              this.isLoading = false

              if (data.success) {
                this.getBoardsForMenu()

                this.$router.push('/')
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
    editBoard() {
      this.$refs['board-modal'].showModal(this.board)
    },
    async onUpdate() {
      try {
        this.getBoardsForMenu()

        const { data } = await this.$axios.post('/app/boards/get', {
          id: +this.$route.params.id,
        })

        if (data.success) {
          this.board = data.board
        }
      } catch (error) {
        this.error = error
      }
    },
  },
}
</script>
