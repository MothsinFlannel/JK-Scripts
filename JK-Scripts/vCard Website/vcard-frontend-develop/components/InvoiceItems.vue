<template>
  <div>
    <div class="d-flex flex-wrap align-items-center justify-content-end mb-3">
      <b-button
        v-if="selectedItems && $isAllowed('app/invoices/update')"
        variant="outline-light"
        class="ml-2"
        size="sm"
        @click="removeItems"
      >
        Delete ({{ selectedItems }})
      </b-button>

      <b-button
        v-if="$isAllowed('app/invoices/update')"
        :disabled="extraItem"
        variant="outline-light"
        class="ml-2"
        size="sm"
        @click="onAddExtraInvoice"
      >
        Add Invoice Item
      </b-button>

      <PrintButton :to="`/invoice/${newInvoice.token}`" class="ml-2" />
    </div>

    <b-table
      :fields="columns"
      :items="invoiceItems"
      empty-text="No data"
      head-variant="light"
      show-empty
      responsive
      bordered
      striped
    >
      <template #head(select)>
        <b-form-checkbox
          v-if="$isAllowed('app/invoices/update')"
          :checked="allItemsSelected"
          :value="true"
          :unchecked-value="false"
          style="min-height: 21px;"
          @change="toggleAllItemsSelection"
        />
      </template>

      <template #cell(select)="{ item }">
        <b-form-checkbox
          v-if="$isAllowed('app/invoices/update')"
          v-model="item.selected"
          :disabled="
            item.type === 'manual' &&
            (item.totalIn === null || item.totalOut === null)
          "
          :value="true"
          :unchecked-value="false"
          class="mb-1"
        />
      </template>

      <template #cell(totalIn)="{ item }">
        <template
          v-if="isEdit && (item.type === 'manual' || item.type === 'automatic')"
        >
          <b-form-input
            v-model.number="item.totalIn"
            placeholder="Total In"
            @change="onChangeInvoiceItems"
          />
        </template>

        <template v-else>
          {{ $getFormattedPrice(item.totalIn || 0) }}
        </template>
      </template>

      <template #cell(totalOut)="{ item }">
        <template
          v-if="isEdit && (item.type === 'manual' || item.type === 'automatic')"
        >
          <b-form-input
            v-model.number="item.totalOut"
            placeholder="Total Out"
            @change="onChangeInvoiceItems"
          />
        </template>

        <template v-else>
          {{ $getFormattedPrice(item.totalOut || 0) }}
        </template>
      </template>

      <template #cell(revenue)="{ item }">
        <template v-if="isEdit && item.type === 'extra'">
          <b-form-input
            v-model.number="item.revenue"
            placeholder="Revenue"
            @change="onChangeInvoiceItems"
          />
        </template>

        <template v-else>
          {{ $getFormattedPrice(item.revenue) }}
        </template>
      </template>

      <template #cell(balance)="{ item }">
        <template>
          {{ $getFormattedPrice(item.balance) }}
        </template>
      </template>

      <template #custom-foot>
        <template v-if="extraItem">
          <b-tr>
            <b-td class="text-right"> </b-td>

            <b-td colspan="3" class="text-right">
              <b-form-input v-model="extraItem.title" placeholder="Title" />
            </b-td>

            <b-td>
              <b-form-input
                v-model.number="extraItem.revenue"
                placeholder="Amount"
              />
            </b-td>

            <b-td>
              <div class="d-flex">
                <b-button
                  variant="primary"
                  class="ml-2"
                  size="sm"
                  @click="saveExtraItem"
                >
                  Save
                </b-button>

                <b-button
                  variant="outline-light"
                  class="ml-2"
                  size="sm"
                  @click="cancelExtraItem"
                >
                  Cancel
                </b-button>
              </div>
            </b-td>
          </b-tr>
        </template>

        <b-tr>
          <b-td colspan="3"></b-td>
          <b-td>{{ $getFormattedPrice(newInvoice.totals.totalIn) }}</b-td>
          <b-td>{{ $getFormattedPrice(newInvoice.totals.totalOut) }}</b-td>
          <b-td>{{ $getFormattedPrice(newInvoice.totals.revenue) }}</b-td>
          <b-td>{{ $getFormattedPrice(newInvoice.totals.balance) }}</b-td>
          <b-td></b-td>
        </b-tr>

        <b-tr>
          <b-td colspan="6">
            <template v-if="isEdit">
              <b-form-textarea
                v-model="newInvoice.notes"
                placeholder="Notes"
                rows="2"
              />
            </template>

            <template v-else> Notes: {{ newInvoice.notes }} </template>
          </b-td>

          <b-td colspan="2" class="text-right">
            <div>
              Revenue to split:
              {{ $getFormattedPrice(newInvoice.totals.revenue) }}
            </div>
            <div>
              Due to Location:
              {{
                $getFormattedPrice(
                  newInvoice.totals.revenue - newInvoice.totals.balance
                )
              }}
            </div>
            <div>
              Due to Company:
              {{ $getFormattedPrice(newInvoice.totals.balance) }}
            </div>
          </b-td>
        </b-tr>
      </template>
    </b-table>

    <div class="d-flex flex-wrap align-items-center justify-content-end mt-3">
      <template v-if="!isEdit">
        <EditButton
          v-if="$isAllowed('app/invoices/update')"
          @click="editInvoiceItems"
        />
      </template>

      <template v-if="isEdit">
        <b-button variant="primary" class="ml-2" size="sm" @click="save">
          Save
        </b-button>

        <b-button
          variant="outline-light"
          class="ml-2"
          size="sm"
          @click="cancel"
        >
          Cancel
        </b-button>
      </template>
    </div>
  </div>
