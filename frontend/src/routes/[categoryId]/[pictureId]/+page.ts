import type { Image } from '#lib/api/model.js';
import type { PageLoad } from './$types';

export const load: PageLoad = ({ params }) => {
    return {
        picture: {
            id: +params.pictureId,
            name: 'Picture ' + params.pictureId,
        } satisfies Partial<Image>,
    }
};
