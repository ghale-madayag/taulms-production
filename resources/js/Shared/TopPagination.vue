<template>
    <nav class="course-nav">
        <div v-for="(link,index) in links">
            <a disabled
               class="page-num"
               :class="link.slug == active ? 'bg-primary text-white borderActive': ''"
               v-html="index < finished.length ?  '✔' : index+1 "
            ></a>
        </div>
    </nav>
</template>

<script setup>
let props = defineProps({
    links: Object,
    active: String,
    finished: Object,
});
</script>

<style scoped>
.borderActive{
    border: 2px solid #348f6c!important
}

</style>
