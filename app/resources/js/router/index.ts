import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/pages/auth/Login.vue';
import Register from '@/pages/auth/Register.vue';
import Home from '@/pages/Home.vue';
import Create from '@/pages/receipts/Create.vue';
import Index from '@/pages/receipts/Index.vue';
import Show from '@/pages/receipts/Show.vue';
import StoreCreate from '@/pages/stores/Create.vue';
import StoreIndex from '@/pages/stores/Index.vue';
import UsersShow from '@/pages/users/Show.vue';
import GroupEdit from '@/pages/groups/Edit.vue';
import { useAuthStore } from '@/stores/authStore';
import { useToastStore } from '@/stores/useToastStore';

const router = createRouter({
	history: createWebHistory(),
	routes: [
		{
			path: '/',
			component: Home,
			name: 'home',
			meta: { requiresAuth: true },
		},
		{
			path: '/receipts',
			component: Index,
			name: 'receipt_list',
			meta: { requiresAuth: true },
		},
		{
			path: '/receipts/:id',
			component: Show,
			name: 'receipt_show',
			meta: { requiresAuth: true },
		},
		{
			path: '/receipts/create',
			component: Create,
			name: 'receipt_create',
			meta: { requiresAuth: true },
		},
		{
			path: '/stores/create',
			component: StoreCreate,
			name: 'store_create',
			meta: { requiresAuth: true },
		},
		{
			path: '/stores',
			component: StoreIndex,
			name: 'store_list',
			meta: { requiresAuth: true },
		},
		{
			path: '/users/:id',
			component: UsersShow,
			name: 'user_show',
			meta: { requiresAuth: true },
		},
		{
			path: '/groups/create',
			component: GroupEdit,
			name: 'group_create',
			meta: { requiresAuth: true },
		},
		{
			path: '/groups/:id',
			component: GroupEdit,
			name: 'group_edit',
			meta: { requiresAuth: true },
		},
		{
			path: '/register',
			component: Register,
			name: 'register',
		},
		{
			path: '/login',
			component: Login,
			name: 'login',
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

router.beforeEach(async (to) => {
	const auth = useAuthStore();

	if (to.meta.requiresAuth && !auth.isAuthenticated) {
		await auth.fetchUser();

		if (!auth.isAuthenticated) {
			return { name: 'login' };
		}
	}
});
export default router;
