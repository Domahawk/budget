<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';

import ReceiptItemsModal from '@/components/ReceiptItemsModal.vue';
import ReceiptItemsTable from '@/components/ReceiptItemsTable.vue';
import api from '@/lib/api';

import type { Receipt } from '@/types/receipt';
import type { ReceiptItem } from '@/types/receiptItem';
import type { ReceiptItemRow } from '@/types/receiptItemRow';
import type { UiRow } from '@/types/uiRow';

const route = useRoute();
const receipt = ref<Receipt | null>(null);
const loading = ref(true);
const editedText = ref('');
const showParser = ref(false);
const rows = ref<ReceiptItemRow[]>([]);
const uiRows = ref<UiRow[]>([]);
const mode = ref<string>('edit');

async function fetchReceipt() {
	loading.value = true;

	try {
		const { data } = await api.get<Receipt>(`/receipts/${route.params.id}`);

		receipt.value = data;
		editedText.value = data.ocr_text_edited ?? data.ocr_text_raw ?? '';
		mode.value = receipt.value.items.length > 0 ? 'edit' : 'create';
		if (mode.value == 'edit') {
			parseExistingItems(receipt.value.items);
		}
	} finally {
		loading.value = false;
	}
}

const parseExistingItems = (items: ReceiptItem[]) => {
	rows.value = items.map((row: ReceiptItem, index: number) => ({
		raw_name: row.raw_name,
		qty: row.quantity,
		name: row.item.name,
		item_id: row.item_id,
		unit_price: row.unit_price,
		total_price: row.total_price,
		position: index + 1,
		id: row.id,
	}));

	uiRows.value = items.map((row: ReceiptItem) => ({
		search: row.raw_name,
		suggestions: [],
		creating: false,
		newItemName: row.raw_name,
		debounce: undefined,
	}));
};

async function parse(text: string) {
	try {
		const { data } = await api.post(`/receipts/parse`, { text });

		if (!Array.isArray(data)) return;

		rows.value = data.map((row: any, index: number) => ({
			raw_name: row.name,
			qty: row.qty,
			name: row.item_name ?? row.name,
			item_id: row.item_id ?? null,
			unit_price: row.unit_price,
			total_price: row.total_price,
			position: index + 1,
		}));

		uiRows.value = data.map((row: any) => ({
			search: row.name ?? '',
			suggestions: [],
			creating: false,
			newItemName: row.name ?? '',
			debounce: undefined,
		}));
	} catch (e) {
		console.log(e);
	}
}

const openModal = () => {
	if (mode.value == 'edit') {
		// pass receipt items to be parsed for modal
	} else {
		// parse text data for modal
		parse(editedText.value);
	}

	showParser.value = true;
};

function copyPrompt() {
	const text = `
Can you extract from this text only the product data in this format:
Name;Quantity;UnitPrice;TotalPrice
Put each item in its own row.
No need for headers, don't explain anything, just give me raw data text:
${receipt.value?.ocr_text_raw}`;

	navigator.clipboard
		.writeText(text)
		.then(() => alert('Copied to clipboard'))
		.catch(() => alert('Failed to copy'));
}

onMounted(fetchReceipt);
watch(() => route.params.id, fetchReceipt);

/* ------------------------------------------
   Modal callbacks
------------------------------------------ */
function onItemsSaved() {
	showParser.value = false;
	fetchReceipt();
}
</script>

<template>
	<div
		v-if="loading || !receipt"
		class="flex min-w-full items-center justify-center py-10 text-sm text-gray-500"
	>
		Loading receipt…
	</div>

	<div
		v-else
		class="flex min-w-full flex-col items-center justify-center py-10"
	>
		<div class="mb-6 max-w-fit rounded border p-4">
			<p class="font-medium">Status: {{ receipt.status }}</p>
		</div>
		<!-- Persisted items -->
		<div class="w-full p-10">
			<ReceiptItemsTable :items="receipt.items" />

			<button
				class="mt-2 self-start rounded bg-green-600 px-4 py-2 text-white"
				@click="openModal"
			>
				{{ mode == 'edit' ? 'Edit items' : 'Create items' }}
			</button>
		</div>
		<div class="flex min-w-[60%] flex-row-reverse justify-between">
			<div
				v-if="receipt.status === 'needs_review'"
				class="flex min-h-fit flex-col items-center space-y-2"
			>
				<label class="block font-semibold"> OCR text (editable) </label>
				<button
					@click="copyPrompt"
					class="space-y-2 rounded border bg-pink-600 p-4 px-3 py-1 text-sm text-white"
				>
					Copy AI ready text
				</button>
				<textarea
					v-model="editedText"
					rows="12"
					class="min-h-fit min-w-2xl rounded border p-2 text-sm"
				/>
			</div>
			<img
				:src="`/storage/${receipt.image_path}`"
				class="mb-6 max-w-100 rounded border"
			/>
		</div>

		<!-- Modal owns ALL parsing + saving logic -->
		<ReceiptItemsModal
			:open="showParser"
			:receipt="receipt"
			:parent-rows="rows"
			:parent-ui-rows="uiRows"
			@close="showParser = false"
			@saved="onItemsSaved"
		/>
	</div>
</template>
