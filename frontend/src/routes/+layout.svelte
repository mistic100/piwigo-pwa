<script lang="ts">
    import "../app.css";

	let { children, data } = $props();
</script>

<svelte:head>
	<title>{data.config?.gallery_title}</title>
</svelte:head>

<header>
	<h1>{data.config.gallery_title}</h1>
	<!-- {@html data.galleryConfig.page_banner} -->
</header>

{@render children()}

<footer>
	Footer
</footer>
