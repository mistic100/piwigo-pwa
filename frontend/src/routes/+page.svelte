<script lang="ts">
    import AlbumPage from '#lib/components/AlbumPage.svelte';
    import type { PageProps } from "./$types";

    const { data }: PageProps = $props();
</script>

<h2>Home</h2>

<AlbumPage categories={data.categories} config={data.config}/>
