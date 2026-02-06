import { csrf } from '@/api/csrf';
import api from '@/lib/api';
import type { Group } from '@/types';

export type CreateGroupPayload = {
	name: string;
	type: 'personal' | 'shared';
	users: number[];
};

export const groupsApi = {
	async addGroup(payload: CreateGroupPayload): Promise<Group> {
		await csrf();
		const response = await api.post('/groups/create', payload);

		return response.data?.group ?? null;
	},

	async updateGroup(
		payload: CreateGroupPayload,
		groupId: number
	): Promise<Group> {
		await csrf();
		const response = await api.post(`/groups/${groupId}/edit`, payload);

		return response.data?.group ?? null;
	},

	async getGroups(userId: number): Promise<Group[]> {
		const response = await api.get(`/users/${userId}/groups`);

		return response.data?.groups ?? response;
	},

	async getGroup(groupId: number): Promise<Group> {
		const response = await api.get(`/groups/${groupId}`);

		return response.data?.group ?? response;
	},
};
