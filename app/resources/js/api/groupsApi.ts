import { csrf } from '@/api/csrf';
import api from '@/lib/api';
import type { User } from '@/types';

export type CreateGroupPayload = {
	name: string;
	type: 'personal' | 'shared';
	users?: number[];
};

export const groupsApi = {
	async addGroup(payload: CreateGroupPayload): Promise<User> {
		await csrf();
		const response = await api.post('/groups/create', payload);

		return response.data?.group ?? null;
	},
};
