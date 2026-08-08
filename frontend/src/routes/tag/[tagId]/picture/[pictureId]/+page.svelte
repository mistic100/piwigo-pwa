<script lang="ts">
    import PicturePage from '$lib/components/PicturePage.svelte';
    import type { PageProps } from "./$types";

    let { params }: PageProps = $props();
</script>

<PicturePage pictureId={params.pictureId}
             tagId={params.tagId}/>
