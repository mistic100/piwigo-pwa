<script lang="ts">
    import AlbumPage from '#lib/components/AlbumPage.svelte';

    const { data } = $props();
</script>

<h2>Home</h2>

<AlbumPage categories={data.rootCategories}/>
