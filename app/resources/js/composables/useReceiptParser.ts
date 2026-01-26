import { router, usePage } from '@inertiajs/vue3'
import { ref, watchEffect } from 'vue'
import type { ReceiptItemRow } from '@/types/receiptItemRow'

export function useReceiptParser(receiptId: number) {
    const page = usePage()

    const rows = ref<ReceiptItemRow[]>([])
    const loading = ref(false)
    const showModal = ref(false)

    watchEffect(() => {
        const parsed = page.props.parsed
        if (!parsed || !Array.isArray(parsed)) return

        rows.value = parsed.map((row: any, index: number) => ({
            include: true,
            raw_name: row.raw_line,
            qty: row.qty,
            name: row.item_name ?? row.name,
            item_id: row.item_id ?? null,
            unit_price: row.unit_price,
            total_price: row.total_price,
            search: row.item_name ?? '',
            suggestions: [],
            creating: false,
            newItemName: '',
            position: index + 1,
        }))

        showModal.value = true
        loading.value = false
    })

    function parse(text: string) {
        loading.value = true

        router.post(
            `/receipts/${receiptId}/parse`,
            { text },
            {
                preserveScroll: true,
                preserveState: true,
                preserveUrl: true,
                only: ['parsed'],
            }
        )
    }

    function save(updatedRows: ReceiptItemRow[]) {
        router.post(`/receipts/${receiptId}/items`, {
            items: updatedRows
                .filter(r => r.include)
                .map(r => ({
                    raw_name: r.raw_name,
                    item_id: r.item_id,
                    quantity: r.qty,
                    unit_price: r.unit_price,
                    total_price: r.total_price,
                    position: r.position,
                })),
        }, {
            preserveState: 'errors'
        })
    }

    return {
        rows,
        loading,
        showModal,
        parse,
        save,
    }
}
