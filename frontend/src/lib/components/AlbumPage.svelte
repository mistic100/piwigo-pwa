<script lang="ts">
    import type {
        Category,
        CategoryInfo,
        ImageWithCategories,
        Page,
    } from "#lib/api/model.js";
    import { getLongDescription } from "#lib/utils/album.utils.js";
    import { getAlbumUrl, getPictureUrl } from "#lib/utils/url.utils.js";
    import { page } from "$app/state";

    const { config } = page.data;

    const {
        category,
        categories,
        images,
    }: {
        category?: CategoryInfo;
        categories: Category[] | Promise<Category[]>;
        images?: Promise<Page<ImageWithCategories>>;
    } = $props();

    const description = $derived.by(() => {
        return category ? getLongDescription(category) : null;
    });
</script>

{#if category && description}
    <div class="mb-4 px-8 pb-4 border-b typeset typeset-description">
        {@html description}
    </div>
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
                    <a href={getPictureUrl(image, { category }, config)}
                        >{image.name}</a
                    >
                </div>
            {/each}
        {/await}
    </div>
{/if}
