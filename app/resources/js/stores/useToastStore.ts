import { defineStore } from 'pinia';
import { toast } from 'vue-sonner';

export const useToastStore = defineStore('toast', () => {
	const error = (message: string) => {
		toast.error(message);
	};

	const warning = (message: string) => {
		toast.warning(message);
	};

	const success = (message: string) => {
		toast.success(message);
	};

	return {
		success,
		warning,
		error,
	};
});
