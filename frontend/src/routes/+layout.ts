import type { FrontendConfig } from '#lib';
import { PiwigoApi } from '#lib/api/index.js';
import type { PwaConfig } from '#lib/api/model.js';
import { error } from '@sveltejs/kit';
import type { LayoutLoad } from './$types';

export const ssr = false;

/**
 * Initial app load
 * 1. local config.json file
 * 2. instance configuration via API
 * 3. global data via API
 *      a. root categories
 *      b. tags
 */
export const load: LayoutLoad = async ({ fetch }) => {
    let config: FrontendConfig;
    try {
        const res = await fetch('/config.json');
        config = await res.json();
    } catch (e) {
        console.error(e instanceof Error ? e.message : 'Unknown error');
        error(500, `The "config.json" file is either missing or malformed.`);
    }

    if (!config.backendUrl) {
        error(500, `Property "backendUrl" is missing in "config.json" file.`);
    }

    const client = new PiwigoApi(
        config.backendUrl,
        fetch,
        error
    );

    let galleryConfig: PwaConfig;
    try {
        galleryConfig = await client.getConfig();
    } catch (e) {
        console.error(e instanceof Error ? e.message : 'Unknown error');
        error(404, `Unable to contact Piwigo backend.`);
    }

    const [rootCategories, allTags] = await Promise.all([
        client.getCategories({ public: true }),
        client.getTags(),
    ]);

    return {
        config: {
            ...config,
            ...galleryConfig
        },
        client,
        rootCategories,
        allTags,
    } satisfies App.PageData;
};
