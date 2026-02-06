export interface Group {
	id: string;
	name: string;
	budgets: Budget[];
	user_role: string;
	type: 'personal' | 'shared';
	users: User[];
}

export interface Budget {
	id: string;
	group: Group;
	name: string;
	limit: number;
	starts_at: string;
	ends_at: string;
}

export type User = {
	id: number;
	username: string;
	email: string;
	email_verified_at: string | null;
	created_at: string;
	updated_at: string;
	[key: string]: unknown;
	groups: Group[];
};

export type Auth = {
	user: User;
};
