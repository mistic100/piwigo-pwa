<script lang="ts">
    import Header from "#lib/components/Header.svelte";
    import * as Breadcrumb from "#lib/components/ui/breadcrumb/index.js";
    import { page } from "$app/state";
    import { onMount } from "svelte";
    import "../app.css";
    import type { CategoryInfo, ImageInfo } from "../lib/api/model.js";
    import { getAlbumUrl } from "../lib/utils/url.utils.js";

    let { children, data } = $props();

    onMount(() => {
        document.getElementById("app-loader")?.remove();
    });

    const breadcrumbs = $derived.by(() => {
        const { category, picture } = page.data as {
            category: CategoryInfo;
            picture: ImageInfo;
        };
        const items = [{ label: "Home", href: "/" }];

        if (category) {
            category.parents?.map((parent) => {
                items.push({
                    label: parent.name,
                    href: getAlbumUrl(parent, data.config),
                });
            });

            items.push({
                label: category.name,
                href: getAlbumUrl(category, data.config),
            });
        }

        if (picture) {
            items.push({
                label: picture.name,
                href: page.url.pathname,
            });
        }

        return items;
    });
</script>

<svelte:head>
    <title>{data.config?.gallery_title}</title>
</svelte:head>

<Header
    config={data.config}
    categories={data.rootCategories}
    tags={data.tags}
/>

<!-- {@html data.galleryConfig.page_banner} -->

<Breadcrumb.Root>
    <Breadcrumb.List>
        {#each breadcrumbs as crumb, index}
            {#if index > 0}
                <Breadcrumb.Separator />
            {/if}

            <Breadcrumb.Item>
                {#if index === breadcrumbs.length - 1}
                    <Breadcrumb.Page>{crumb.label}</Breadcrumb.Page>
                {:else}
                    <Breadcrumb.Link href={crumb.href}>
                        {crumb.label}
                    </Breadcrumb.Link>
                {/if}
            </Breadcrumb.Item>
        {/each}
    </Breadcrumb.List>
</Breadcrumb.Root>

{@render children()}

<footer>Footer</footer>
