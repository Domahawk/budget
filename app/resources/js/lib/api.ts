import axios from 'axios';
import { useAuthStore } from '@/stores/authStore';
import { useErrorStore } from '@/stores/useErrorStore';

const api = axios.create({
	baseURL: '/api',
	withCredentials: true,
	headers: {
		'X-Requested-With': 'XMLHttpRequest',
		Accept: 'application/json',
	},
});

// Global response interceptor
api.interceptors.response.use(
	(response) => response,
	(error) => {
		const errorStore = useErrorStore();

		if (error.response?.status === 401) {
			errorStore.capture({
				message: 'You are not logged in!',
				status: 401,
			});
			useAuthStore().forceLogout();

			return Promise.reject(error);
		}

		errorStore.capture(error);

		return Promise.reject(error);
	}
);

export default api;
