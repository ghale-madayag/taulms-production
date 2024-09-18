<template>
    <Head>
        <title>Profile</title>
    </Head>
    <AvatarProfile :profile="user" :url="'student'"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="row mb-lg-16pt">
                <div class="col-lg-8 mb-8pt mb-sm-0">
                    <div class="page-separator">
                        <div class="page-separator__text">Learning Paths</div>
                    </div>
                    <div class="alert alert-soft-warning mb-24pt" v-if="!subject.length">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <span>No pending subject.</span><br>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div v-for="data in subject" class="card js-overlay card-sm overlay--primary-dodger-blue stack stack--1 mb-16pt">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <div class="flex">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded mr-12pt z-0 o-hidden">
                                            <div class="overlay">
                                                <img :src="data.subject.thumbnail ? '/storage/'+data.subject.thumbnail : '/images/blank.webp'"
                                                     width="40"
                                                     height="40"
                                                     alt="Angular"
                                                     class="rounded">
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <div class="card-title">{{ data.subject.title}}</div>
                                            <p class="flex text-50 lh-1 mb-0"><small>{{ data.subject.code }}</small></p>
                                        </div>
                                    </div>
                                </div>
                                <Link v-if="data.length !=0 && data.finished.includes('0') && data.term.includes('1') && !auth.permission.includes('view all students')"
                                   @click="loadContinue(data.subject.slug,1, data.subject.lesson[0].section_id)"
                                   class="ml-4pt btn btn-sm btn-link text-secondary">Resume Midterm</Link>
                                <Link v-else-if="data.length !=0 && data.finished.includes('0') && 
                                data.term.includes('2') && 
                                !auth.permission.includes('view all students')
                                && data.subject.lesson.map(a=>a.term).includes('2')"
                                   @click="loadStart(data.subject.slug,2, data.subject.lesson[0].section_id)"
                                   class="ml-4pt btn btn-sm btn-link text-secondary">Resume Finals</Link>
                                <Link v-else-if="!data.subject.lesson.map(a=>a.term).includes('2')" class="ml-4pt btn btn-sm btn-accent text-white">Finals Not Ready</Link>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="page-separator">
                        <div class="page-separator__text">Announcements</div>
                    </div>
                    <div class="alert alert-soft-warning mb-24pt" v-if="!announ.map(a=>a.length > 0).includes(true)">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <span>No announcement</span><br>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-flush" v-for="announce in announ">
                        <div v-for="announce in announce">
                            <div class="list-group-item d-flex align-items-start p-16pt">
                                <div class="d-flex flex-column mr-16pt">
                                    <small class="text-uppercase text-50">{{ announce.month }}</small>
                                    <strong class="border-bottom-2 border-bottom-accent">{{ announce.day }}</strong>
                                </div>
                                <div class="flex">
                                    <div class="mb-8pt"><strong>{{ announce.title }}</strong></div>
                                    <p class="mb-0 text-50 linkData" v-html="announce.full_text.replace(/<br>/g,'')"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-separator">
                <div class="page-separator__text">Quizzes</div>
            </div>
            <div class="alert alert-soft-warning mb-24pt" v-if="!quiz.data.length">
                <div class="d-flex flex-wrap align-items-start">
                    <div class="mr-8pt">
                        <i class="material-icons">access_time</i>
                    </div>
                    <div class="flex" style="min-width: 180px">
                        <small class="text-black-100">
                            <span>No pending quiz.</span><br>
                        </small>
                    </div>
                </div>
            </div>
            <div class="row card-group-row">
                <div class="card-group-row__col col-md-6" v-for="data in quiz.data">
                    <div class="card card-group-row__card card-sm">
                        <div class="card-body d-flex align-items-center">
                            <a href="" class="avatar overlay overlay--primary avatar-4by3 mr-12pt">
                                <img :src="data.quiz.thumbnail ? '/storage/'+data.quiz.thumbnail : '/images/blank.webp'" alt="" class="avatar-img rounded">
                            </a>
                            <div class="flex mr-12pt">
                                <a class="card-title" href="">{{ data.quiz.title }}</a>
                                <div class="card-subtitle text-50">{{ getDiff(data.updated_at) }}</div>
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <span class="text-headings lh-1 text-50">{{ data.quiz.formatted_date }}</span>
                                <small class="text-50 text-uppercase text-headings">End Date</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center">
                                <div class="flex mr-2">
                                    <a href="javascript:void(0)" @click="continueQuiz(data.quiz.slug,data.quiz.student_quiz_status_pending[0].question_id)" class="btn btn-light btn-sm">
                                        <i class="material-icons icon--left">refresh</i> Resume </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-32pt">
                <ListPagination :links="quiz.links"/>
            </div>
        </div>
    </div>

</template>

<script setup>
import AvatarProfile from "../../Shared/AvatarProfile.vue";
import {loadScript} from "vue-plugin-load-script";
import {useForm} from "@inertiajs/inertia-vue3";
import { Inertia } from '@inertiajs/inertia'
import ListPagination from "../../Shared/ListPagination";
let props = defineProps({
    auth:Object,
    user:Object,
    subject: Object,
    quiz: Object,
    announ: Object,
})

let form = useForm({
    subject: null,
    term: null,
    section: null,
});

function converTime(remaining){
    var time_left = remaining.substring(0, remaining.indexOf('.'));
    return time_left;
}

function continueQuiz(quiz,question){
    Inertia.get('/quiz/'+quiz+'/question/'+question);
}

function getDiff(updated_time){

    var difference;
    var date1 = new Date(updated_time);
    var date2 = new Date();

    var diff = date2.getTime() - date1.getTime();

    var msec = diff;
    var month = Math.floor( msec / 1000 / 60 / (60*24) % 365 / 30)
    var days = Math.floor( msec / 1000 / 60 / (60*24))
    var hh = Math.floor(msec / 1000 / 60 / 60);
    msec -= hh * 1000 * 60 * 60;
    var mm = Math.floor(msec / 1000 / 60);
    msec -= mm * 1000 * 60;
    var ss = Math.floor(msec / 1000);
    msec -= ss * 1000;

    if(month !=0){
        if(month == 1){
            difference = month+' month ago';
        }else{
            difference = month+' months ago';
        }
    }else if(days != 0){
        if(days == 1){
            difference = days+' day ago';
        }else{
            difference = days+' days ago';
        }
    }else if(hh != '00'){
        if(hh == 1){
            difference = hh + ' hour ago';
        }else{
            difference = hh + ' hours ago';
        }
    }else if(mm != '00'){
        if(mm == 1){
            difference = 'a min hour ago';
        }else{
            difference = mm + ' minutes ago';
        }
    }else if(ss < '60'){
        difference = ss + ' seconds ago';
    }

    return difference;
}
function loadStart(subject, term, section){

    form.subject = subject;
    form.term = term;
    form.section = section;

    form.post('/lesson/'+subject+'/start');

}

function loadContinue(subject, term, section){

form.subject = subject;
form.term = term;
form.section = section;

form.post('/lesson/'+subject+'/getActive');

}
</script>

