import type { PageLoad } from './$types';

export const load: PageLoad = async ({ parent, params }) => {
    const { client, tag } = await parent();

    const images = client.getTagImages({ tag_id: tag.id });

    return {
        images,
    };
};
