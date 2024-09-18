<template>
    <Head>
        <title>Student</title>
    </Head>
    <Breadcrumbs pageTitle="Student" parentTitle="Courses"/>
    <div class="container page__container">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
        <div class="page-section">
            <div class="card mb-0">
                <div class="card-header">
                    <form class="form-inline">
                        <label class="mr-sm-2 form-label" for="filter_name">Filter By</label>
                        <input v-model="search" id="filter_name" type="text" class="form-control search mb-2 mr-sm-2 mb-sm-0" placeholder="Search by name">
                        <div class="ml-auto mb-2 mb-sm-0 custom-control-inline mr-0">
                            <div class="col-sm-auto">
                                <label>Total</label>
                                <h3>{{ total }}</h3>
                            </div>
                        </div>
                    </form>
               </div>
               <div class="list-group list-group-flush">
                    <div v-for="student in students.data" class="accordion js-accordion accordion--boxed list-group-flush" id="parent">
                        <div class="accordion__item rounded-0">
                            <a href="#" class="accordion__toggle collapsed" data-toggle="collapse" :data-target="'#course-toc-'+student.id" data-parent="#parent" aria-expanded="false">
                                <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                    <div class="avatar avatar-sm mr-8pt">
                                        <div class="avatar avatar-sm mr-8pt">
                                            <div class="avatar avatar-sm mr-8pt">
                                                <span class="avatar-title rounded-circle">{{ student.fl }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div class="d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-employee-name">{{student.full_name}}</strong></p>
                                            <small class="js-lists-values-employee-email text-50">
                                                    {{student.id }}
                                                </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex mr-3" style="max-width: 100%">
                                    <span class="lead text-headings lh-1">{{ getValue(student.student_lesson_statuses_count,lesson) }}</span>
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            :style="'width:'+getValue(student.student_lesson_statuses_count,lesson)"
                                        ></div>
                                    </div>
                                </div>
                                <span class="accordion__toggle-icon material-icons">keyboard_arrow_down</span>
                            </a>
                            <div class="accordion__menu collapse" :id="'course-toc-'+student.id" style="">
                                <div class="accordion__menu-link" v-for="(quiz,index) in student.student_quizzes">
                                    <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                        <i class="material-icons icon-16pt">hourglass_empty</i>
                                    </span>
                                    <a class="flex">{{ quiz.quiz.title }}</a>
                                    <strong>Score: {{ quiz.score }}/{{ quiz.total }}</strong>
                                    <Link :href="'/review/'+quiz.id+'/quiz'" class="btn btn-link text-primary">Review Quiz</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                   <!-- <div v-for="student in students.data" class="list-group-item d-flex flex-column flex-sm-row align-items-sm-center px-12pt">
                       <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                           <div class="avatar avatar-sm mr-8pt">
                               <div class="avatar avatar-sm mr-8pt">
                                <div class="avatar avatar-sm mr-8pt">
                                    <span class="avatar-title rounded-circle">{{ student.fl }}</span>
                                </div>
                               </div>
                           </div>
                           <div class="media-body">
                               <div class="d-flex flex-column">
                                   <p class="mb-0"><strong class="js-lists-values-employee-name">{{student.full_name}}</strong></p>
                                   <small class="js-lists-values-employee-email text-50">
                                        {{student.id }}
                                    </small>
                               </div>
                           </div>
                       </div>
                       <div class="d-flex align-items-center flex-column flex-sm-row posts-card__meta">
                           <div class="mr-3 text-50 text-uppercase posts-card__tag d-flex align-items-center">
                            <div class="flex mr-3" style="max-width: 100%">
                                <span class="lead text-headings lh-1">20%</span>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width:20%"
                                    ></div>
                                </div>
                            </div>
                           </div> -->
                           <!-- <div v-if="quiz.published==1" class="d-flex flex-column">
                               <small class="js-lists-values-status text-50 mb-4pt">Pubished</small>
                               <span class="indicator-line rounded bg-primary"></span>
                           </div>
                           <div v-else class="d-flex flex-column">
                               <small class="js-lists-values-status text-50 mb-4pt">Draft</small>
                               <span class="indicator-line rounded bg-warning"></span>
                           </div> -->
                       <!-- </div> -->
                       <!-- <div class="d-flex align-items-center">
                           <div class="dropdown ml-auto">
                               <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                               <div class="dropdown-menu dropdown-menu-right">
                                   <Link :href="'/quiz/'+quiz.slug+'/edit/'" class="dropdown-item">Edit</Link>
                                   <a v-if="auth.permission.includes('delete quiz')" href="#" class="dropdown-item text-danger" @click="destroy(quiz.id)">
                                        Delete
                                    </a>
                               </div>
                           </div>
                       </div> -->
                   <!-- </div> -->
                   <div v-if="students.data == 0" class="list-group-item">
                       <p class="mb-0 text-center"><strong class="text-50">No Data Found</strong></p>
                   </div>
               </div>
               <div class="card-footer p-8pt d-inline-block">
                   <ul class="pagination justify-content-start pagination-xsm m-0">
                       <li v-for="link in students.links" class="page-item" :class="link.url ? '' : 'disabled'">
                           <Component :is="link.url ? 'Link' : 'span'" :class="link.active ? 'text-white bg-primary' : ''" class="page-link" :href="link.url" preserve-scroll>
                               <span v-html="link.label"></span>
                           </Component>
                       </li>
                   </ul>
               </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import CourseHeader from '../../Shared/CourseHeader';
import {useForm} from "@inertiajs/inertia-vue3";
import {ref,watch} from "vue";
import  debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";

let props = defineProps({
        auth: Object,
        students: Object,
        user: Object,
        subjects: Object,
        section: Object, 
        lesson: Number,   
        filters:Object,
        total: Number,
    })


let form = useForm();

let status = ref(props.filters.status);
let search = ref(props.filters.search);

watch([search, status], debounce(function (value){
    Inertia.get(
        '/student/'+props.subjects.slug+'/'+props.section.id,
        {search: value[0], status: value[1]},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        })
}, 500))

function getValue(active, tot){
    let total = active/tot * 100;

    if(!isNaN(total)){
        total = total.toFixed()+'%'
    }else{
        total = 0+'%';
    }
    
    return total;
}

</script>