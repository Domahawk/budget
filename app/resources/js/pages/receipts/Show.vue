<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';

import ReceiptItemsModal from '@/components/ReceiptItemsModal.vue';
import ReceiptItemsTable from '@/components/ReceiptItemsTable.vue';
import api from '@/lib/api';

import type { Receipt } from '@/types/receipt';

const route = useRoute();

const receipt = ref<Receipt | null>(null);
const loading = ref(true);

const editedText = ref('');
const showParser = ref(false);

/* ------------------------------------------
   Fetch receipt
------------------------------------------ */
async function fetchReceipt() {
    loading.value = true;

    try {
        const { data } = await api.get<Receipt>(`/receipts/${route.params.id}`);

        receipt.value = data;
        editedText.value = data.ocr_text_edited ?? data.ocr_text_raw ?? '';
    } finally {
        loading.value = false;
    }
}
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
        <ReceiptItemsTable :items="receipt.items" />
        <div class="min-w-[60%] flex flex-row-reverse justify-between">
            <div
                v-if="receipt.status === 'needs_review'"
                class="flex min-h-fit flex-col items-center space-y-2"
            >
                <label class="block font-semibold"> OCR text (editable) </label>
                <button
                    @click="copyPrompt"
                    class="space-y-2 rounded border p-4 bg-pink-600 px-3 py-1 text-sm text-white"
                >
                    Copy AI ready text
                </button>
                <textarea
                    v-model="editedText"
                    rows="12"
                    class="min-h-fit min-w-2xl rounded border p-2 text-sm"
                />

                <button
                    class="mt-2 rounded bg-green-600 px-4 py-2 text-white"
                    @click="showParser = true"
                >
                    Create items
                </button>
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
            :text="editedText"
            @close="showParser = false"
            @saved="onItemsSaved"
        />
    </div>
</template>
