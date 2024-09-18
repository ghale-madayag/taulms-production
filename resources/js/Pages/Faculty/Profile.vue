<template>
    <Head>
        <title>Profile</title>
    </Head>
    <AvatarProfile :profile="auth" :url="'faculty'"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="row mb-lg-24pt">
                <div class="col-md-12">
                    <div class="page-separator">
                        <div class="page-separator__text">Overview</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-md-0">
                                <div class="card-header pb-0 border-0 d-flex">
                                    <div class="flex">
                                        <div class="h2 mb-0">{{ subjectTotal }}</div>
                                        <p class="card-title">{{ subjectTotal > 1 ? 'Courses' : 'Course' }}</p>
                                    </div>
                                    <Link href="/subject" class="ml-4pt btn btn-sm btn-link text-secondary">View All</Link>
                                </div>
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-md-0">
                                <div class="card-header pb-0 border-0 d-flex">
                                    <div class="flex">
                                        <div class="h2 mb-0">{{ lessonTotal }}</div>
                                        <p class="card-title">{{ lessonTotal > 1 ? 'Lessons' : 'Lesson' }}</p>
                                    </div>
                                    <!-- <Link href="/lesson" class="ml-4pt btn btn-sm btn-link text-secondary">View All</Link> -->
                                </div>
                                <div class="card-body">

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-md-0">
                                <div class="card-header pb-0 border-0 d-flex">
                                    <div class="flex">
                                        <div class="h2 mb-0">{{ quizTotal }}</div>
                                        <p class="card-title">{{ quizTotal > 1 ? 'Quizzes' : 'Quiz' }}</p>
                                    </div>
                                    <!-- <Link href="/quiz" class="ml-4pt btn btn-sm btn-link text-secondary">View All</Link> -->
                                </div>
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-lg-16pt mt-40pt">
                <div class="col-lg-8 mb-8pt mb-sm-0">
                    <div class="page-separator">
                        <div class="page-separator__text">Recently Added Lessons</div>
                    </div>
                    <div class="alert alert-soft-warning mb-24pt" v-if="!lessons.length">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <span>No lessons created yet.</span><br>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div v-for="lesson in lessons">
                        <div class="card js-overlay card-sm overlay--primary-dodger-blue stack stack--1 mb-16pt" data-toggle="popover" data-trigger="click">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <div class="flex">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded mr-12pt z-0 o-hidden">
                                                <div class="overlay">
                                                    <a href="" class="avatar avatar-sm">
                                                        <img :src="lesson.thumbnail ? '/storage/'+lesson.thumbnail : '/images/blank.webp'" alt="avatar" class="avatar-img rounded-circle">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="flex">
                                                <Link :href="'/subject/'+lesson.subject.slug+'/lesson/'+lesson.slug+'/term/'+lesson.term+'/'+lesson.section_id" class="card-title">{{ lesson.title.substring(0,35)+"..." }}</Link>
                                                <p class="flex text-50 lh-1 mb-0">
                                                    <small>{{ getDiff(lesson.updated_at) }}</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <Link :href="'/subject/'+lesson.subject.slug+'/lesson/'+lesson.slug+'/term/'+lesson.term+'/'+lesson.section_id" class="ml-4pt btn btn-sm btn-link text-secondary border-1 border-secondary">View</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="page-separator">
                        <div class="page-separator__text">Recenly Added Quizzes</div>
                    </div>
                    <div class="alert alert-soft-warning mb-24pt" v-if="!quizzes.length">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <span>No quizzes created yet.</span><br>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div v-for="quiz in quizzes">
                        <div class="row card-group-row">
                            <div class="card-group-row__col col-md-12">
                                <div class="card card-group-row__card card-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <a href="" class="avatar avatar-4by3 mr-12pt">
                                            <img :src="quiz.thumbnail ? '/storage/'+quiz.thumbnail : '/images/blank.webp'" alt="avatar" class="avatar-img rounded">
                                            <span class="overlay__content"></span>
                                        </a>
                                        <div class="flex mr-12pt">
                                            <a class="card-title" :href="'/quiz/'+quiz.slug+'/edit'">{{ quiz.title.substring(0,35)+"..." }}</a>
                                            <div class="card-subtitle text-50">{{ getDiff(quiz.updated_at) }}</div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center">
                                            <div class="flex mr-2">
                                                <a :href="'/quiz/'+quiz.slug+'/edit'" class="btn btn-light btn-sm">
                                                    Edit <span class="badge badge-dark badge-notifications ml-2"></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           <div class="mt-40pt">
               <div class="page-separator">
                   <div class="page-separator__text">Course</div>
               </div>
               <div class="row card-group-row">
                   <div class="card-group-row__col col-md-6" v-for="subject in subject.data">
                       <div class="card card-group-row__card card-sm">
                           <div class="card-body d-flex align-items-center">
                               <a href="" class="avatar avatar-4by3 mr-12pt">
                                   <img :src="subject.thumbnail ? '/storage/'+subject.thumbnail : '/images/blank.webp'" alt="avatar" class="avatar-img rounded">
                                   <span class="overlay__content"></span>
                               </a>
                               <div class="flex mr-12pt">
                                    <Link :href="'/subject/'+subject.slug+'/'+subject.section_id" class="card-title">{{ subject.title }}</Link>
                                    <div class="card-subtitle text-50">{{ subject.section.name }}</div>
                               </div>
                           </div>
                           <div class="card-footer">
                               <div class="d-flex align-items-center">
                                   <div class="flex mr-2" v-if="subject.section.meet.length > 0" v-for="meet in subject.section.meet">
                                        <a :href="meet.link" target="_blank" class="ml-4pt btn btn-sm btn-custom text-white">
                                            <i class="material-icons mr-2">videocam</i> 
                                            Join with Google Meet
                                        </a>
                                   </div>
                                   <div class="dropdown">
                                       <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                                           <i class="material-icons">more_horiz</i>
                                       </a>
                                       <div class="dropdown-menu dropdown-menu-right">
                                           <Link :href="'/subject/'+subject.slug+'/'+subject.section_id" class="dropdown-item">View</Link>
                                           <Link :href="'/student/'+subject.slug+'/'+subject.section_id" class="dropdown-item">View Students</Link>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="mb-32pt">
                   <ListPagination :links="subject.links"/>
               </div>
           </div>
        </div>
    </div>
</template>

<script setup>
import AvatarProfile from "../../Shared/AvatarProfile";
import {loadScript} from "vue-plugin-load-script";
import ListPagination from "../../Shared/ListPagination";

loadScript('/js/app.js');

let props = defineProps({
    auth:Object,
    subject: Object,
    subjectTotal: Number,
    lessons: Object,
    lessonTotal: Number,
    quizzes: Object,
    quizTotal: Number,
})

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

</script>

<style>
.btn-custom{
    background: #1b6ddd!important;
}
</style>
