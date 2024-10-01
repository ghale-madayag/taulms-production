<template>
    <Head>
        <title>Courses info</title>
    </Head>
    <Breadcrumbs pageTitle="Overview"/>
    
    <div class="container page__container">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
        <div class="page-section">
            <div class="row mb-0">
                <div class="col-lg-8">
                    <div class="alert alert-soft-warning mb-24pt" v-if="subjects.lesson_mid.length == 0 && subjects.lesson_fin.length == 0">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex"
                                 style="min-width: 180px">
                                <small class="text-black-100">
                                    <strong>Lessons is empty</strong><br>
                                    <span>Please try the again later. If you want to stay up to date please wait for further instructions.</span>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-primary" role="alert" v-if="msg">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">done</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <strong>Success!</strong> {{ msg }}
                                </small>
                            </div>
                        </div>
                    </div>
                   <div class="page-separator mb-4" v-if="subjects.lesson_mid.length != 0 || subjects.lesson_fin.length != 0">
                        <div class="page-separator__text">Midterm</div>
                    </div>
                    <div class="accordion js-accordion accordion--boxed list-group-flush" id="parent">
                        <draggable
                            :list="subjects.lesson_mid"
                            group="lesson"
                            @add="onAdd($event,1)"
                            itemKey="id"
                            @update="midMove"
                            v-bind="dragOptions"
                            :component-data="{
                              tag: 'ul',
                              type: 'transition-group',
                              name: 'flip-list'
                            }"
                            handle=".handle"
                            class="list-group"
                            @change="log"
                        >
                            <template #item="{ element, index }">
                                <li class="list-group shadow-none border-0" :key="element.id" :data-id="element.id" :data-index="index">
                                    <div class="accordion__item">
                                        <a href="#" class="accordion__toggle" data-toggle="collapse" :data-target="'#course-toc-'+element.id" data-parent="#parent" aria-expanded="true">
                                            <span v-if="auth.permission.includes('edit lesson') && subjects.lesson_mid?.at(-1)?.is_midterm_completed == 0" class="handle material-icons mr-2 text-muted" style="cursor: n-resize;">drag_handle</span>
                                            <span class="flex" :class="{'text-muted': element.published==0}">{{ element.title }}</span>
                                            <div class="dropdown" v-if="auth.permission.includes('create lesson')">
                                                <a href="#"
                                                   data-toggle="dropdown"
                                                   data-caret="false"
                                                   class="text-muted"><i class="material-icons">more_horiz</i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <Link v-if="auth.permission.includes('edit lesson')" :href="'/lesson/'+element.slug+'/edit/'"
                                                          class="dropdown-item">
                                                        Edit</Link>
                                                    <a v-if="auth.permission.includes('delete lesson')" href="#" class="dropdown-item text-danger" @click="destroy(element.id)">
                                                        Delete
                                                    </a>
                                                </div>
                                            </div>
                                            <span class="accordion__toggle-icon material-icons" :class="{'text-muted': element.published==0}">keyboard_arrow_down</span>
                                        </a>
                                        <div class="accordion__menu collapse" :id="'course-toc-'+element.id" style="">
                                            <div class="accordion__menu-link">
                                                <p class="text-70 mb-24pt">{{ element.short_text }}</p>
                                            </div>
                                            <div v-for="attach in element.attachement" >
                                                <div class="accordion__menu-link">
                                                    <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                                        <i class="material-icons icon-16pt">attach_file</i>
                                                    </span>
                                                    <a class="flex" download :href="'/storage/attachement/lessons/'+attach.attachement">{{ attach.attachement }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </draggable>
                    </div>

                    <div class="page-separator mt-5 mb-4" v-if="subjects.lesson_mid.length != 0 || subjects.lesson_fin.length != 0">
                        <div class="page-separator__text">Finals</div>
                    </div>
                    <div class="accordion js-accordion accordion--boxed list-group-flush" id="parents">
                        <draggable
                            :list="subjects.lesson_fin"
                            group="lesson"
                            @add="onAdd($event,2)"
                            itemKey="id"
                            @update="finMove"
                            v-bind="dragOptions"
                            handle=".handle"
                            :component-data="{
                              tag: 'ul',
                              type: 'transition-group',
                              name: 'flip-list'
                            }"
                            class="list-group"
                            @change="log"
                        >
                            <template #item="{ element, index }">
                                <li class="list-group shadow-none border-0" :key="element.id" :data-id="element.id" :data-index="index">
                                    <div class="accordion__item">
                                    <a href="#" class="accordion__toggle" data-toggle="collapse" :data-target="'#course-toc-'+element.id" data-parent="#parent" aria-expanded="true">
                                        <span v-if="auth.permission.includes('edit lesson') && subjects.lesson_fin?.at(-1)?.is_finals_completed == 0" class="handle material-icons mr-2 text-muted " style="cursor: n-resize;">drag_handle</span>
                                        <span class="flex" :class="{'text-muted': element.published==0}">{{ element.title }}</span>
                                        <div class="dropdown" v-if="auth.permission.includes('create lesson')">
                                            <a href="#"
                                               data-toggle="dropdown"
                                               data-caret="false"
                                               class="text-muted"><i class="material-icons">more_horiz</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <Link v-if="auth.permission.includes('edit lesson')" :href="'/lesson/'+element.slug+'/edit/'"
                                                      class="dropdown-item">
                                                    Edit</Link>
                                                <a v-if="auth.permission.includes('delete lesson')" href="#" class="dropdown-item text-danger" @click="destroy(element.id)">
                                                    Delete
                                                </a>
                                            </div>
                                        </div>
                                        <span class="accordion__toggle-icon material-icons" :class="{'text-muted': element.published==0}">keyboard_arrow_down</span>
                                    </a>
                                    <div class="accordion__menu collapse" :id="'course-toc-'+element.id" style="">
                                        <div class="accordion__menu-link">
                                            <p class="text-70 mb-24pt">{{ element.short_text }}</p>
                                        </div>
                                        <div v-for="attach in element.attachement" >
                                            <div class="accordion__menu-link">
                                                <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                                    <i class="material-icons icon-16pt">attach_file</i>
                                                </span>
                                                <a class="flex" download :href="'/storage/attachement/lessons/'+attach.attachement">{{ attach.attachement }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </li>
                            </template>
                        </draggable>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card" v-if="auth.permission.includes('create lesson')">
                        <div class="card-body">
                            <label class="form-label">Featured Image</label>
                            <div class="custom-file mb-3">
                                <input type="file"
                                       @change="uploadImg"
                                       class="custom-file-input" :class="{ 'is-invalid': form.errors.thumbnail }">
                                <div class="invalid-feedback">{{ form.errors.thumbnail }}</div>
                                <label for="file"
                                       class="custom-file-label">{{ previewTitle }}</label>
                            </div>
                            <div class="js-image mb-4 containers" data-position="center" data-height="auto" :style="'display: block; position: relative; overflow: hidden; background-image: url('+previewImage+'); background-size: cover; background-position: center center; height: 168px;'">
                                <img :src="previewImage" class="uploading-image" style="visibility: hidden;"/>
                                <a hre="#" @click="removeImg" v-if="subjects.thumbnail && previewImage!='/images/blank.webp'">
                                    <div class="overlays">
                                        <i class="material-icons">delete</i>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="list-group list-group-flush text-center">
                                <div class="list-group-item ">
                                    <button class="btn btn-primary" @click="publish" type="button" ref="publishTxt" :disabled="form.processing" :class="{ ' is-loading': form.processing}" v-text="props.subjects.thumbnail ? 'Save Changes' : 'Upload'"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-separator">
                        <div class="page-separator__text">Conference Link</div>
                    </div>
                    <div v-if="meeting" class="card js-overlay card-sm overlay--primary-dodger-blue stack stack--1 mb-32pt" data-toggle="popover" data-trigger="click">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <div class="flex">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded mr-12pt z-0 o-hidden">
                                            <div class="overlay">
                                                <a href="javascript:void(0);" class="avatar avatar-sm">
                                                    <img src='/images/logo_meet_2020q4_192px.svg' alt="avatar" class="avatar-img rounded">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <a :href="meeting.link" target="_blank" class="ml-4pt btn btn-custom text-white">Join with Google Meet</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-soft-warning mb-24pt" v-if="!meeting">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <span>No created link yet!.</span><br>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="page-separator">
                        <div class="page-separator__text">Announcements</div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center mb-16pt">
                                <div class="flex">
                                    <strong class="card-title">Latest Announcements</strong>
                                </div>
                            </div>
                            <div class="alert alert-soft-warning mb-24pt" v-if="!announcement.data.length">
                                <div class="d-flex flex-wrap align-items-start">
                                    <div class="mr-8pt">
                                        <i class="material-icons">access_time</i>
                                    </div>
                                    <div class="flex" style="min-width: 180px">
                                        <small class="text-black-100">
                                            <span>No announcement found.</span><br>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group list-group-flush" v-for="announce in announcement.data">
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
                        <div class="card-footer">
                            <ListPagination :links="announcement.links"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import draggable from "vuedraggable";
export default {
    components: {
        draggable
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                group: "lesson",
            };
        }
    }
}
</script>
<script setup>
import CourseHeader from '../../Shared/CourseHeader.vue';
import {loadScript} from "vue-plugin-load-script";
import {onMounted, ref, watch} from "vue";
import {useForm} from "@inertiajs/inertia-vue3";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import ListPagination from "../../Shared/ListPagination";

