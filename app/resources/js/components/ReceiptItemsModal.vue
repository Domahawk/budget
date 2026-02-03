<script setup lang="ts">
import { ref, watch, watchEffect } from 'vue';
import ReceiptItemRowForm from '@/components/ReceiptItemRowForm.vue';
import api from '@/lib/api';
import type { Receipt } from '@/types/receipt';
import type { ReceiptItemRow } from '@/types/receiptItemRow';
import type { RowError } from '@/types/rowError';

const props = defineProps<{
	receipt: Receipt;
	open: boolean;
	parentRows: ReceiptItemRow[];
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
		id: undefined,
	};
};

const newRow = ref<ReceiptItemRow>(resetRow());
const rows = ref<ReceiptItemRow[]>([]);
const errors = ref<
	{
		error: RowError;
		formIndex: number;
	}[]
>([]);
const singleRowError = ref<RowError>({});
const disabledSave = ref<boolean>(false);
watchEffect(() => {
	rows.value = props.parentRows;
});

const loading = ref(false);

const saveError = (index: number, error: RowError | undefined) => {
	if (!error) {
		errors.value = errors.value.filter(
			(errorItem) => errorItem.formIndex != index
		);

		return;
	}

	errors.value.push({
		error: error,
		formIndex: index,
	});
};

const saveSingleError = (error: RowError | undefined) => {
	if (!error) {
		return;
	}

	singleRowError.value = error;
};

const addItemToItems = (item: ReceiptItemRow) => {
	rows.value.unshift(item);

	newRow.value = resetRow();
};

const removeItem = (item: ReceiptItemRow) => {
	const itemId = item.id ?? item.raw_name;
	rows.value = rows.value.filter(
		(rowItem) => rowItem.id != itemId && rowItem.raw_name != itemId
	);
	// removing items causes indexes of items to change
	// need to reindex errors via revalidate-toggle
	// revalidation adds errors and new correct indexes
	errors.value = [];
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
					mode="add"
					@error="
						(error: RowError | undefined) => saveSingleError(error)
					"
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
						:revalidate-toggle="rows.length"
						mode="edit"
						@error="
							(error: RowError | undefined) =>
								saveError(index, error)
						"
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
