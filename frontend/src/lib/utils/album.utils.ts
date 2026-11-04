import type { Category } from '#lib/api/model.js';

const MAX_SHORT_DESC_LENGTH = 200;
const EXT_DESC_PATTERNS = /<!--complete-->|<!--more-->/;

/**
 * Returns the short description of a category
 * If Extended description is used, it returns the part before the complete/more separator
 * Otherwise it truncates the description after 200 chars
 */
export function getShortDescription(category: Category): string {
    if (!category.comment) {
        return null;
    }

    const [start, end] = category.comment.split(EXT_DESC_PATTERNS, 2);

    if (end) {
        return start;
    } else if (start.length > MAX_SHORT_DESC_LENGTH) {
        return start.substring(0, start.lastIndexOf(' ', MAX_SHORT_DESC_LENGTH)) + '…';
    } else {
        return start;
    }
}

/**
 * Returns the long descriptionn of a category
 * If Extended description is used, it returns the part after the complete/more separator
 * Otherwise it returns the full description
 */
export function getLongDescription(category: Category): string {
    if (!category.comment) {
        return null;
    }

    const [start, end] = category.comment.split(EXT_DESC_PATTERNS, 2);

    if (end) {
        return end;
    } else {
        return start;
    }
}
