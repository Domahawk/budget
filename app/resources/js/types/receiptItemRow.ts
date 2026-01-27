export interface ReceiptItemRow {
    raw_name: string
    qty: string | null
    name: string | null
    unit_price: string | null
    total_price: string | null
    item_id: number | null
    position: number
}
