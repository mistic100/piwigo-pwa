<script lang="ts">
    import type { PageProps } from "./$types";

    let { params }: PageProps = $props();
</script>

<h2>Tag {params.tagId}</h2>

<a href="/tag/{params.tagId}/map">Map</a>
<a href="/tag/{params.tagId}/picture/1">Picture 1</a>
<a href="/tag/{params.tagId}/picture/2">Picture 2</a>
