<script lang="ts">
    import * as Breadcrumb from "#lib/components/ui/breadcrumb/index.js";
    import { getAlbumUrl, getTagUrl } from "#lib/utils/url.utils.js";
    import { page } from "$app/state";

    const breadcrumbs = $derived.by(() => {
        const { category, tag, picture, config } = page.data;

        const items = [{ label: "Home", href: "/" }];

        if (category) {
            category.parents?.map((parent) => {
                items.push({
                    label: parent.name,
                    href: getAlbumUrl(parent, config),
                });
            });

            items.push({
                label: category.name,
                href: getAlbumUrl(category, config),
            });
        } else if (tag) {
            items.push({
                label: tag.name,
                href: getTagUrl(tag, config),
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
