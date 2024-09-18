<template>
    <Head>
       <title>Quiz</title>
   </Head>
   <Breadcrumbs pageTitle="Quiz" parentTitle="Courses"/>
   <div class="container page__container">
       <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
       <div class="page-section">
           <div class="card mb-0">
               <div class="card-header">
                   <form class="form-inline">
                       <label class="mr-sm-2 form-label" for="filter_name">Filter By</label>
                       <input v-model="quiz" id="filter_name" type="text" class="form-control search mb-2 mr-sm-2 mb-sm-0" placeholder="Search by title">
                       <select v-model="status" id="custom-select" class="form-control custom-select">
                           <option value="0" selected="selected">All</option>
                           <option value="1">Pubished</option>
                           <option value="2">Draft</option>
                       </select>
                       <div class="ml-auto mb-2 mb-sm-0 custom-control-inline mr-0">
                           <div class="col-sm-auto">
                               <label>Published</label>
                               <h3>{{ published }}</h3>
                           </div>
                           <div class="col-sm-auto">
                               <label>Draft</label>
                               <h3>{{ pending }}</h3>
                           </div>
                           <div class="col-sm-auto">
                               <label>Total</label>
                               <h3>{{ total }}</h3>
                           </div>
                       </div>
                       <div class="ml-auto mb-2 mb-sm-0 custom-control-inline mr-0">
                           <Link v-if="auth.permission.includes('create all quiz')" :href="'/quiz/'+subjects.slug+'/'+section.id+'/'+user.id+'/create'" :preserve-scroll="true" class="btn bg-primary text-white"><i class="material-icons">add</i> Create New</Link>
                           <Link v-else-if="auth.permission.includes('create quiz')" :href="'/quiz/'+subjects.slug+'/'+section.id+'/create'" :preserve-scroll="true" class="btn bg-primary text-white"><i class="material-icons">add</i> Create New</Link>
                       </div>
                   </form>
               </div>
               <div class="list-group list-group-flush">
                   <div v-for="quiz in quizzes.data" class="list-group-item d-flex flex-column flex-sm-row align-items-sm-center px-12pt">
                       <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                           <div class="avatar avatar-sm mr-8pt">
                               <div class="avatar avatar-sm mr-8pt">
                                   <img :src="quiz.thumbnail ? '/storage/'+quiz.thumbnail : '/images/blank.webp'" alt="Avatar" class="avatar-img rounded">
                               </div>
                           </div>
                           <div class="media-body">
                               <div class="d-flex flex-column">
                                   <p class="mb-0"><strong class="js-lists-values-employee-name">{{quiz.title.substring(0,80)+"..." }}</strong></p>
                                   <small class="js-lists-values-employee-email text-50">
                                    <i class="material-icons text-muted-light mr-1 icon-16pt">access_time</i>{{ quiz.quiz_date }}
                                </small>
                               </div>
                           </div>
                       </div>
                       <div class="d-flex align-items-center flex-column flex-sm-row posts-card__meta">
                           <div class="mr-3 text-50 text-uppercase posts-card__tag d-flex align-items-center">
                               <i class="material-icons text-muted-light mr-1">help</i> {{ quiz.question_count }}
                           </div>
                           <div v-if="quiz.published==1" class="d-flex flex-column">
                               <small class="js-lists-values-status text-50 mb-4pt">Pubished</small>
                               <span class="indicator-line rounded bg-primary"></span>
                           </div>
                           <div v-else class="d-flex flex-column">
                               <small class="js-lists-values-status text-50 mb-4pt">Draft</small>
                               <span class="indicator-line rounded bg-warning"></span>
                           </div>
                       </div>
                       <div class="d-flex align-items-center">
                           <div class="dropdown ml-auto">
                               <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                               <div class="dropdown-menu dropdown-menu-right">
                                    <Link v-if="auth.permission.includes('edit quiz')  && quiz.question.length > 0" :href="'/faculty/review/'+quiz.slug+'/'" class="dropdown-item">Review</Link>
                                    <Link v-if="auth.permission.includes('edit quiz') && quiz.question.length > 0" :href="'/faculty/review/'+quiz.slug+'/'+quiz.question[0].id" class="dropdown-item">Preview</Link>
                                    <Link v-if="auth.permission.includes('edit quiz')" :href="'/quiz/'+quiz.slug+'/edit/'" class="dropdown-item">Edit</Link>
                                    <a v-if="auth.permission.includes('delete quiz')" href="#" class="dropdown-item" @click="duplicate(quiz.id)">
                                        Duplicate
                                    </a>
                                    <a v-if="auth.permission.includes('delete quiz')" href="#" class="dropdown-item text-danger" @click="destroy(quiz.id)">
                                        Delete
                                    </a>
                               </div>
                           </div>
                       </div>
                   </div>
                   <div v-if="quizzes.data == 0" class="list-group-item">
                       <p class="mb-0 text-center"><strong class="text-50">No Data Found</strong></p>
                   </div>
               </div>
               <div class="card-footer p-8pt d-inline-block">
                   <ul class="pagination justify-content-start pagination-xsm m-0">
                       <li v-for="link in quizzes.links" class="page-item" :class="link.url ? '' : 'disabled'">
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
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

let props = defineProps({
    auth: Object,
    quizzes: Object,
    user: Object,
    filters:Object,
    subjects: Object,
    section: Object,
    total: Number,
    published: Number,
    pending: Number,
})

let form = useForm();

let status = ref(props.filters.status);
let quiz = ref(props.filters.lesson);

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

watch([quiz, status], debounce(function (value){
    if(props.auth.permission.includes('create all quiz')){
        Inertia.get(
        '/quiz/'+props.subjects.slug+'/'+props.section.id+'/'+props.user.id,
        {quiz: value[0], status: value[1]},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        })
    }else{
        Inertia.get(
        '/quiz/'+props.subjects.slug+'/'+props.section.id,
        {quiz: value[0], status: value[1]},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        })
    }
    
}, 500))

function destroy(id) {
    swalBtn.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete('/quiz/list/'+id,{
                preserveScroll:true,
                onSuccess:() => {
                    swalBtn.fire(
                        'Deleted!',
                        'Your data has been deleted.',
                        'success'
                    )
                }
            });
        }
    })
}

function duplicate(id){
    form.post('/quiz/'+id+'/duplicate',{
        preserveScroll:true,
        onSuccess:() => {
            console.log('test');
        }
    });
}
</script>