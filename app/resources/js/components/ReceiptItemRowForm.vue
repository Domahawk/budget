<script setup lang="ts">
import { computed, ref, watch, watchEffect } from 'vue';
import { Input } from '@/components/ui/input';
import api from '@/lib/api';
import type { Receipt } from '@/types/receipt';
import type { ReceiptItemRow } from '@/types/receiptItemRow';
import type { RowError } from '@/types/rowError';
import type { UiRow } from '@/types/uiRow';
import { Item } from '@/types/item';

const props = defineProps<{
	receipt: Receipt;
	parentRow: ReceiptItemRow;
	parentUiRow: UiRow;
	mode: 'add' | 'edit';
}>();

const row = ref<ReceiptItemRow>(<ReceiptItemRow>{});
const uiRow = ref<UiRow>(<UiRow>{});
const validationError = ref<RowError>({});
watchEffect(() => {
	row.value = props.parentRow;
	uiRow.value = props.parentUiRow;
});

const emit = defineEmits<{
	(e: 'add', value: ReceiptItemRow): void;
	(e: 'error', value: RowError): void;
}>();

const sendError = (error: RowError) => {
	emit('error', error);
};

function validate(): void {
	validationError.value = {};
	if (
		!row.value.qty ||
		isNaN(Number(row.value.qty)) ||
		Number(row.value.qty) <= 0
	) {
		validationError.value.qty = 'Quantity must be a positive number';
	}

	if (
		!row.value.unit_price ||
		isNaN(Number(row.value.unit_price)) ||
		Number(row.value.unit_price) <= 0
	) {
		validationError.value.unit_price = 'Unit price must be a number';
	}

	if (
		!row.value.total_price ||
		isNaN(Number(row.value.total_price)) ||
		Number(row.value.total_price) <= 0
	) {
		validationError.value.total_price = 'Total must be a number';
	}

	if (!row.value.item_id) {
		validationError.value.item = 'Select an item or create a new one';
	}

	if (Object.keys(validationError.value).length > 0)
		sendError(validationError.value);
}

const itemFind = computed(() => {
	return row.value.item_id
		? `Found Item ID: ${row.value.item_id} Name: ${row.value.name}`
		: 'No item found, search for an Item or create a new Item';
});

function onSearch(value: string) {
	uiRow.value.search = value;

	if (uiRow.value.debounce) {
		clearTimeout(uiRow.value.debounce);
	}

	if (!value) {
		uiRow.value.suggestions = [];
		return;
	}

	uiRow.value.debounce = window.setTimeout(async () => {
		const res = await api.get(`/items?search=${encodeURIComponent(value)}`);
		uiRow.value.suggestions = res.data;
	}, 300);
}

function selectItem(item: Item) {
	row.value.name = item.name;
	row.value.item_id = item.id;
	row.value.raw_name = !row.value.raw_name
		? item.aliases[0].alias
		: row.value.raw_name;
	uiRow.value.search = item.name;
	uiRow.value.suggestions = [];
	uiRow.value.creating = false;
}

async function createItem() {
	if (!uiRow.value.newItemName) return;

	const { data: item } = await api.post('/items/create', {
		name: uiRow.value.newItemName,
		raw_name: row.value.raw_name,
	});

	row.value.name = item.name;
	row.value.item_id = item.id;
	uiRow.value.newItemName = '';
	uiRow.value.creating = false;
	uiRow.value.suggestions = [];
}

const addItem = () => {
	const locRow: ReceiptItemRow = {
		item_id: row.value.item_id,
		name: row.value.name,
		position: 0,
		qty: row.value.qty,
		raw_name: row.value.raw_name,
		total_price: row.value.total_price,
		unit_price: row.value.unit_price,
	};

	emit('add', locRow);
};

watch([row.value, uiRow.value], () => validate(), {
	deep: true,
	immediate: true,
});
</script>

<template>
	<h3 class="mb-2 text-sm text-gray-400">
		{{ itemFind }}
	</h3>

	<div class="grid grid-flow-row grid-cols-4 gap-2">
		<div>
			<Input
				v-model="row.qty"
				placeholder="Qty"
				:class="validationError.qty ? 'border-red-500' : ''"
			/>
			<p v-if="validationError?.qty" class="text-xs text-red-500">
				{{ validationError.qty }}
			</p>
		</div>

		<div class="relative">
			<Input
				:model-value="uiRow.search"
				@input="(e) => onSearch((e.target as HTMLInputElement).value)"
				placeholder="Search item…"
				:class="validationError?.item ? 'border-red-500' : ''"
			/>

			<div
				v-if="uiRow?.suggestions.length"
				class="absolute z-10 w-full rounded border bg-black"
			>
				<div
					v-for="item in uiRow.suggestions"
					:key="item.id"
					class="cursor-pointer px-2 py-1 hover:bg-gray-100"
					@click="selectItem(item)"
				>
					{{ item.name }}
				</div>
			</div>

			<p v-if="validationError?.item" class="text-xs text-red-500">
				{{ validationError.item }}
			</p>

			<div
				v-if="!row.item_id && mode == 'edit'"
				class="cursor-pointer px-2 py-1 text-sm text-blue-500"
				@click="uiRow.creating = true"
			>
				+ Add “{{ uiRow.search }}”
			</div>
		</div>
		<div>
			<Input
				v-model="row.unit_price"
				placeholder="Unit price"
				:class="validationError?.unit_price ? 'border-red-500' : ''"
			/>

			<p v-if="validationError?.unit_price" class="text-xs text-red-500">
				{{ validationError.unit_price }}
			</p>
		</div>

		<div>
			<Input
				v-model="row.total_price"
				placeholder="Total"
				:class="validationError?.total_price ? 'border-red-500' : ''"
			/>
			<p v-if="validationError?.total_price" class="text-xs text-red-500">
				{{ validationError.total_price }}
			</p>
		</div>
		<div class="col-span-4 flex items-center justify-end">
			<button
				v-if="mode == 'add'"
				type="button"
				class="rounded p-3 text-sm text-white"
				:class="
					Object.keys(validationError).length > 0
						? 'bg-gray-600'
						: 'bg-green-600'
				"
				@click="addItem"
				:disabled="Object.keys(validationError).length > 0"
			>
				Add Item
			</button>
		</div>
	</div>

	<div v-if="uiRow?.creating" class="mt-2 flex gap-2">
		<Input v-model="uiRow.newItemName" placeholder="New item name" />
		<button
			type="button"
			class="rounded bg-green-600 px-3 text-sm text-white"
			@click="createItem()"
		>
			Save
		</button>
	</div>
</template>
