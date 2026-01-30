import type { Component } from 'vue';

export interface Link {
	label: string;
	icon: Component;
	component: Component;
	links?: Link[];
	link_name?: string;
	me?: string;
}
