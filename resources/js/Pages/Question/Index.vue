<template>
    <Head>
        <title>Take Quiz</title>
    </Head>
    <div class="navbar navbar-list navbar-light bg-white border-bottom-2 border-bottom navbar-expand-sm" style="white-space: nowrap;">
        <div class="container page__container">
            <nav class="nav navbar-nav">
                <div class="nav-item navbar-list__item">
    <!--                    <Link :href="'/subject/'+question.quiz.slug+'/lesson/'+" class="nav-link h-auto"><i class="material-icons icon&#45;&#45;left">keyboard_backspace</i> Back to Lesson</Link>-->
                </div>
            </nav>
        </div>
    </div>
    <div class="bg-header pb-lg-64pt pb-lg-4pt py-32pt">
        <div class="container page__container">
            <!--Top pagination-->
            <TopPaginationQuiz :links='links' :active="active" :finished_quiz="finished_quiz" :auth="auth"/>
            <!--end of top pagination-->
<!--            <div class="d-flex flex-wrap align-items-end mb-16pt">-->
<!--                <h1 class="h2 mb-0 flex m-0">{{ question.question ? strippedContent(question.question) : '' }}</h1>-->
<!--                 <p class="h1 text-50 font-weight-light m-0">50:13</p>-->
<!--            </div>-->
            <div v-for="(link, index) in links">
                <div class="d-flex flex-wrap align-items-end mb-16pt" v-if="question.id==links[index].id">
                    <h1 class="h2 mb-0 flex m-0">Question {{ index+1 }} of {{ links.length }}</h1>
                    <!-- <p class="h1 text-50 font-weight-light m-0"><span>0{{timer.hours}}</span>:<span>{{timer.minutes}}</span>:<span>{{timer.seconds}}</span></p> -->
                </div>
            </div>
            <p class="hero__lead measure-hero-lead text-70 mb-24pt">
                <div v-if="question.question" v-html="question.question"></div>
            </p>
            <p class="hero__lead measure-hero-lead text-70 mb-24pt">
                {{ question.description ? strippedContent(question.description) : '' }}
            </p>
        </div>
    </div>
    <div class="navbar navbar-expand-md navbar-list navbar-light bg-white border-bottom-2 "
         style="white-space: nowrap;">
        <div class="container page__container">
            <ul class="nav navbar-nav flex navbar-list__item">
                <li class="nav-item navbar-list__item">
                    <div class="media align-items-center">
                        <ul class="nav navbar-nav flex navbar-list__item">
                            <li class="nav-item" v-if="question.type==='multiple answer' || question.type==='select' || question.type==='multiple choice'">
                                <i class="material-icons text-50 mr-8pt">tune</i>
                                Choose the correct answer below:
                            </li>
                            <li class="nav-item" v-if="question.type==='short answer' || question.type==='essay'">
                                <i class="material-icons text-50 mr-8pt">tune</i>
                                Please type your answer below:
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div class="nav navbar-nav ml-sm-auto navbar-list__item" v-for="(link,index) in links">
                <SimplePaginationQuiz v-if="link.id==active"
                    :link_next='index+1 < links.length ? index+1 : index'
                    :link="links"
                    :question="question"
                    :active="active"
                    @finish="setNext"
                    @prev="setPrev"
                    :link_prev="index == 0 ? index : index-1"
                    :auth="auth"
                />
            </div>
        </div>
    </div>
    <div class="container page-section">
        <div v-if="errors.answers" class="alert alert-soft-accent alert-dismissible fade show mt-8pt" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <div class="d-flex flex-wrap align-items-start">
                <div class="mr-8pt">
                    <i class="material-icons">priority_high</i>
                </div>
                <div class="flex" style="min-width: 180px">
                    <small class="text-danger-100">
                        <strong v-if="question.type==='multiple answer' || question.type==='select' || question.type==='multiple choice'">Please select your answer</strong>
                        <strong v-else-if="question.type==='short answer' || question.type==='essay'">Please type your answer</strong>
                        <strong v-else-if="question.type==='file upload'">Please upload a file</strong>
                    </small>
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Your Answer</div>
        </div>
        <QuestionViewer
            v-model="answers[question.id]"
            :question="question"
            :answer="activeAnswer"
            :auth="auth"
        />
        <p class="text-50 mb-0" v-if="question.type === 'multiple answer'">Note: There can be multiple correct answers to this question.</p>
    </div>
</template>

<script setup>

