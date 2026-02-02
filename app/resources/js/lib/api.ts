import axios from 'axios';
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

		errorStore.capture(error);

		return Promise.reject(error);
	}
);

export default api;
