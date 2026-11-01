<script lang="ts">
	import type { AppConfig } from "#lib";
	import * as NavigationMenu from "#lib/components/ui/navigation-menu/index.js";
	import { Separator } from '#lib/components/ui/separator/index.js';
	import * as Sheet from '#lib/components/ui/sheet/index.js';
	import MenuIcon from "@lucide/svelte/icons/menu";
	import { MediaQuery } from 'svelte/reactivity';

	const menuSections = [
		{ label: "Albums", items: ["Album 1", "Album 2"] },
		{ label: "Tags", items: ["tag a", "tag b"] },
		{ label: "Browse", items: ["Recent", "Random"] },
	];

	const { config }: { config: AppConfig } = $props();
	const isDesktop = new MediaQuery("(min-width: 768px)");
</script>

<header class="sticky top-0 flex h-[55px] w-full items-center justify-between gap-4 px-4 border-b bg-background">
	<h1 class="uppercase">{config.gallery_title}</h1>

{#if isDesktop.current}
	<NavigationMenu.Root>
		<NavigationMenu.List>
			{#each menuSections as section (section.label)}
				<NavigationMenu.Item openOnHover={false} class="static">
					<NavigationMenu.Trigger>{section.label}</NavigationMenu.Trigger>
					<NavigationMenu.Content>
						<ul class="min-w-xs">
							{#each section.items as item (item)}
								<li class="whitespace-nowrap">
									<NavigationMenu.Link>{item}</NavigationMenu.Link>
								</li>
							{/each}
						</ul>
					</NavigationMenu.Content>
				</NavigationMenu.Item>
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
				<Sheet.Title class="uppercase">{config.gallery_title}</Sheet.Title>
			</Sheet.Header>
			<nav aria-label="Main menu" class="grid gap-4 p-4 pt-0">
				{#each menuSections as section, i (section.label)}
					{#if i > 0}<Separator />{/if}
					<section>
						<h2 class="mb-2 text-muted-foreground uppercase">{section.label}</h2>
						<ul class="grid gap-2">
							{#each section.items as item (item)}
								<li>
									<span class="block pl-2">{item}</span>
								</li>
							{/each}
						</ul>
					</section>
				{/each}
			</nav>
		</Sheet.Content>
	</Sheet.Root>
{/if}
</header>
