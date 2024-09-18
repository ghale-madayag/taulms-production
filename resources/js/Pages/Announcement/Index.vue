<template>
     <Head>
        <title>Announcement</title>
    </Head>
    <Breadcrumbs pageTitle="Announcement" parentTitle="Courses"/>
    <div class="container page__container">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
        <div class="page-section">
            <div class="card mb-0">
                <div class="card-header">
                    <form class="form-inline">
                        <label class="mr-sm-2 form-label" for="filter_name">Filter By</label>
                        <input v-model="search" id="filter_name" type="text" class="form-control search mb-2 mr-sm-2 mb-sm-0" placeholder="Search by title">
                        <div class="ml-auto mb-2 mb-sm-0 custom-control-inline mr-0">
                            <div class="col-sm-auto">
                                <label>Total</label>
                                <h3>{{ total }}</h3>
                            </div>
                        </div>
                        <div class="ml-auto mb-2 mb-sm-0 custom-control-inline mr-0">
                           <Link v-if="auth.permission.includes('create all announcement')" :href="'/announcement/'+subjects.slug+'/'+section.id+'/'+user.id+'/create'" :preserve-scroll="true" class="btn bg-primary text-white"><i class="material-icons">add</i> Create New</Link>
                           <Link v-else-if="auth.permission.includes('create announcement')" :href="'/announcement/'+subjects.slug+'/'+section.id+'/create'" :preserve-scroll="true" class="btn bg-primary text-white"><i class="material-icons">add</i> Create New</Link>
                       </div>
                    </form>
               </div>
               <div class="list-group list-group-flush">
                   <div v-for="announ in announs.data" class="list-group-item d-flex flex-column flex-sm-row align-items-sm-center px-12pt">
                       <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                        <div class="avatar avatar-lg mr-3">
                            <div class="d-flex flex-column mr-16pt">
                                <small class="text-uppercase text-50">{{ announ.month }}</small>
                                <strong class="border-bottom-2 border-bottom-accent">{{ announ.day }}</strong>
                            </div>
                        </div>
                           <div class="media-body">
                               <div class="d-flex flex-column">
                                   <p class="mb-0"><strong class="js-lists-values-employee-name">{{announ.title}}</strong></p>
                                   <small class="js-lists-values-employee-email text-50" v-html="announ.full_text.substring(0,100)+'...'">
                                    </small>
                               </div>
                           </div>
                       </div>
                       <div class="d-flex align-items-center">
                           <div class="dropdown ml-auto">
                               <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                               <div class="dropdown-menu dropdown-menu-right">
                                   <Link v-if="auth.permission.includes('edit announcement')" :href="'/announcement/'+announ.slug+'/edit/'" class="dropdown-item">Edit</Link>
                                   <a v-if="auth.permission.includes('delete announcement')" href="#" class="dropdown-item text-danger" @click="destroy(announ.id)">
                                        Delete
                                    </a>
                               </div>
                           </div>
                       </div>
                   </div>
                   <div v-if="announs.data == 0" class="list-group-item">
                       <p class="mb-0 text-center"><strong class="text-50">No Data Found</strong></p>
                   </div>
               </div>
               <div class="card-footer p-8pt d-inline-block">
                   <ul class="pagination justify-content-start pagination-xsm m-0">
                       <li v-for="link in announs.links" class="page-item" :class="link.url ? '' : 'disabled'">
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
    announs: Object,
    filters:Object,
    user: Object,
    subjects: Object,
    section: Object, 
    total: Number,
})

let form = useForm();
let search = ref(props.filters.search);

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

watch([search], debounce(function (value){
    Inertia.get(
        '/announcement/'+props.subjects.slug+'/'+props.section.id,
        {search: value[0]},
        {
            preserveState: true,
            preserveScroll: true,
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
            form.delete('/announcement/list/'+id,{
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