</template>

<script>
import _ from 'lodash'
import EditButton from '@/components/buttons/EditButton'
import PrintButton from '@/components/buttons/PrintButton'

export default {
  components: {
    EditButton,
    PrintButton,
  },
  props: {
    invoice: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      newInvoice: null,
      isEdit: false,
      invoiceItems: null,
      invoiceExtraItems: null,
      extraItem: null,
      columns: [
        {
          key: 'select',
          label: '',
          tdClass: 'pt-2 pb-0 pr-1',
          thStyle: { width: '1%' },
        },
        {
          key: 'number',
          label: '#',
          thStyle: { width: '1%' },
        },
        {
          key: 'title',
          label: 'Terminal',
        },
        {
          key: 'totalIn',
          label: 'Total In',
          thStyle: { width: '26%' },
        },
        {
          key: 'totalOut',
          label: 'Total Out',
          thStyle: { width: '26%' },
        },
        {
          key: 'revenue',
          label: 'Revenue',
        },
        {
          key: 'balance',
          label: 'Balance',
        },
        {
          key: 'notes',
          label: 'Notes',
        },
      ],
    }
  },
  computed: {
    selectedItems() {
      let result = 0

      this.invoiceItems.forEach((item) => {
        if (item.selected) {
          result++
        }
      })

      return result
    },
  },
  watch: {
    invoice(newValue) {
      this.newInvoice = _.cloneDeep(newValue)
      this.invoiceItems = newValue.invoiceItems
    },
  },
  created() {
    if (
      this.invoice.location.invoicingMode === 'custom' &&
      this.$isAllowed('app/invoices/update')
    ) {
      this.isEdit = true
    }

    this.newInvoice = _.cloneDeep(this.invoice)
    this.invoiceItems = this.invoice.invoiceItems
  },
  methods: {
    allItemsSelected() {
      return this.invoiceItems.filter((item) => !item.selected).length === 0
    },
    toggleAllItemsSelection(value) {
      this.invoiceItems = this.invoiceItems.map((item) => {
        return {
          ...item,
          selected:
            item.type === 'manual' &&
            (item.totalIn === null || item.totalOut === null)
              ? false
              : value,
        }
      })
    },
    onAddExtraInvoice() {
      this.extraItem = {
        type: 'extra',
        title: null,
        revenue: null,
      }
    },
    async saveExtraItem() {
      try {
        const invoice = _.cloneDeep(this.newInvoice)
        invoice.invoiceItems.push({ ...this.extraItem })

        const { data } = await this.$axios.post('/app/invoices/update', {
          id: invoice.id,
          invoice: {
            ...invoice,
            invoiceItems: invoice.invoiceItems.filter(
              (invoice) =>
                invoice.title ||
                invoice.balance ||
                invoice.totalIn ||
                invoice.totalOut
            ),
          },
        })

        this.newInvoice = _.cloneDeep(data.invoice)
        this.extraItem = null

        this.$emit('update', data.invoice)
      } catch (error) {
        console.error(error)
      }
    },
    cancelExtraItem() {
      this.extraItem = null
    },
    editInvoiceItems() {
      this.isEdit = true

      if (this.newInvoice.location.maxOfflineHours === 0) {
        this.newInvoice.invoiceItems.push({
          type: 'manual',
          number: null,
          title: null,
          totalIn: null,
          totalOut: null,
          revenue: null,
          notes: null,
        })
      }
    },
    async save() {
      try {
        const { data } = await this.$axios.post('/app/invoices/update', {
          id: this.newInvoice.id,
          invoice: {
            ...this.newInvoice,
            invoiceItems: this.invoiceItems.filter(
              (invoice) =>
                invoice.title ||
                invoice.balance ||
                invoice.totalIn ||
                invoice.totalOut
            ),
          },
        })

        this.newInvoice = _.cloneDeep(data.invoice)

        this.isEdit = false

        this.$emit('update', data.invoice)
      } catch (error) {
        console.error(error)
      }
    },
    cancel() {
      this.newInvoice = _.cloneDeep(this.invoice)

      this.isEdit = false
    },
    onChangeInvoiceItems() {
      const emptyInvoiceItem = this.invoiceItems.find(
        (item) =>
          item.type === 'manual' &&
          item.totalIn === null &&
          item.totalOut === null
      )

      if (!emptyInvoiceItem && this.newInvoice.location.maxOfflineHours === 0) {
        this.invoiceItems.push({
          type: 'manual',
          number: null,
          title: null,
          totalIn: null,
          totalOut: null,
          revenue: null,
          notes: null,
        })
      }
    },
    removeItems() {
      const newArray = this.invoiceItems.filter((item) => !item.selected)

      this.invoiceItems = newArray

      this.save()
    },
  },
}
</script>
