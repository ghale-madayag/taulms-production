<template>
    <Head>
        <title>Quiz</title>
    </Head>
    <Breadcrumbs pageTitle="Quiz" :auth="auth.permission"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <div class="row card-group-row mb-lg-8pt">
                <div class="col-lg-7 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-6">
                                <div class="card-body">
                                    <h6 class="text-50">Overview</h6>
                                    <div class="h1 mb-0">{{ total }}</div>
                                    <div class="d-flex flex-column">
                                    <strong>Total Quiz</strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="h1 mb-0">{{ published }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Published</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 border-left">
                                <div class="card-body">
                                    <h6 class="text-50">Engagement</h6>
                                    <div class="h1 mb-0">0</div>
                                    <div class="d-flex flex-column">
                                        <strong>Total Views</strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="h1 mb-0">{{ pending }}</div>
                                    <strong>Draft</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 card-group-row__col">
                    <div class="card card-group-row__card" >
                        <div class="card-body">
                            <h6>Latest Quizzes</h6>
                            <div class="d-flex align-items-center" v-for="(data, index) in latest">
                                <div class="mr-12pt" v-if="index==0">
                                    <div class="avatar avatar-xl avatar-4by3">
                                        <img :src="data.thumbnail ? '/storage/'+data.thumbnail : '/images/blank.webp'" alt="Avatar" class="avatar-img rounded">
                                    </div>
                                </div>
                                <div class="flex" v-if="index==0">
                                    <Link :href="'/quiz/'+data.slug+'/edit'" :title="data.title" class="card-title">{{ data.title.substring(0,20)+"..." }}</Link>
                                    <small class="text-50">{{ data.created }}</small>
                                </div>
                                <div class="dropdown" v-if="index==0">
                                    <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
<!--                                        <Link :href="'/subject/quiz/'" class="dropdown-item">Review</Link>-->
                                        <Link v-if="auth.permission.includes('edit quiz')" :href="'/quiz/'+data.slug+'/edit'" class="dropdown-item">Edit</Link>
                                        <div class="dropdown-divider" v-if="auth.permission.includes('delete quiz')"></div>
                                        <a v-if="auth.permission.includes('delete quiz')" href="javascript:void(0)" @click="destroy(quiz.id)" class="dropdown-item text-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2"  v-for="(data, index) in latest" :class="{'hidden' : index==0}">
                                <div class="mr-8pt" v-if="index!=0">
                                    <div class="avatar avatar-sm avatar-4by3">
                                        <img :src="data.thumbnail ? '/storage/'+data.thumbnail : '/images/blank.webp'" alt="Avatar" class="avatar-img rounded">
                                    </div>
                                </div>
                                <div class="flex d-flex flex-column" v-if="index!=0">
                                    <Link v-if="auth.permission.includes('edit quiz')" :href="'/quiz/'+data.slug+'/edit'" :title="data.title"  class="card-title">{{ data.title.substring(0,20)+"..." }}</Link>
                                    <small class="text-50">{{ data.created }}</small>
                                </div>
                                <div class="dropdown" v-if="index!=0">
                                    <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
<!--                                        <Link :href="'/subject/quiz/'" class="dropdown-item">Review</Link>-->
                                        <Link v-if="auth.permission.includes('edit quiz')" :href="'/quiz/'+data.slug+'/edit'" class="dropdown-item">Edit</Link>
                                        <div v-if="auth.permission.includes('delete quiz')" class="dropdown-divider"></div>
                                        <a v-if="auth.permission.includes('delete quiz')" href="javascript:void(0)" @click="destroy(quiz.id)" class="dropdown-item text-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-separator">
                <div class="page-separator__text">Quiz List</div>
            </div>
            <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-24pt" style="white-space: nowrap;">
                <small class="flex text-muted text-headings text-uppercase mr-3 mb-2 mb-sm-0"></small>
                <div class="w-auto ml-sm-auto table flex align-items-center mb-2 mb-sm-0">
                    <form class="form-inline">
                        <label class="mr-sm-2 form-label" for="inlineFormFilterBy">Filter by:</label>
                        <input v-model="search"
                               type="text"
                               class="flex w-auto form-control search mb-2 mr-sm-2 mb-sm-0"
                               id="inlineFormFilterBy"
                               placeholder="Search ...">
                        <select id="inlineFormRole" class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="selected">
                            <option value="0" selected="selected">All Lessons</option>
                            <option value="1">Published</option>
                            <option value="2">Draft</option>
                        </select>
                    </form>
                </div>
            </div>
            <QuizCard @delete="destroy" :quizzes="quiz.data" :auth="auth.permission"/>
            <ListPagination :links="quiz.links"/>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import QuizCard from "../../Shared/QuizCard";
import ListPagination from "../../Shared/ListPagination";
import {ref, watch} from "vue";
import debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";
import {useForm} from "@inertiajs/inertia-vue3";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

let props = defineProps({
    auth: Object,
    quiz:Object,
    filters: Object,
    total: Number,
    published: Number,
    pending: Number,
    latest: Object,
})
let search = ref(props.filters.search);
let selected = ref(props.filters.selected);
let form = useForm();

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

watch([search, selected], debounce(function (value){
    Inertia.get(
        '/quiz',
        {search: value[0],selected: value[1]},
        {
            preserveScroll:true,
            preserveState: true,
            replace: true
        })
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
</script>

<style scoped>

</style>
