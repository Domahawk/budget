<script setup lang="ts">
import { ref } from 'vue'
import ReceiptItemsModal from '@/components/ReceiptItemsModal.vue'
import ReceiptItemsTable from '@/components/ReceiptItemsTable.vue';
import { useReceiptParser } from '@/composables/useReceiptParser'
import type { ReceiptItem } from '@/types/receiptItem';

const props = defineProps<{
    receipt: {
        id: number
        image_path: string
        status: string
        ocr_text_raw?: string | null
        ocr_text_edited?: string | null
        items: ReceiptItem[] | []
    }
}>()

const editedText = ref(
    props.receipt.ocr_text_edited ?? props.receipt.ocr_text_raw ?? ''
)

const {
    rows,
    loading,
    showModal,
    parse,
    save,
} = useReceiptParser(props.receipt.id)
</script>

<template>
    <div class="min-w-full py-10 flex flex-col justify-center items-center">
        <div class="mb-6 rounded border p-4 max-w-fit">
            <p class="font-medium">Status: {{ receipt.status }}</p>
        </div>

        <ReceiptItemsTable :items="receipt.items" />

        <div v-if="receipt.status === 'needs_review'" class="space-y-2 flex flex-col items-center min-h-fit">
            <label class="block font-semibold">OCR text (editable)</label>

            <textarea
                v-model="editedText"
                rows="12"
                class="min-w-2xl min-h-fit rounded border p-2 text-sm"
            />

            <button
                class="mt-2 rounded bg-black px-4 py-2 text-white"
                :disabled="loading"
                @click="parse(editedText)"
            >
                {{ loading ? 'Parsing…' : 'Create items' }}
            </button>
        </div>

        <img
            :src="`/storage/${receipt.image_path}`"
            class="mb-6 max-w-fit rounded border"
        />
        <ReceiptItemsModal
            :open="showModal"
            :rows="rows"
            @close="showModal = false"
            @save="save"
        />
    </div>
</template>
