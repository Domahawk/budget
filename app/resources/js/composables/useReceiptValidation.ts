import type { Ref } from 'vue';
import { ref } from 'vue';
import { computed } from 'vue';
import type { ReceiptItemRow } from '@/types/receiptItemRow';
import type { RowError } from '@/types/rowError';

export function useReceiptValidation(rows: Ref<ReceiptItemRow[]>) {
	const errors = ref<RowError[]>([]);
	const disabledSave = computed(
		() => errors.value.length > 0 || rows.value.length < 1
	);

	function validate(): boolean {
		errors.value = [];

		rows.value.forEach((row, index) => {
			const rowErrors: RowError = {};

			if (!row.qty || isNaN(Number(row.qty)) || Number(row.qty) <= 0) {
				rowErrors.qty = 'Quantity must be a positive number';
			}

			if (
				!row.unit_price ||
				isNaN(Number(row.unit_price)) ||
				Number(row.unit_price) <= 0
			) {
				rowErrors.unit_price = 'Unit price must be a number';
			}

			if (
				!row.total_price ||
				isNaN(Number(row.total_price)) ||
				Number(row.total_price) <= 0
			) {
				rowErrors.total_price = 'Total must be a number';
			}

			if (!row.item_id) {
				rowErrors.item = 'Select an item or create a new one';
			}

			if (Object.keys(rowErrors).length > 0)
				errors.value[index] = rowErrors;
		});

		return errors.value.length > 0;
	}

	return {
		errors,
		disabledSave,

		validate,
	};
}
