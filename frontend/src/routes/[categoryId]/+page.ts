import type { PageLoad } from './$types';

export const load: PageLoad = async ({ parent, params }) => {
    const { client, category } = await parent();

    const categories = client.getCategories({ cat_id: category.id, public: true })
        .then(categories => categories.filter(({ id }) => id !== category.id));

    const images = client.getCategoryImages({ cat_id: category.id });

    return {
        categories,
        images,
    };
};
