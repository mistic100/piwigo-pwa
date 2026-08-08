<script lang="ts">
    import type { Image } from '$lib/api/model';

    let props: { picture: Image, categoryId?: string, tagId?: string } = $props();
</script>

<h2>{props.picture.name}
{#if props.categoryId}
    in album {props.categoryId}
{:else if props.tagId}
    in tag {props.tagId}
{/if}
</h2>