loadScript('/js/sidebar-mini.js');
loadScript('/js/app.js');
loadScript('/js/script.js');
loadScript('/js/preloader.js');

let props = defineProps({
    errors: Object,
    subjects: Object,
    flash: Object,
    user: Object,
    auth: Object,
    section: Object,
    announcement: Object,
    meeting: Object,
})

let msg = ref(false);

let img = null;
let form = useForm({
    thumbnail: "",
    oldThumb: "",
})

let lessForm = useForm({
    position: [],
    term:[],
    id:[],
})

let startForm = useForm({
    subject: null,
    term: null,
});

function loadContinue(subject, term){
    startForm.subject = subject;
    startForm.term = term;

    console.log(term)

    //startForm.post('/lesson/'+subject+'/getActive');

}

function loadStart(subject, term){
    startForm.subject = subject;
    startForm.term = term;
    startForm.post('/lesson/'+subject+'/start');
}

let previewImage = ref("/images/blank.webp");
let previewTitle = ref("Browse File");

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

if(props.subjects.thumbnail){
    previewImage.value = '/storage/'+props.subjects.thumbnail;
    previewTitle.value = props.subjects.thumbnail.replace('subject/','');
}

function log(event){
    console.log(event);
}

function onAdd(event,term){
   if(term==1){
       props.subjects.lesson_mid.map((lesson, index) =>{
           lesson.term = term;
           lesson.position = index + 1
       });
       lessForm.position = props.subjects.lesson_mid;

       console.log(lessForm.position)
       lessForm.put('/lesson/sort',{
           preserveScroll:true,
           onSuccess:()=>{
               lessForm.reset();
           }
       });
   }else{
       props.subjects.lesson_fin.map((lesson, index) =>{
           lesson.term = term;
           lesson.position = index + 1
       });
       lessForm.position = props.subjects.lesson_fin;

       console.log(lessForm.position)
       lessForm.put('/lesson/sort',{
           preserveScroll:true,
           onSuccess:()=>{
               lessForm.reset();
           }
       });
   }

}

