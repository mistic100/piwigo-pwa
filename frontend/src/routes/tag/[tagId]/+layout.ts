import { getIdFromUrl } from '#lib/utils/url.utils.js';
import { error } from '@sveltejs/kit';
import type { LayoutLoad } from './$types';

export const load: LayoutLoad = async ({ parent, params }) => {
    const tagId = getIdFromUrl(params.tagId);
    if (!tagId) {
        throw error(404, 'Tag not found');
    }

    const { allTags } = await parent();

    const tag = allTags.find(t => t.id === tagId);

    if (!tag) {
        throw error(404, 'Tag not found');
    }

    return {
        tag,
    };
};
