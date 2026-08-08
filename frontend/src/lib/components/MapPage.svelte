<script lang="ts">
    let props: { categoryId?: string, tagId?: string } = $props();
</script>

<h2>Map
{#if props.categoryId}
    album {props.categoryId}
{:else if props.tagId}
    tag {props.tagId}
{:else}
    general
{/if}
</h2>
