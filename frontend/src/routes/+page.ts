import { error } from '@sveltejs/kit';
import type { PageLoad } from './$types';

export const load: PageLoad = async ({ parent }) => {
    const { client } = await parent();

    return {
        categories: client.getCategories({ public: true }),
    };
};
