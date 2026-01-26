export interface ReceiptItemRow {
    include: boolean
    raw_name: string
    qty: string | null
    name: string | null
    unit_price: string | null
    total_price: string | null
    item_id: number | null
    search: string
    suggestions: any[]
    creating: boolean
    newItemName: string
    position: number
}
