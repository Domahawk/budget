import { csrf } from '@/api/csrf';
import api from '@/lib/api';
import type { User } from '@/types';

export type RegisterPayload = {
	username: string;
	email: string;
	password: string;
	confirm: string;
};

export const authApi = {
	async register(payload: RegisterPayload): Promise<User> {
		await csrf();

		const { data } = await api.post('/register', payload);

		return data.data ?? data;
	},

	async login(payload: {
		username: string;
		password: string;
	}): Promise<User> {
		await csrf();
		const response = await api.post('/login', payload);

		return response.data?.user ?? null;
	},

	async logout() {
		await api.post('/logout');
	},

	async me(): Promise<User> {
		const response = await api.get('/me');
		return response?.data?.user ?? null;
	},
};
