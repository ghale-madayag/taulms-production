<template>
    <Head>
        <title>Take Subject</title>
    </Head>
    <div class="navbar navbar-list navbar-light bg-white border-bottom-2 border-bottom navbar-expand-sm" style="white-space: nowrap;">
        <div class="container page__container">
            <nav class="nav navbar-nav">
                <div class="nav-item navbar-list__item">
                    <Link :href="'/subject/'+lesson.subject.slug+'/'+lesson.section_id" class="nav-link h-auto"><i class="material-icons icon--left">keyboard_backspace</i> Back to Course</Link>
                </div>
                <div class="nav-item navbar-list__item">
                    <div class="d-flex align-items-center flex-nowrap">
                        <div class="mr-16pt">
                            <Link href="#">
                                <img :src="lesson.subject.thumbnail ? '/storage/'+lesson.subject.thumbnail : '/images/blank.webp'" width="40" class="rounded"></Link>
                        </div>
                        <div class="flex">
                            <Link :href="'/subject/'+lesson.subject.slug"
                                  class="card-title text-body mb-0">{{ lesson.subject.title }}</Link>
                            <p class="lh-1 d-flex align-items-center mb-0">
                                <span class="text-50 small font-weight-bold mr-8pt">{{ lesson.subject.code }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <div class="bg-header pb-lg-64pt pb-lg-4pt py-32pt">
        <div class="container page__container">
            <!--Top pagination-->
            <TopPagination :links='links' :active="active" :finished="finished"/>
            <!--end of top pagination-->
            <div class="d-flex flex-wrap align-items-end mb-16pt">
                <h1 class="h2 mb-0 flex m-0"> {{ lesson.title }}</h1>
                <!-- <p class="h1 text-50 font-weight-light m-0">50:13</p> -->
            </div>
            <p class="hero__lead measure-hero-lead text-70 mb-24pt">
                {{ lesson.short_text }}
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
            <div class="nav navbar-nav ml-sm-auto navbar-list__item" v-for="(link,index) in links">
                <SimplePagination v-if="link.slug==active"
                                  :auth="auth"
                                  :link_next='index+1 < links.length ? index+1 : index'
                                  :link="links"
                                  :lesson="lesson"
                                  :active="active"
                                  :finished_quiz="finished_quiz"
                                  :link_prev="index == 0 ? index : index-1"
                />
            </div>
        </div>
    </div>
    <div class="container page-section">
        <div class="row">
            <div class="col-lg-8 imgWidth" v-html="lesson.full_text.replace(/<br>/g,'')">
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body py-16pt">
                        <h4 class="card-title mb-16pt">Downloadable Files</h4>
                        <div class="alert alert-soft-warning mb-0 p-8pt" v-if="lesson.attachement.length == 0">
                            <div class="d-flex align-items-start">
                                <div class="mr-8pt">
                                    <i class="material-icons">error_outline</i>
                                </div>
                                <div class="flex">
                                    <small class="text-100">There is no downloadable file available.</small>
                                </div>
                            </div>
                        </div>
                        <ul v-for="att in lesson.attachement" class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="form-row align-items-center">
                                    <div class="col">
                                        <small class="font-weight-bold"><span class="material-icons">attach_file</span> {{ att.attachement.substring(11,25)+"..." }}</small>
                                    </div>
                                    <div class="col-auto">
                                        <a :href="'/storage/attachement/lessons/'+att.attachement">
                                            <i class="material-icons">file_download</i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import TopPagination from "../../Shared/TopPagination";
import SimplePagination from "../../Shared/SimplePagination";
import { useForm } from '@inertiajs/inertia-vue3';
import {onMounted} from "vue";

let props = defineProps({
    lesson: Object,
    links: Object,
    active: String,
    auth: Object,
    finished: Object,
    finished_quiz: Object,
})

let form = useForm({
    lesson: props.active,
    term: props.lesson.term
});

onMounted(() => {
    if(props.auth.url=='student'){
        form.post('/lesson/'+props.lesson.slug+'/active');
    }
});
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

@media only screen and (max-width: 600px) {
    .col-lg-8.imgWidth img {
        max-width: 320px !important;
    }

    iframe.ql-video {
        width: 320px !important;
        height: 300px !important;
    }
}


</style>