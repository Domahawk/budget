<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';

import ReceiptItemsModal from '@/components/ReceiptItemsModal.vue';
import ReceiptItemsTable from '@/components/ReceiptItemsTable.vue';
import api from '@/lib/api';

import type { Receipt } from '@/types/receipt';
import type { ReceiptItem } from '@/types/receiptItem';
import type { ReceiptItemRow } from '@/types/receiptItemRow';
import { useToastStore } from '@/stores/useToastStore';

const route = useRoute();
const receipt = ref<Receipt | null>(null);
const loading = ref(true);
const editedText = ref('');
const showParser = ref(false);
const rows = ref<ReceiptItemRow[]>([]);
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
	rows.value = items.map((row: ReceiptItem) => ({
		raw_name: row.raw_name,
		qty: row.quantity,
		name: row.item.name,
		item_id: row.item_id,
		unit_price: row.unit_price,
		total_price: row.total_price,
		id: row.id,
	}));
};
const toastStore = useToastStore();
async function parse(text: string) {
	if (!receipt.value) {
		return;
	}

	try {
		const { data } = await api.post(`/receipts/parse/${receipt.value.id}`, {
			text,
		});

		console.log(data);

		if (!Array.isArray(data)) {
			toastStore.warning('Data has been parsed, but there are no items');
			return;
		}

		toastStore.success('Data parsed successfully');

		rows.value = data.map((row: any) => ({
			raw_name: row.name,
			qty: row.qty,
			name: row.item_name ?? row.name,
			item_id: row.item_id ?? null,
			unit_price: row.unit_price,
			total_price: row.total_price,
		}));
	} catch (e) {
		console.log(e);
	}
}

const openModal = async () => {
	if (mode.value == 'edit') {
		// pass receipt items to be parsed for modal
	} else {
		// parse text data for modal
		await parse(editedText.value);
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

function onItemsSaved() {
	showParser.value = false;
	fetchReceipt();
}

onMounted(fetchReceipt);
watch(() => route.params.id, fetchReceipt);
</script>

<template>
	<div
		v-if="loading || !receipt"
		class="flex min-w-full items-center justify-center text-sm text-gray-500"
	>
		Loading receipt…
	</div>

	<div v-else class="flex flex-col items-center justify-center">
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
		<div class="w-full">
			<div class="flex flex-col p-10">
				<div class="grid grid-cols-3 items-center justify-items-center">
					<label class="block font-semibold">
						OCR text (editable)
					</label>
					<button
						@click="copyPrompt"
						class="mb-3 w-fit rounded border bg-pink-600 p-4 px-3 py-1 text-sm text-white"
					>
						Copy AI ready text
					</button>
					<label class="block font-semibold">
						OCR text (original)
					</label>
				</div>
				<div class="grid grid-cols-3 place-content-evenly">
					<textarea
						v-model="editedText"
						rows="12"
						class="row-span-1 mx-2 no-scrollbar min-h-fit rounded border p-2 text-sm"
					/>
					<div class="flex w-full items-center justify-center">
						<img
							:src="`/storage/${receipt.image_path}`"
							class="rounded border"
							alt=""
						/>
					</div>
					<textarea
						v-model="receipt.ocr_text_raw"
						:disabled="true"
						rows="12"
						class="row-span-1 mx-2 no-scrollbar min-h-fit rounded border p-2 text-sm"
					/>
				</div>
			</div>
		</div>
		<!-- Modal owns ALL parsing + saving logic -->
		<ReceiptItemsModal
			:open="showParser"
			:receipt="receipt"
			:parent-rows="rows"
			@close="showParser = false"
			@saved="onItemsSaved"
		/>
	</div>
</template>
