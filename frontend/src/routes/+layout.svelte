<script lang="ts">
    import PiwigoHeader from "#lib/components/PiwigoHeader.svelte";
    import PiwigoBreadcrumb from '#lib/components/PiwigoBreadcrumb.svelte';
    import { onMount } from "svelte";
    import "../app.css";

    let { children, data } = $props();

    onMount(() => {
        document.getElementById("app-loader")?.remove();
    });
</script>

<svelte:head>
    <title>{data.config?.gallery_title}</title>
</svelte:head>

<PiwigoHeader/>

<!-- {@html data.galleryConfig.page_banner} -->

<PiwigoBreadcrumb/>

{@render children()}

<footer>Footer</footer>
