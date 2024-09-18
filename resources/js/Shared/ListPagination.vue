<template>
    <div class="card p-8pt mb-0 d-inline-block">
        <ul class="pagination justify-content-start pagination-xsm m-0">
            <template v-for="link in links">
                <li class="page-item" :class="link.url ? '' : 'disabled'">
                    <Component :is="link.url ? 'Link' : 'span'" :class="link.active ? 'text-white bg-primary' : ''" class="page-link" :href="link.url" preserve-scroll>
                        <span v-html="link.label"></span>
                    </Component>
                </li>
            </template>
        </ul>
    </div>
</template>

<script setup>
let props = defineProps({
    links: Object,
})
</script>