import TopPaginationQuiz from "../../Shared/TopPaginationQuiz";
import SimplePaginationQuiz from "../../Shared/SimplePaginationQuiz";
import {ref, onMounted, watchEffect, watch} from 'vue';
import QuestionViewer from "../../Shared/QuestionViewer";
import { Inertia } from '@inertiajs/inertia'
import {useForm} from "@inertiajs/inertia-vue3";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { useTimer } from 'vue-timer-hook';
import debounce from "lodash/debounce";

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})


const answers = ref({});
let nextBtn = ref(false);

let props = defineProps({
    auth: Object,
    question: Object,
    links: Object,
    active: Number,
    errors: Object,
    auth: Object,
    activeAnswer:Object,
    flash: Object,
    finished_quiz:Object,
})

const time = new Date();
time.setSeconds(time.getSeconds() + props.question.quiz.duration);
//time.setSeconds(time.getSeconds() + 10);
const timer = useTimer(time);

let form = useForm();

let formIsExpired = useForm({
    next: 0,
    complete: false,
    quizzes_id: props.question.quiz_id,
    questions_id: props.question.id,
    answers: [],
    hrs: 0,
    min: 0,
    sec: 0,
});


onMounted(() => {
    if(props.auth.url=='student'){
        form.post('/question/'+props.question.id+'/active');
    }

    watchEffect(async () => {
        if(timer.isExpired.value) {
            isexpired();
        }
    })
});

function isexpired(){
    formIsExpired.answers = ['null'];
    formIsExpired.hrs = timer.hours
    formIsExpired.min = timer.minutes
    formIsExpired.sec = timer.seconds
    formIsExpired.complete = true;

    swalBtn.fire({
        title: 'Sorry!',
        text: 'Your out of time',
        icon: 'error',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        allowEscapeKey:false,
        confirmButtonText: 'Submit Quiz'
    }).then((result) => {
        if (result.isConfirmed) {
            formIsExpired.post('/question/'+props.question.id+'/finished')
        }
    })
}

function strippedContent(string) {
    //return string.replace(/<\/?[^>]+>/ig, " ");
    return string.replace(/<(?!img\s).*?>/ig, " ");
}

function setPrev(formQuestions,quiz,question){
    formQuestions.hrs = timer.hours
    formQuestions.min = timer.minutes
    formQuestions.sec = timer.seconds

   formQuestions.post('/question/'+props.question.id+'/prev',{
       onSuccess:()=>{
           Inertia.get(
               '/quiz/'+quiz+'/question/'+question
           )
       }
   })
}

function setNext(formQuestions){
    formQuestions.answers = answers.value

    formQuestions.hrs = timer.hours
    formQuestions.min = timer.minutes
    formQuestions.sec = timer.seconds
    formQuestions.post('/question/'+props.question.id+'/finished',{
        onStart: () => { nextBtn.value = true},
        onFinish: () => { nextBtn.value = false },
        onSuccess:()=>{
            if(!props.flash.error){
                nextBtn.value = false
                if(formQuestions.complete){
                    const flash = props.flash.scoreAndTotal;

                    const score = flash.score;
                    const total = flash.total;
                    const subject = flash.subject;
                    const lesson = flash.lesson;
                    const term = flash.term;
                    const section = flash.section;
                    const avg = (score / total) * 100;

                    const swalOptions = {
                        title: flash.checkType ? `Pending Score: ${score}/${total}` : `Your score: ${score}/${total}`,
                        text: flash.checkType ? 'Due to the inclusion of essay/file upload questions in this quiz, your score is currently pending. Please await further announcements regarding the assessment of these submissions. Thank you for your patience.' : avg < 60 ? `Sorry! You failed to pass ${props.question.quiz.title}. Your mark is under 60%.` : `Congratulations! You finished ${props.question.quiz.title}. Your mark is ${avg.toFixed()}%.`,
                        icon: flash.checkType ? 'info' : avg < 60 ? 'error' : 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'BACK TO LESSON'
                    };

                    swalBtn.fire(swalOptions).then((result) => {
                        if (result.isConfirmed) {
                            Inertia.get(`/subject/${subject}/lesson/${lesson}/term/${term}/${section}`);
                        }
                    });
                 
                }else{
                    Inertia.get(
                        '/quiz/'+props.question.quiz.slug+'/question/'+formQuestions.next
                    )
                }
            }else{
                props.errors.answers = props.flash.error;
            }
            
        }
    });
}


</script>

<style>
[dir] .course-nav::before {
    background-color: #303840;
}

.bg-header {
    background-color: #dfdfdf !important
}

.col-lg-8.imgWidth img {
    max-width: 560px !important;
}

iframe.ql-video {
    width: 600px !important;
    height: 400px !important;
}

.custom-file {
  height: 360px;
}
</style>
