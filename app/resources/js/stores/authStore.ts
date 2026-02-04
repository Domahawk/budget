import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { authApi } from '@/api/authApi';
import router from '@/router';
import type { User } from '@/types';

export const useAuthStore = defineStore('auth', () => {
	const user = ref<User | null>(null);
	const initialized = ref(false);
	const isAuthenticated = computed(() => !!user.value);

	const fetchUser = async () => {
		try {
			user.value = await authApi.me();
		} catch {
			user.value = null;
		}
	};

	const logout = async () => {
		try {
			await authApi.logout();
		} finally {
			user.value = null;
			await router.push({ name: 'login' });
		}
	};

	const forceLogout = async () => {
		user.value = null;
		await router.push({ name: 'login' });
	};

	return {
		user,
		initialized,
		isAuthenticated,
		fetchUser,
		logout,
		forceLogout,
	};
});