function midMove(event){
    props.subjects.lesson_mid.map((lesson, index) =>{
        console.log(index+1);
        lesson.position = index + 1
    })
    lessForm.position = props.subjects.lesson_mid;

    lessForm.put('/lesson/sort',{
        preserveScroll:true,
        onSuccess:()=>{
            lessForm.reset();
        }
    });
}

function finMove(event){
    props.subjects.lesson_fin.map((lesson, index) =>{
        lesson.position = index + 1
    })
    lessForm.position = props.subjects.lesson_fin;

    lessForm.put('/lesson/sort',{
        preserveScroll:true,
        onSuccess:()=>{
            lessForm.reset();
        }
    });
}

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
            form.delete('/lesson/'+id,{
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


function uploadImg(e){
    const image = e.target.files[0];
    const reader = new FileReader();
    reader.readAsDataURL(image);
    reader.onload = e => {

        previewImage.value = e.target.result;
        previewTitle.value = image.name;

    }

    form.thumbnail = image;
}

function removeImg(){
    form.oldThumb = props.subjects.thumbnail;
    previewImage.value = "/images/blank.webp";
    previewTitle.value = "Browse File";
    form.thumbnail = null
}

function publish(){
    form.post('/subject/'+props.subjects.id+'/update',{
        preserveScroll: true,
        onSuccess:() => {
            msg.value = props.flash.message;
            setTimeout(() => {
                msg.value = false;
            },10000)
        }

    });
}

</script>

<style scoped>
.sortable-drag{
    opacity: 0;
}
.flip-list-move {
    transition: transform 0.5s;
}

.no-move {
    transition: transform 0s;
}

.linkData a{
    color:#5567ff;
}
[dir] .list-group{
    box-shadow: none;
}

[dir] .card, [dir] .card-nav .tab-content {
    box-shadow: none;
}

[dir] .card, [dir] .card-nav .tab-content {
    border: none;
}

.btn-custom{
    background: #1b6ddd!important;
}
</style>

