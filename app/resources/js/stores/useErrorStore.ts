import { defineStore } from 'pinia';
import type { Ref } from 'vue';
import { ref } from 'vue';

export const useErrorStore = defineStore('error', () => {
	const lastError: Ref<null | {
		status: number;
		message: string;
		errors?: Record<string, string[]>;
	}> = ref(null);
	const capture = (error: { status?: number; message: string }) => {
		lastError.value = {
			status: error.status ?? 0,
			message: error.message,
		};
	};

	const clear = () => {
		lastError.value = null;
	};

	return {
		lastError,
		capture,
		clear,
	};
});
