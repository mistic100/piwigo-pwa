<script lang="ts">
    import { getPictureUrl } from '#lib/utils/url.utils.js';
    import type { PageProps } from "./$types";

    let { data }: PageProps = $props();
</script>

<h2>Tag {data.tag.id}</h2>

{#await data.images}
    Loading
{:then images}
    {#each images.elements as image (image.id)}
        <div>
            <a href={getPictureUrl(image, { tag: data.tag }, data.config)}
                >{image.name}</a
            >
        </div>
    {/each}
{/await}
