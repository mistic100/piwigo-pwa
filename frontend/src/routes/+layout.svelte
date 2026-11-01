<script lang="ts">
    import Header from '#lib/components/Header.svelte';
    import * as Breadcrumb from "#lib/components/ui/breadcrumb/index.js";
	import "../app.css";

	let { children, data } = $props();
</script>

<svelte:head>
	<title>{data.config?.gallery_title}</title>
</svelte:head>

<Header config={data.config}/>

	<!-- {@html data.galleryConfig.page_banner} -->

<Breadcrumb.Root>
  <Breadcrumb.List>
    <Breadcrumb.Item>
      <Breadcrumb.Link href="/">Home</Breadcrumb.Link>
    </Breadcrumb.Item>
    <Breadcrumb.Separator />
    <Breadcrumb.Item>
      <Breadcrumb.Link href="/components">Components</Breadcrumb.Link>
    </Breadcrumb.Item>
    <Breadcrumb.Separator />
    <Breadcrumb.Item>
      <Breadcrumb.Page>Breadcrumb</Breadcrumb.Page>
    </Breadcrumb.Item>
  </Breadcrumb.List>
</Breadcrumb.Root>

{@render children()}

<footer>
	Footer
</footer>
