import { getIdFromUrl } from '$lib/utils/url.utils';
import { error } from '@sveltejs/kit';
import type { LayoutLoad } from './$types';

export const load: LayoutLoad = async ({ parent, params }) => {
    const categoryId = getIdFromUrl(params.categoryId);
    if (!categoryId) {
        throw error(404, 'Album not found');
    }

    const { client } = await parent();

    const category = await client.getCategoryInfo({ cat_id: categoryId, public: true });

    const categories = client.getCategories({ cat_id: categoryId, public: true })
        .then(categories => categories.filter(({ id }) => id !== category.id));

    const images = client.getCategoryImages({ cat_id: categoryId });

    return {
        category,
        categories,
        images,
    };
};
