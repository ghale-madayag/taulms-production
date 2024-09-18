<template>
    <nav class="course-nav">
        <div v-for="(link,index) in links">
            <a v-if="auth.role =='student'" disabled
               class="page-num"
               :class="link.id == active ? 'bg-primary text-white borderActive': ''"
               v-html="index < finished_quiz.length ?  '✔' : index+1 "
            ></a>
            <a v-else disabled
               class="page-num"
               :class="link.id == active ? 'bg-primary text-white borderActive': ''"
               v-html="index+1"
            ></a>
            
        </div>
    </nav>
</template>

<script setup>
let props = defineProps({
    auth: Object,
    links: Object,
    active: Number,
    finished_quiz: Object,
});
</script>

<style>
.borderActive{
    border: 2px solid #348f6c!important
}

</style>
