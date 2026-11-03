import type { AppConfig } from '#lib';
import type { Category, Image, Tag } from '#lib/api/model.js';
import slug from 'slug';

export function getIdFromUrl(str: string): number {
    const matches = /^([0-9]+)(\-.+)?$/.exec(str);
    return matches ? parseInt(matches[1], 10) : null;
}

export function getAlbumUrl(
    category: { id: number, name: string },
    config: AppConfig
) {
    if (config.category_url_style === 'id-name') {
        return `/${category.id}-${slug(category.name)}`;
    } else {
        return `/${category.id}`;
    }
}

export function getTagUrl(
    tag: Tag,
    config: AppConfig
) {
    if (config.tag_url_style === 'id-tag') {
        return `/tag/${tag.id}-${slug(tag.name)}`;
    } else {
        return `/tag/${tag.id}`;
    }
}

export function getPictureUrl(
    image: Image,
    { category, tag }: { category?: Category, tag?: Tag },
    config: AppConfig
): string {
    let pict: string;
    if (config.picture_url_style === 'id-file') {
        pict = `${image.id}-${slug(image.file.replace(/\.[^.]$/, ''))}`;
    } else {
        pict = `${image.id}`;
    }
    if (category) {
        return `${getAlbumUrl(category, config)}/${pict}`;
    }
    if (tag) {
        return `${getTagUrl(tag, config)}/${pict}`;
    }
    return `/picture/${pict}`;
}
