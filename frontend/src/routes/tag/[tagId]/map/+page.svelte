<script lang="ts">
    import MapPage from '#lib/components/MapPage.svelte';
    import type { PageProps } from "./$types";

    let { params }: PageProps = $props();
</script>

<MapPage tagId={params.tagId}/>
