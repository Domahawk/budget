export interface ItemAlias {
	item_id: number;
	alias: string;
}
export interface Item {
	id: number;
	name: string;
	aliases: ItemAlias[];
}
