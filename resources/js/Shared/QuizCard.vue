<template>
    <div class="posts-cards mb-24pt">
        <div class="card posts-card" v-for="quiz in quizzes">
            <div class="posts-card__content d-flex align-items-center flex-wrap" :class="{'draft' : quiz.published == 0}">
                <div class="avatar avatar-lg mr-3">
                    <a href="">
                        <img :src="quiz.thumbnail ? '/storage/'+quiz.thumbnail : '/images/blank.webp'" alt="avatar" class="avatar-img rounded">
                    </a>
                </div>
                <div class="posts-card__title mr-5 flex d-flex flex-column">
                    <a class="card-title mr-3" :title="quiz.title" :class="{'text-muted' : quiz.published == 0}">{{ quiz.title }}</a>
                    <small class="text-50">{{ quiz.question.length ? quiz.question.length : 'No' }} Questions</small>
                </div>
                <div class="d-flex align-items-center flex-column flex-sm-row posts-card__meta">
                    <div class="text-50 text-uppercase posts-card__tag d-flex align-items-center mr-5">

                    </div>
                    <div class="text-50 posts-card__date">
                        <small>{{ quiz.created }}</small>
                    </div>
                </div>
                <div class="dropdown ml-auto">
                    <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                        <i class="material-icons">more_vert</i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
<!--                        <Link :href="'/quiz/'+quiz.slug+'/edit'" class="dropdown-item">Review</Link>-->
                        <Link v-if="auth.includes('edit quiz')" :href="'/quiz/'+quiz.slug+'/edit'" class="dropdown-item">Edit</Link>
                        <div v-if="auth.includes('delete quiz')" class="dropdown-divider"></div>
                        <a v-if="auth.includes('delete quiz')" href="javascript:void(0)" @click="destroy(quiz.id)" class="dropdown-item text-danger">Delete</a>
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
    auth: Object,
    quizzes: Object,
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
