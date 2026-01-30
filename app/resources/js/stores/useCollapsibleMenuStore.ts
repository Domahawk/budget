import { defineStore } from 'pinia';
import type { Ref } from 'vue';
import { watch } from 'vue';
import { ref } from 'vue';
import router from '@/router';
import type { Link } from '@/types/Link';

export const useCollapsibleMenuStore = defineStore('collapsibleMenu', () => {
	const activeId: Ref<string[]> = ref([]);

	const openCollapsible = (value: boolean, item: Link) => {
		if (value) {
			activeId.value.push(item.label);
		} else {
			activeId.value.splice(activeId.value.indexOf(item.label));
		}
	};

	const isCollapsible = (item: Link): boolean => {
		return activeId.value.indexOf(item.label) != -1;
	};

	watch(router.currentRoute, () => {
		activeId.value = [];
	});

	return {
		activeId,
		isCollapsible,
		openCollapsible,
	};
});
