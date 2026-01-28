export interface ReceiptItemRow {
	raw_name: string;
	qty: number | null;
	name: string | null;
	unit_price: number | null;
	total_price: number | null;
	item_id: number | null;
	position: number;
	id?: number;
}
