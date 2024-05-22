<template>
  <b-card class="mb-4 shadow-sm">
    <template #header>
      <div
        class="d-flex align-items-baseline justify-content-between flex-wrap"
      >
        <span class="m-0">Boards</span>
      </div>
    </template>

    <b-alert :show="typeof error === 'string'" variant="danger">
      {{ error }}
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
            v-if="$route.query.limit > 0 && boardsCount > +$route.query.limit"
            :value="+$route.query.page"
            :total-rows="boardsCount"
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

      <div class="d-flex align-items-center align-self-end mt-3">
        <b-button
          type="button"
          variant="outline-light"
          class="align-self-end text-nowrap"
          size="sm"
          @click="addBoard"
        >
          New Board
        </b-button>
      </div>
    </div>

    <b-table
      :fields="boardsTableFields"
      :items="boards"
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
      <template #cell(title)="data">
        <nuxt-link :to="`/boards/${data.item.id}`">
          {{ data.item.title }}
        </nuxt-link>
      </template>

      <template #cell(actions)="data">
        <div
          class="d-flex flex-nowrap align-items-center"
          style="margin: -0.25rem;"
        >
          <EditButton
            class="mb-2 mr-2 btn-link-hover"
            @click="editBoard(data.item)"
          />

          <DeleteButton @click="deleteBoard(data.item)" />
        </div>
      </template>
    </b-table>

    <TablePagination :count="boardsCount" />

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

    <BoardModal ref="board-modal" @updated="getBoards" />
  </b-card>
</template>

<script>
import { mapActions } from 'vuex'

import TablePagination from '@/components/TablePagination'
import BoardModal from '@/components/modals/Board'
import EditButton from '@/components/buttons/EditButton'
import DeleteButton from '@/components/buttons/DeleteButton'

export default {
  components: {
    TablePagination,
    BoardModal,
    EditButton,
    DeleteButton,
  },
  data() {
    return {
      boardsTableFields: [
        {
          key: 'id',
          label: '#',
          sortable: true,
          thStyle: { width: '1%' },
        },
        {
          key: 'title',
          label: 'Title',
          sortable: true,
        },
        {
          key: 'actions',
          label: 'Manage',
          tdClass: 'pt-2 pb-0',
          thStyle: { width: '1%' },
        },
      ],
      boards: null,
      boardsCount: 0,
      isLoading: false,
      error: null,
    }
  },
  computed: {
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
      this.getBoards()
    },
  },
  created() {
    this.getBoards()
  },
  methods: {
    ...mapActions({
      getBoardsForMenu: 'boards/getBoards',
    }),
    async getBoards() {
      try {
        this.getBoardsForMenu()

        this.error = null
        this.isLoading = true

        const { data } = await this.$axios.post('/app/boards/list', {
          offset: (+this.$route.query.page - 1) * +this.$route.query.limit,
          limit: +this.$route.query.limit,
          sort: this.$route.query.sort,
        })

        if (data.success) {
          this.boards = data.results
          this.boardsCount = data.count
        }

        this.isLoading = false
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
    deleteBoard(board) {
      this.$bvModal
        .msgBoxConfirm(`Are you sure you want to delete ${board.title}?`, {
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
                id: board.id,
              })

              this.isLoading = false

              if (data.success) {
                await this.getBoards()
              }
            } catch (error) {
              this.error = this.$getErrorMessage(error)

              this.isLoading = false
            }
          }
        })
        .catch((error) => console.error(error))
    },
    addBoard() {
      this.$refs['board-modal'].showModal(null)
    },
    editBoard(board) {
      this.$refs['board-modal'].showModal(board)
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
  },
}
</script>
