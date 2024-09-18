<template>
    <Head>
        <title>Review</title>
    </Head>
    <div class="navbar navbar-list navbar-light bg-white border-bottom-2 border-bottom navbar-expand-sm" style="white-space: nowrap;">
        <div class="container page__container">
            <nav class="nav navbar-nav">
                <div class="nav-item navbar-list__item">
                    <Link :href="'/quiz/'+quiz.lesson.subject.slug+'/'+quiz.lesson.section_id" class="nav-link h-auto"><i class="material-icons icon--left">keyboard_backspace</i> Back to Quiz List</Link>
                </div>
            </nav>
        </div>
    </div>
    <div class="bg-header py-32pt">
        <div class="container page__container">
            <div class="d-flex flex-wrap align-items-end mb-16pt">
                <h1 class="h2 mb-0 flex m-0">{{ quiz.title }}</h1>
                <p class="h1 text-50 font-weight-light m-0">Points: {{ points }}</p>
            </div>
            <p class="measure-hero-lead text-70 mb-24pt">
                <div class="page-separator">
                    <div class="page-separator__text bg-trans">Instructions</div>
                </div>
                {{ quiz.description }}
            </p>
            <p class="measure-hero-lead text-70 mb-24pt" v-if="quiz.excluded !=''">
                <div class="page-separator">
                    <div class="page-separator__text bg-trans">Excluded Student</div>
                </div>
                <ul class="list-unstyled" v-for="stud in excluded">
                    <li class="d-flex align-items-center">
                        <span class="material-icons text-50 mr-8pt">check</span> 
                        <span class="text-70">{{ stud }}</span>
                    </li>
                </ul>
            </p>
            
        </div>
    </div>
    <div class="navbar navbar-expand-md navbar-list navbar-light bg-white border-bottom-2 "
         style="white-space: nowrap;">
        <div class="container page__container">
            <ul class="nav navbar-nav flex navbar-list__item">
                <li class="nav-item navbar-list__item">
                    <div class="media align-items-center">

                    </div>
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">schedule</i>
                    {{ quiz.quiz_schedule }} - {{ quiz.end_date }}
                </li>
            </ul>
            <div class="nav navbar-nav ml-sm-auto navbar-list__item">

            </div>
        </div>
    </div>
    <div class="container page__container">
        <div v-for="(quizzes,index) in quiz.question" class="border-left-2 page-section pl-32pt">
           <ReviewViewer :quiz="quizzes" :index="index+1"/>
        </div>
    </div>
</template>
<script setup>
import ReviewViewer from "../../Shared/ReviewViewer";

let props = defineProps({
    quiz:Object,
    points: Number,
    excluded: Object
})
</script>

<style>
.bg-header {
    background-color: #dfdfdf !important
}
.bg-trans{
    background-color: #DFDFDF !important;
}
</style>