import type { Item } from "./item";

export interface ReceiptItem {
    id: string;
    quantity: number;
    raw_name: string | null;
    unit_price: number;
    total_price: number;
    item: Item
}
