<script lang="ts">
    import PicturePage from '$lib/components/PicturePage.svelte';
    import type { PageProps } from "./$types";

    let { params, data }: PageProps = $props();
</script>

<PicturePage picture={data.picture}
             categoryId={params.categoryId}/>
