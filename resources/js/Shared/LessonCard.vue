<template>
    <div class="posts-cards mb-24pt">
        <div class="card posts-card" v-for="lesson in lessons">
            <div class="posts-card__content d-flex align-items-center flex-wrap" :class="{'draft' : lesson.published == 0}">
                <div class="avatar avatar-lg mr-3">
                    <a href="javascript:void(0);">
                        <img :src="lesson.thumbnail ? '/storage/'+lesson.thumbnail : '/images/blank.webp'" alt="avatar" class="avatar-img rounded">
                    </a>
                </div>
                <div class="posts-card__title mr-5 flex d-flex flex-column">
                    <a class="card-title mr-3" :title="lesson.title" :class="{'text-muted' : lesson.published == 0}">{{ lesson.title }}</a>
                    <small class="text-50">{{ lesson.quiz.length ? lesson.quiz.length : 'No' }} Quiz</small>
                </div>
                <div class="d-flex align-items-center flex-column flex-sm-row posts-card__meta">
                    <div class="text-50 text-uppercase posts-card__tag d-flex align-items-center mr-5">
                        <div v-if="lesson.attachement.length">
                            <i class="material-icons text-muted-light mr-1">folder_open</i> {{ lesson.attachement.length ? lesson.attachement.length : 0 }} Files
                        </div>
                    </div>
                    <div class="text-50 posts-card__date">
                        <small>{{ lesson.created }}</small>
                    </div>
                </div>
                <div class="dropdown ml-auto">
                    <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                        <i class="material-icons">more_vert</i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <Link :href="'/subject/'+lesson.subject.slug+'/lesson/'+lesson.slug+'/term/'+lesson.term+'/'+lesson.section_id" class="dropdown-item">Review</Link>
                        <Link v-if="auth.includes('edit lesson')" :href="'/lesson/'+lesson.slug+'/edit'" class="dropdown-item">Edit</Link>
                        <div v-if="auth.includes('delete lesson')" class="dropdown-divider"></div>
                        <a v-if="auth.includes('delete lesson')" href="javascript:void(0)" @click="destroy(lesson.id)" class="dropdown-item text-danger">Delete</a>
                    </div>
<!--                    <div class="dropdown-menu dropdown-menu-right">-->
<!--                        <Link :href="'/lessons/'" class="dropdown-item">Edit</Link>-->
<!--                        <Link :href="'/subject/'" class="dropdown-item text-danger">Delete</Link>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
let props = defineProps({
    auth:Object,
    lessons: Object,
})

const emit = defineEmits(['delete']);
function destroy(id) {
    emit('delete', id);
}
</script>

<style>
.draft{
    background:#272c3313 !important;
}
</style>
