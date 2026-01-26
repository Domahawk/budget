<script setup lang="ts">
import { reactive, watch } from 'vue'
import { Form } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import type { ReceiptItemRow } from '@/types/receiptItemRow'
import { router } from '@inertiajs/vue3'

/* ------------------------------------------
   Props / Emits
------------------------------------------ */
const props = defineProps<{
    rows: ReceiptItemRow[]
    open: boolean
}>()

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'save', rows: ReceiptItemRow[]): void
}>()

/* ------------------------------------------
   Editable rows (local copy)
------------------------------------------ */
const formRows = reactive<ReceiptItemRow[]>([])

watch(
    () => props.rows,
    rows => {
        formRows.splice(0, formRows.length, ...rows.map(r => ({ ...r })))
    },
    { immediate: true }
)

/* ------------------------------------------
   UI-only state (search + create)
------------------------------------------ */
type UiRow = {
    search: string
    suggestions: any[]
    creating: boolean
    newItemName: string
    debounce?: number
}

const uiRows = reactive<UiRow[]>([])

watch(
    () => props.rows,
    rows => {
        uiRows.splice(
            0,
            uiRows.length,
            ...rows.map(row => ({
                search: row.name ?? '',
                suggestions: [],
                creating: false,
                newItemName: row.raw_name ?? '',
                debounce: undefined,
            }))
        )
    },
    { immediate: true }
)

/* ------------------------------------------
   Search existing items
------------------------------------------ */
function onSearch(uiRow: UiRow, value: string) {
    uiRow.search = value

    if (uiRow.debounce) {
        clearTimeout(uiRow.debounce)
    }

    if (!value) {
        uiRow.suggestions = []
        return
    }

    uiRow.debounce = window.setTimeout(async () => {
        const res = await fetch(`/items?search=${encodeURIComponent(value)}`, {
            headers: { Accept: 'application/json' },
        })

        uiRow.suggestions = await res.json()
    }, 300)
}

function selectItem(index: number, uiRow: UiRow, item: any) {
    formRows[index].name = item.name
    formRows[index].item_id = item.id

    uiRow.search = item.name
    uiRow.suggestions = []
    uiRow.creating = false
}

/* ------------------------------------------
   Create new item
------------------------------------------ */
async function createItem(index: number, uiRow: UiRow) {
    if (!uiRow.newItemName) return

    const token = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content

    const res = await fetch('/items', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...(token ? { 'X-CSRF-TOKEN': token } : {}),
        },
        body: JSON.stringify({
            name: uiRow.newItemName,
            raw_name: formRows[index].raw_name,
        }),
    })

    if (!res.ok) {
        // optional: show errors
        return
    }

    const item = await res.json()

    formRows[index].name = item.name
    formRows[index].item_id = item.id

    uiRow.search = item.name
    uiRow.newItemName = ''
    uiRow.creating = false
    uiRow.suggestions = []
}

/* ------------------------------------------
   Submit
------------------------------------------ */
function onSubmit() {
    emit('save', formRows)
}
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="max-h-[90vh] w-full max-w-5xl overflow-auto rounded bg-black p-6">
            <h2 class="mb-4 text-xl font-semibold">Receipt items</h2>

            <Form @submit.prevent="onSubmit">
                <div
                    v-for="(row, index) in formRows"
                    :key="index"
                    class="mb-4 flex flex-col border-b pb-4"
                >
                    <!-- raw OCR line -->
                    <h3 class="mb-2 text-sm text-gray-400">
                        {{ row.name }}
                    </h3>

                    <div class="grid grid-cols-4 gap-2">
                        <!-- qty -->
                        <Input
                            v-model="row.qty"
                            type="text"
                            placeholder="Qty"
                        />

                        <!-- search / create -->
                        <div class="relative">
                            <Input
                                :model-value="uiRows[index]?.search"
                                @input="e => onSearch(uiRows[index], (e.target as HTMLInputElement).value)"
                                placeholder="Search item…"
                            />

                            <!-- suggestions -->
                            <div
                                v-if="uiRows[index]?.suggestions.length"
                                class="absolute z-10 w-full rounded border bg-black"
                            >
                                <div
                                    v-for="item in uiRows[index].suggestions"
                                    :key="item.id"
                                    class="cursor-pointer px-2 py-1 hover:bg-gray-100"
                                    @click="selectItem(index, uiRows[index], item)"
                                >
                                    {{ item.name }}
                                </div>
                            </div>
                            <div
                                class="cursor-pointer px-2 py-1 text-sm text-blue-500 hover:bg-gray-100"
                                @click="uiRows[index].creating = true"
                                v-if="!row.item_id"
                            >
                                + Add “{{ uiRows[index].search }}”
                            </div>
                        </div>

                        <!-- unit price -->
                        <Input
                            v-model="row.unit_price"
                            type="text"
                            placeholder="Unit price"
                        />

                        <!-- total -->
                        <Input
                            v-model="row.total_price"
                            type="text"
                            placeholder="Total"
                        />
                    </div>

                    <!-- create new item -->
                    <div
                        v-if="uiRows[index].creating"
                        class="mt-2 flex gap-2"
                    >
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
                        class="rounded bg-white px-4 py-2 text-black"
                    >
                        Save receipt
                    </button>
                </div>
            </Form>
        </div>
    </div>
</template>
