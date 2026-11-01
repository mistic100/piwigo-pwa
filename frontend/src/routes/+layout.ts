import type { AppConfig, FrontendConfig } from '#lib';
import { PiwigoApi } from '#lib/api/index.js';
import { error } from '@sveltejs/kit';
import type { LayoutLoad } from './$types';
import type { PwaConfig } from '#lib/api/model.js';

export const ssr = false;

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

    return {
        config: {
            ...config,
            ...galleryConfig
        } as AppConfig,
        client,
    };
};
