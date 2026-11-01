<script lang="ts">
    import type { AppConfig } from "#lib";
    import type { Category, CategoryInfo, ImageWithCategories, Page } from "#lib/api/model.js";
    import { getAlbumUrl, getPictureUrl } from "#lib/utils/url.utils.js";

    const {
        category,
        categories,
        images,
        config,
    }: {
        category?: CategoryInfo;
        categories: Promise<Category[]>;
        images?: Promise<Page<ImageWithCategories>>;
        config: AppConfig;
    } = $props();
</script>

{#if category}
    <h2>Album {category.name}</h2>
    {#each category.parents as parent (parent.id)}
        {parent.name} /
    {/each}
{/if}

<div>
    {#await categories}
        Loading
    {:then categories}
        {#each categories as category (category.id)}
            <div>
                <a href={getAlbumUrl(category, config)}>{category.name}</a>
            </div>
        {/each}
    {/await}
</div>

{#if images}
<div>
    {#await images}
        Loading
    {:then images}
        {#each images.elements as image (image.id)}
            <div>
                <a href={getPictureUrl(image, { category }, config)}>{image.name}</a>
            </div>
        {/each}
    {/await}
</div>
{/if}
