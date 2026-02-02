import { defineStore } from 'pinia';
import { toast } from 'vue-sonner';

export const useToastStore = defineStore('toast', () => {
	const error = (message: string) => {
		toast.error(message);
	};

	return {
		error,
	};
});
