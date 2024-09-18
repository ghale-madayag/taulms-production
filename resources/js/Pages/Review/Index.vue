<template>
    <Head>
        <title>Review</title>
    </Head>
    <div class="navbar navbar-list navbar-light bg-white border-bottom-2 border-bottom navbar-expand-sm" style="white-space: nowrap;">
        <div class="container page__container">
            <nav class="nav navbar-nav">
                <div class="nav-item navbar-list__item">
                    <Link :href="'/student/'+quiz.lesson.subject.slug+'/'+quiz.lesson.section_id" class="nav-link h-auto"><i class="material-icons icon--left">keyboard_backspace</i> Back to Student List</Link>
                </div>
            </nav>
        </div>
    </div>
    <div class="bg-header py-32pt">
        <div class="container page__container">
            <div class="d-flex flex-wrap align-items-end mb-16pt">
                <h1 class="h2 mb-0 flex m-0">{{ student.full_name }}</h1>
                <p class="h1 text-50 font-weight-light m-0">Score: {{score.score}}/{{ score.total }}</p>
            </div>
            <p class="hero__lead measure-hero-lead text-70 mb-24pt">
                {{ student.id }}
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
            </ul>
            <div class="nav navbar-nav ml-sm-auto navbar-list__item">

            </div>
        </div>
    </div>
    <div class="container page__container">
        <div v-for="(quizzes,index) in quiz.question" class="border-left-2 page-section pl-32pt">
           <ResultViewer :quiz="quizzes" :index="index+1" @saveScore="save" :process="form" :student="student.id"/>
        </div>
    </div>
</template>
<script setup>
import ResultViewer from "../../Shared/ResultViewer";
import {useForm} from "@inertiajs/inertia-vue3";


let props = defineProps({
    quiz:Object,
    student: Object,
    score: Object
})

let form = useForm({
    id:'',
    score:'',
    student:'',
});

function save(score,id,student){
    form.id = id;
    form.score = score;
    form.student = student;
    
    form.post('/question/'+id+'/savescore',{
        onStart: () => { },
        onFinish: () => { },
        onSuccess:()=>{
            
        },
        preserveScroll: true,
    });
}
</script>

<style>
.bg-header {
    background-color: #dfdfdf !important
}
</style>