import api from '@/lib/api';

export async function csrf() {
	await api.get('/sanctum/csrf-cookie');
}
