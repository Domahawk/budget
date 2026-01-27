import type { Ref } from 'vue';
import { ref } from 'vue';
import { computed } from 'vue';
import type { ReceiptItemRow } from '@/types/receiptItemRow';

export type RowErrors = {
    qty?: string;
    unit_price?: string;
    total_price?: string;
    item?: string;
};
export function useReceiptValidation(rows: Ref<ReceiptItemRow[]>) {
    const errors = ref<RowErrors[]>([]);
    const disabledSave = computed(() => errors.value.length > 0 || rows.value.length < 1)

    function validate(): boolean {
        errors.value = []

        rows.value.forEach((row, index) => {
            const rowErrors: RowErrors = {};

            if (!row.qty || isNaN(Number(row.qty)) || Number(row.qty) <= 0) {
                rowErrors.qty = 'Quantity must be a positive number';
            }

            if (!row.unit_price || isNaN(Number(row.unit_price)) || Number(row.unit_price) <= 0) {
                rowErrors.unit_price = 'Unit price must be a number';
            }

            if (!row.total_price || isNaN(Number(row.total_price)) || Number(row.total_price) <= 0) {
                rowErrors.total_price = 'Total must be a number';
            }

            if (!row.item_id) {
                rowErrors.item = 'Select an item or create a new one';
            }

            if (Object.keys(rowErrors).length > 0) errors.value[index] = rowErrors
        });

        return errors.value.length > 0;
    }

    const inputClass = (index: number, itemProperty: string) => {
        let error = null

        if (itemProperty == 'qty') {
            error = errors.value[index]?.qty
        }

        if (itemProperty == 'unit') {
            error = errors.value[index]?.unit_price
        }

        if (itemProperty == 'total') {
            error = errors.value[index]?.total_price
        }

        if (itemProperty == 'search') {
            error = errors.value[index]?.item
        }

        return error ? "border-red-500" : ""
    }

    return {
        errors,
        disabledSave,

        validate,
        inputClass,
    }
}
