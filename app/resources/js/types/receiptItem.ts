import type { Item } from './item';

export interface ReceiptItem {
	id: number;
	quantity: number;
	raw_name: string;
	item_id: number;
	unit_price: number;
	total_price: number;
	item: Item;
}
