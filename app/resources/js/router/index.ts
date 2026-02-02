import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/pages/Home.vue';
import Create from '@/pages/receipts/Create.vue';
import Index from '@/pages/receipts/Index.vue';
import Show from '@/pages/receipts/Show.vue';
import StoreCreate from '@/pages/stores/Create.vue';
import StoreIndex from '@/pages/stores/Index.vue';
import { useToastStore } from '@/stores/useToastStore';

const router = createRouter({
	history: createWebHistory(),
	routes: [
		{
			path: '/',
			component: Home,
			name: 'home',
		},
		{
			path: '/receipts',
			component: Index,
			name: 'receipt_list',
		},
		{
			path: '/receipts/:id',
			component: Show,
			name: 'receipt_show',
		},
		{
			path: '/receipts/create',
			component: Create,
			name: 'receipt_create',
		},
		{
			path: '/stores/create',
			component: StoreCreate,
			name: 'store_create',
		},
		{
			path: '/stores',
			component: StoreIndex,
			name: 'store_list',
		},
	],
});

router.afterEach(() => {
	const state = history.state as any;

	if (state?.flash) {
		const toast = useToastStore();

		if (state.flash.type === 'success') {
			toast.success(state.flash.message);
		}

		if (state.flash.type === 'error') {
			toast.error(state.flash.message);
		}

		// IMPORTANT: clear flash so it doesn't reappear
		history.replaceState({ ...state, flash: null }, document.title);
	}
});
export default router;
