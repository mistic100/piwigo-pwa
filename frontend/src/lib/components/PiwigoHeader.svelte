<!--
Main header with title, responsive navigation menu
-->

<script lang="ts">
    import * as NavigationMenu from "#lib/components/ui/navigation-menu/index.js";
    import { Separator } from "#lib/components/ui/separator/index.js";
    import * as Sheet from "#lib/components/ui/sheet/index.js";
    import { page } from "$app/state";
    import MenuIcon from "@lucide/svelte/icons/menu";
    import { MediaQuery } from "svelte/reactivity";
    import { getAlbumUrl, getTagUrl } from "../utils/url.utils";

    const { rootCategories, allTags, config } = page.data;

    const menuSections: Array<{
        title: string;
        items: Array<{ name: string; link: string }>;
    }> = [
        {
            title: "Albums",
            items: rootCategories.map((cat) => ({
                name: cat.name,
                link: getAlbumUrl(cat, config),
            })),
        },
        {
            title: "Tags",
            items: allTags.map((tag) => ({
                name: tag.name,
                link: getTagUrl(tag, config),
            })),
        },
        {
            title: "Browse",
            items: [{ name: "Recent photos", link: "recent" }],
        },
    ];

    const isDesktop = new MediaQuery("(min-width: 768px)");

    const isRoot = $derived.by(() => {
        return page.data.isRoot;
    });
</script>

<header
    class="sticky top-0 flex h-[55px] w-full items-center justify-between gap-4 px-4 border-b bg-background"
>
    <h1 class="uppercase">{config.gallery_title}</h1>

    {#if isDesktop.current}
        <NavigationMenu.Root>
            <NavigationMenu.List class="gap-2">
                {#each menuSections as section (section.title)}
                    {#if section.items.length > 0}
                        <NavigationMenu.Item openOnHover={false} class="static">
                            <NavigationMenu.Trigger
                                >{section.title}</NavigationMenu.Trigger
                            >
                            <NavigationMenu.Content>
                                <ul class="min-w-xs">
                                    {#each section.items as item (item)}
                                        <li class="whitespace-nowrap">
                                            <NavigationMenu.Link
                                                href={item.link}
                                                >{item.name}</NavigationMenu.Link
                                            >
                                        </li>
                                    {/each}
                                </ul>
                            </NavigationMenu.Content>
                        </NavigationMenu.Item>
                    {/if}
                {/each}

                <NavigationMenu.Indicator />
            </NavigationMenu.List>
        </NavigationMenu.Root>
    {:else}
        <Sheet.Root>
            <Sheet.Trigger aria-label="Open menu">
                <MenuIcon aria-hidden="true" />
            </Sheet.Trigger>
            <Sheet.Content side="top">
                <Sheet.Header>
                    <Sheet.Title class="uppercase"
                        >{config.gallery_title}</Sheet.Title
                    >
                </Sheet.Header>
                <nav aria-label="Main menu" class="grid gap-4 p-4 pt-0">
                    {#each menuSections as section, i (section.title)}
                        {#if section.items.length > 0}
                            {#if i > 0}<Separator />{/if}
                            <section>
                                <h2
                                    class="mb-2 text-muted-foreground uppercase"
                                >
                                    {section.title}
                                </h2>
                                <ul class="grid gap-2">
                                    {#each section.items as item (item)}
                                        <li>
                                            <span class="block pl-2"
                                                >{item.name}</span
                                            >
                                        </li>
                                    {/each}
                                </ul>
                            </section>
                        {/if}
                    {/each}
                </nav>
            </Sheet.Content>
        </Sheet.Root>
    {/if}
</header>

{#if isRoot && config.displayBanner}
<div class="my-4 px-8 pb-4 border-b typeset typeset-banner">
    {@html config.page_banner}
</div>
{/if}
