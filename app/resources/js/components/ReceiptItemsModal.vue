<script setup lang="ts">
import { ref, watch, watchEffect } from 'vue';
import ReceiptItemRowForm from '@/components/ReceiptItemRowForm.vue';
import api from '@/lib/api';
import type { Receipt } from '@/types/receipt';
import type { ReceiptItemRow } from '@/types/receiptItemRow';
import type { RowError } from '@/types/rowError';
import type { UiRow } from '@/types/uiRow';

const props = defineProps<{
	receipt: Receipt;
	open: boolean;
	parentRows: ReceiptItemRow[];
	parentUiRows: UiRow[];
}>();

const emit = defineEmits<{
	(e: 'close'): void;
	(e: 'saved'): void;
}>();

const resetRow = (): ReceiptItemRow => {
	return {
		raw_name: '',
		qty: 0,
		name: '',
		item_id: null,
		unit_price: null,
		total_price: null,
		position: 0,
		id: undefined,
	};
};

const resetUiRow = (): UiRow => {
	return {
		search: '',
		suggestions: [],
		creating: false,
		newItemName: '',
		debounce: undefined,
	};
};

const newRow = ref<ReceiptItemRow>(resetRow());
const newUiRow = ref<UiRow>(resetUiRow());
const rows = ref<ReceiptItemRow[]>([]);
const uiRows = ref<UiRow[]>([]);
const errors = ref<RowError[]>([]);
const singleRowError = ref<RowError>({});
const disabledSave = ref<boolean>(false);
watchEffect(() => {
	rows.value = props.parentRows;
	uiRows.value = props.parentUiRows;
});

const loading = ref(false);

const saveError = (index: number, error: RowError) => {
	errors.value[index] = error;
};

const saveSingleError = (error: RowError) => {
	singleRowError.value = error;
};

const addItemToItems = (item: ReceiptItemRow) => {
	rows.value.unshift(item);
	uiRows.value.unshift({
		creating: false,
		newItemName: '',
		search: item.name ?? '',
		suggestions: [],
	});

	newRow.value = resetRow();
	newUiRow.value = resetUiRow();
};

const removeItem = (item: ReceiptItemRow) => {
	console.log('removing', item);
	rows.value = rows.value.filter(
		(rowItem) => rowItem.item_id != item.item_id
	);
	console.log('after remove', rows.value);
};

async function onSubmit() {
	loading.value = true;

	try {
		await api.post(`/receipts/${props.receipt.id}/items`, {
			items: rows.value.map((r) => ({
				id: r.id,
				raw_name: r.raw_name,
				item_id: r.item_id,
				quantity: r.qty,
				unit_price: r.unit_price,
				total_price: r.total_price,
				position: r.position,
			})),
		});

		emit('saved');
		emit('close');
	} finally {
		loading.value = false;
	}
}

watch(
	() => errors.value,
	() => {
		disabledSave.value = errors.value.length > 0;
	},
	{
		deep: true,
	}
);
</script>

<template>
	<div
		v-if="open"
		class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
	>
		<div
			class="max-h-[90vh] w-full max-w-5xl overflow-auto rounded bg-black p-6"
		>
			<h2 class="mb-4 text-xl font-semibold">Receipt items</h2>

			<div class="mb-5 rounded-2xl border p-5">
				<h3>Add item</h3>
				<ReceiptItemRowForm
					:receipt="receipt"
					:parent-row="newRow"
					:parent-ui-row="newUiRow"
					mode="add"
					@error="(error: RowError) => saveSingleError(error)"
					@add="(aItem: ReceiptItemRow) => addItemToItems(aItem)"
				/>
			</div>

			<form @submit.prevent="onSubmit">
				<div
					v-for="(row, index) in rows"
					:key="index"
					class="mb-4 flex flex-col border-b pb-4"
				>
					<ReceiptItemRowForm
						:receipt="receipt"
						:parent-row="row"
						:parent-ui-row="uiRows[index]"
						mode="edit"
						@error="(error: RowError) => saveError(index, error)"
						@remove="(item: ReceiptItemRow) => removeItem(item)"
					/>
				</div>
				<div class="mt-4 flex justify-end gap-2">
					<button
						type="button"
						class="rounded border px-4 py-2"
						@click="emit('close')"
					>
						Cancel
					</button>

					<button
						type="submit"
						class="rounded px-4 py-2 text-black"
						:class="[disabledSave ? 'bg-gray-600' : 'bg-green-600']"
						:disabled="disabledSave"
					>
						Save receipt
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
