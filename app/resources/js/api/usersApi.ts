import { csrf } from '@/api/csrf';
import api from '@/lib/api';
import type { User } from '@/types';

export type RegisterPayload = {
	username: string;
	email: string;
	password: string;
	confirm: string;
};

export const usersApi = {
	async getUser(id: string) {
		const { data } = await api.get(`/users/${id}`);

		return data.data ?? data;
	},
};
