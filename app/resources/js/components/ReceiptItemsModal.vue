<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { useReceiptValidation } from '@/composables/useReceiptValidation';
import api from '@/lib/api';
import type { Receipt } from '@/types/receipt';
import type { ReceiptItemRow } from '@/types/receiptItemRow';
import type { UiRow } from '@/types/uiRow';

const props = defineProps<{
    receipt: Receipt;
    open: boolean;
    text: string
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'saved'): void;
}>();

const rows = ref<ReceiptItemRow[]>([]);
const uiRows = ref<UiRow[]>([]);
const validation = useReceiptValidation(rows)
const loading = ref(false);

const itemFind = (itemId: number | null | undefined, itemName: string | null) => {
    return itemId ? `Found Item ID: ${itemId} Name: ${itemName}` : "No item found, search for an Item or create a new Item";
}

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
        console.log(e)
    }
}

function onSearch(uiRow: UiRow, value: string) {
    uiRow.search = value;

    if (uiRow.debounce) {
        clearTimeout(uiRow.debounce);
    }

    if (!value) {
        uiRow.suggestions = [];
        return;
    }

    uiRow.debounce = window.setTimeout(async () => {
        const res = await api.get(`/items?search=${encodeURIComponent(value)}`);
        uiRow.suggestions = res.data;
    }, 300);
}

function selectItem(index: number, uiRow: UiRow, item: any) {
    rows.value[index].name = item.name;
    rows.value[index].item_id = item.id;
    uiRow.search = item.name;
    uiRow.suggestions = [];
    uiRow.creating = false;
}

async function createItem(index: number, uiRow: UiRow) {
    if (!uiRow.newItemName) return;

    const { data: item } = await api.post('/items/create', {
        name: uiRow.newItemName,
        raw_name: rows.value[index].raw_name,
    });

    rows.value[index].name = item.name;
    rows.value[index].item_id = item.id;
    uiRow.newItemName = '';
    uiRow.creating = false;
    uiRow.suggestions = [];
}

async function onSubmit() {
    if (validation.validate()) return;

    loading.value = true;

    try {
        await api.post(`/receipts/${props.receipt.id}/items`, {
            items: rows.value.map((r) => ({
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
    () => props.open,
    async (open) => {
        if (!open) {
            rows.value = [];
            uiRows.value = [];
            validation.errors.value = []
        }

        await parse(props.text);
    },
);

watch(
    () => rows.value,
    () => {
        if (!props.open) return
        validation.validate()
    },
    {
        deep: true
    }
)
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

            <form @submit.prevent="onSubmit">
                <div
                    v-for="(row, index) in rows"
                    :key="index"
                    class="mb-4 flex flex-col border-b pb-4"
                >
                    <h3 class="mb-2 text-sm text-gray-400">
                        {{ itemFind(row.item_id, row.name) }}
                    </h3>

                    <div class="grid grid-cols-4 gap-2">
                        <div>
                            <Input
                                v-model="row.qty"
                                placeholder="Qty"
                                :class="validation.inputClass(index, 'qty')"
                            />
                            <p
                                v-if="validation.errors.value[index]?.qty"
                                class="text-xs text-red-500"
                            >
                                {{ validation.errors.value[index].qty }}
                            </p>
                        </div>

                        <div class="relative">
                            <Input
                                :model-value="uiRows[index]?.search"
                                @input="
                                    (e) =>
                                        onSearch(
                                            uiRows[index],
                                            (e.target as HTMLInputElement)
                                                .value,
                                        )
                                "
                                placeholder="Search item…"
                                :class="validation.inputClass(index, 'search')"
                            />

                            <div
                                v-if="uiRows[index]?.suggestions.length"
                                class="absolute z-10 w-full rounded border bg-black"
                            >
                                <div
                                    v-for="item in uiRows[index].suggestions"
                                    :key="item.id"
                                    class="cursor-pointer px-2 py-1 hover:bg-gray-100"
                                    @click="
                                        selectItem(index, uiRows[index], item)
                                    "
                                >
                                    {{ item.name }}
                                </div>
                            </div>

                            <p
                                v-if="validation.errors.value[index]?.item"
                                class="text-xs text-red-500"
                            >
                                {{ validation.errors.value[index].item }}
                            </p>

                            <div
                                v-if="!row.item_id"
                                class="cursor-pointer px-2 py-1 text-sm text-blue-500"
                                @click="uiRows[index].creating = true"
                            >
                                + Add “{{ uiRows[index].search }}”

                            </div>
                        </div>
                        <div>
                            <Input
                                v-model="row.unit_price"
                                placeholder="Unit price"
                                :class="validation.inputClass(index, 'unit')"
                            />
                            <p
                                v-if="validation.errors.value[index]?.unit_price"
                                class="text-xs text-red-500"
                            >
                                {{ validation.errors.value[index].unit_price }}
                            </p>
                        </div>

                        <div>
                            <Input
                                v-model="row.total_price"
                                placeholder="Total"
                                :class="validation.inputClass(index, 'total')"
                            />
                            <p
                                v-if="validation.errors.value[index]?.total_price"
                                class="text-xs text-red-500"
                            >
                                {{ validation.errors.value[index].total_price }}
                            </p>
                        </div>
                    </div>

                    <div v-if="uiRows[index]?.creating" class="mt-2 flex gap-2">
                        <Input
                            v-model="uiRows[index].newItemName"
                            placeholder="New item name"
                        />
                        <button
                            type="button"
                            class="rounded bg-green-600 px-3 text-sm text-white"
                            @click="createItem(index, uiRows[index])"
                        >
                            Save
                        </button>
                    </div>
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
                        :class="[validation.disabledSave.value ? 'bg-gray-600' : 'bg-green-600']"
                        :disabled="validation.disabledSave.value"
                    >
                        Save receipt
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
