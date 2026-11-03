<script lang="ts">
    import AlbumPage from "#lib/components/AlbumPage.svelte";

    let { data } = $props();
</script>

<svelte:head>
    <title>{data.category.name} | {data.config?.gallery_title}</title>
</svelte:head>

<AlbumPage
    category={data.category}
    categories={data.categories}
    images={data.images}
/>
