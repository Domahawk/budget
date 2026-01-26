export interface Item {
    id: string
    name: string
}
export interface ReceiptItem {
    id: string;
    quantity: number;
    raw_name: string | null;
    unit_price: number;
    total_price: number;
    item: Item
}
