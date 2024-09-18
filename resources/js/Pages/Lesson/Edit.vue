<template>
    <Head>
        <title>Edit Lesson</title>
    </Head>
    <Breadcrumbs pageTitle="Edit Lesson" parentTitle="Lesson" :auth="auth.permission"/>
    <div class="container page__container mt-4">
        <CourseHeader :subjects="lesson.subject" :section="lesson.section" :auth="auth" :user="lesson.user"/>
        <div class="page-section">
            <Alert :msg="msg"/>
            <div class="row">
                <div class="col-md-12 mb-32pt">
                    <Link v-if="auth.permission.includes('create all lesson')" :href="'/lesson/'+lesson.subject.slug+'/'+lesson.section_id+'/'+auth.id+'/create'" :preserve-scroll="true" class="btn bg-primary text-white"><i class="material-icons">add</i> Create New</Link>
                    <Link v-else-if="auth.permission.includes('create lesson')"  :href="'/lesson/'+lesson.subject.slug+'/'+lesson.section_id+'/create'" :preserve-scroll="true" class="btn bg-primary text-white"><i class="material-icons">add</i> Create New</Link>
                </div>
            </div>
           
            <form @submit.prevent="" class="row">
                <div class="col-md-8">
                    <div class="page-separator">
                        <div class="page-separator__text">Basic information</div>
                    </div>
                    <label class="form-label">Lesson title</label>
                    <div class="form-group mb-24pt">
                        <input v-model="form.title" type="text" :class="{ 'is-invalid': form.errors.title }" class="form-control form-control-lg" placeholder="">
                        <div class="invalid-feedback">{{ form.errors.title }}</div>
                    </div>

                    <div class="form-group mb-32pt">
                        <label class="form-label">Short Description</label>
                        <textarea class="form-control" rows="3" v-model="form.short_text" :class="{ 'is-invalid': form.errors.short_text }"></textarea>
                        <div class="invalid-feedback">The short description field is required.</div>
                    </div>

                    <div class="form-group mb-32pt">
                        <label class="form-label">Description</label>
                        <!-- <textarea class="form-control" rows="3" placeholder="Course description"></textarea> -->
                        <quill-editor ref="myEditor" style="background-color: white; height: 600px;"
                                    :toolbar="[ { 'header': [1, 2, 3, 4, 5, 6, false] }, 'bold', 'italic', 'underline', 'strike', 'link', 'image', 'video',{ 'list': 'ordered'}, { 'list': 'bullet' }, ]"
                                    v-model:content="form.full_text" contentType="html" them="snow" required></quill-editor>
                        <small class="form-text text-muted">Please put the content of this Lessons.</small>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="page-separator">
                        <div class="page-separator__text">Other Details</div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <!-- <div class="form-group mb-3">
                                <label class="form-label">Subject Title</label>
                                <input type="hidden" v-model="form.subject_id">
                                <Select2 v-model="form.subject_id" :options="subjects" @select="getSection($event)"/>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Section</label>
                                <input type="hidden" v-model="form.section">
                                <select class="form-control custom-select" @change="onChangeSite($event)" :class="{ 'is-invalid': form.errors.section_id }">
                                    <option v-for="sec in form.section" :value="sec.id" >{{ sec.text }}</option>
                                </select>
                                <div class="invalid-feedback">{{ form.errors.section_id }}</div>
                            </div> -->
                            <div class="form-group mb-3">
                                <label class="form-label">Select Term</label>
                                <select class="form-control custom-select" v-model="form.term" :class="{ 'is-invalid': form.errors.term }">
                                    <option value="1">Midterm</option>
                                    <option value="2">Finals</option>
                                </select>
                                <div class="invalid-feedback">{{ form.errors.term }}</div>
                            </div>
                            <div class="form-group mb-4">
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
                                    <a hre="#" @click="removeImg" v-if="lesson.thumbnail && previewImage!='/images/blank.webp'">
                                        <div class="overlays">
                                            <i class="material-icons">delete</i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Attach File</label>
                                <div class="row">
                                    <div class="col-md-12">

                                        <!--dropzone-->
                                        <div ref="dropRef" id="drop" class="dropzone custom-dropzone"></div>
                                        <div class="dropzone preview-container" ref="dropPreview"></div>
                                        <div class="invalid-feedback">The file format is invalid.</div>
                                        <!--end of dropzone-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="list-group list-group-flush text-center">
                                <div class="list-group-item">
                                    <button class="btn btn-primary" @click="publish" :disabled="form.processing" :class="{ 'is-loading': publishBtn}" type="button" v-text="props.lesson.published == '0' ? 'Publish' : 'Save Changes'"></button>
                                </div>
                                <div class="list-group-item">
                                    <button class="btn btn-link" @click="draft" type="button" :disabled="form.processing" :class="{ ' is-loading': draftBtn}" v-text="props.lesson.published == '1' ? 'Draft' : 'Save Draft'"></button>
                                </div>
                                <div v-if="auth.permission.includes('delete lesson')" class="list-group-item">
                                    <button @click="destroy(lesson.id)" class="btn btn-link text-danger">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import CourseHeader from '../../Shared/CourseHeader';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {useForm} from "@inertiajs/inertia-vue3";
import {ref, onMounted} from "vue";
import Dropzone from "dropzone";
import Alert from "../../Shared/Alert.vue";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import Select2 from 'vue3-select2-component';
import "/vendor/select2/select2.min.css";
import "/css/select2.css";

let props = defineProps({
    auth: Object,
    flash: Object,
    errors: Object,
    lesson: Object,
});

let previewImage = ref("/images/blank.webp");
let previewTitle = ref("Browse File");
const dropRef = ref(null);
let dropPreview = ref();
let img = null;
let msg = ref(false);

let publishBtn = ref(false);
let draftBtn = ref(false);
let myEditor = ref();
let mockFile = [];
let name = "";
let id = "";

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

let form = useForm({
    title: props.lesson.title,
    short_text: props.lesson.short_text,
    full_text: props.lesson.full_text || '',
    subject_id: props.lesson.subject_id,
    section:props.section,
    section_id:props.lesson.section_id,
    term: props.lesson.term,
    thumbnail: '',
    attachement: [],
    oldAttachement: [],
    oldThumb:'',
    published: ''
});

if(props.lesson.thumbnail){
    previewImage.value = '/storage/'+props.lesson.thumbnail;
    previewTitle.value = props.lesson.thumbnail.replace('lesson/','');
}

if(props.flash.message){
    msg.value = props.flash.message;

    setTimeout(() => {
        msg.value = false;
    },10000)
}

let formSection = useForm();

function getSection({id}){
    formSection.id = id;
    formSection.post('/lesson/getSection/'+id,{
        preserveScroll:true,
        onSuccess:() => {
            form.section = props.flash.status;
        }
    })
}

function onChangeSite(e){
    var id = e.target.value;
    form.section_id = id;
}

function getEditSection(id){
    formSection.id = id;
    formSection.post('/lesson/getSection/'+id,{
        preserveScroll:true,
        onSuccess:() => {
            form.section = props.flash.status;
        }
    })
}

const customPreview = `
    <ul class="dz-preview dz-preview-multiple list-group list-group-flush mt-16pt">
        <li class="list-group-item">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <div class="avatar avatar-xs">
                        <img src="/images/icon/attached_file.png"
                            class="avatar-img rounded"
                            data-dz-thumbnail>
                    </div>
                </div>
                <div class="col">
                    <div class="font-weight-bold" data-dz-name>...</div>
                </div>
                <div class="col-auto">
                    <a href="#"
                    class="text-muted-light"
                    data-dz-remove>
                        <i class="material-icons">close</i>
                    </a>
                </div>
            </div>
        </li>
    </ul>
    `

onMounted(() => {
    if(dropRef.value !== null) {
        let myDropzone = new Dropzone(dropRef.value, {
            autoProcessQueue : false,
            previewTemplate: customPreview,
            uploadMultiple: true,
            url: 'http://localhost:3011/file/',
            method: 'POST',
            acceptedFiles: "image/jpeg,image/png,image/jpg,.pdf,.docs,.docx",
            previewsContainer: dropRef.value.parentElement.querySelector('.preview-container'),
            init: function () {
                this.on("addedfile", function (file) {
                    form.attachement.push(file);
                });

                this.on("removedfile", function (file) {
                    form.attachement.splice(file, 1);
                    form.oldAttachement.push(file.name);
                });

            }
        })

        if(dropRef.value.querySelector('.dz-default')) {
            dropRef.value.querySelector('.dz-default').innerHTML = `
            <div style="display: flex; justify-content: center;">
                <svg width="10em" height="10em" viewBox="0 0 16 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="pointer-events: none;">
                <path fill-rule="evenodd" d="m 8.0274054,0.49415269 a 5.53,5.53 0 0 0 -3.594,1.34200001 c -0.766,0.66 -1.321,1.52 -1.464,2.383 -1.676,0.37 -2.94199993,1.83 -2.94199993,3.593 0,2.048 1.70799993,3.6820003 3.78099993,3.6820003 h 8.9059996 c 1.815,0 3.313,-1.43 3.313,-3.2270003 0,-1.636 -1.242,-2.969 -2.834,-3.194 -0.243,-2.58 -2.476,-4.57900001 -5.1659996,-4.57900001 z m 2.3539996,5.14600001 -1.9999996,-2 a 0.5,0.5 0 0 0 -0.708,0 l -2,2 a 0.5006316,0.5006316 0 1 0 0.708,0.708 l 1.146,-1.147 v 3.793 a 0.5,0.5 0 0 0 1,0 v -3.793 l 1.146,1.147 a 0.5006316,0.5006316 0 0 0 0.7079996,-0.708 z"/>
                </svg>
            </div>
            <small class="form-text text-muted mb-2"><strong>Drag and drop files to upload</strong></small>
            <button class="dz-button btn btn-outline-secondary" type="button">or select files</button>
            `
        }

        for(let i = 0; i < props.lesson.attachement.length; i++){
            id = props.lesson.attachement[i].id
            name = props.lesson.attachement[i].attachement;
            mockFile.push({id: id, name: name})
        }

        for (let i = 0; i < mockFile.length; i++) {
            myDropzone.options.addedfile.call(myDropzone, mockFile[i]);
        }
    }

    getEditSection(props.lesson.subject_id);
})

function publish(){
    form.published = 1;
    form.post('/lesson/'+props.lesson.id+'/update',{
        preserveScroll: true,
        onStart: () => { publishBtn.value = true },
        onFinish: () => { publishBtn.value = false},
        onSuccess:() => {
            msg.value = props.flash.message;
            setTimeout(() => {
                msg.value = false;
            },10000)
        }
    });
}

function draft(){
    form.published = 0;
    form.post('/lesson/'+props.lesson.id+'/update',{
        preserveScroll: true,
        onStart: () => { draftBtn.value = true },
        onFinish: () => { draftBtn.value = false},
        onSuccess:() => {
            msg.value = props.flash.message;
            setTimeout(() => {
                msg.value = false;
            },10000)
        }
    });
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
            form.delete('/lesson/'+id);
            swalBtn.fire(
                'Deleted!',
                'Your data has been deleted.',
                'success'
            )
        }
    })
}

function removeImg(){
    form.oldThumb = props.lesson.thumbnail.replace('lesson/','');
    previewImage.value = "/images/blank.webp";
    previewTitle.value = "Browse File";
    form.thumbnail = "";
}
</script>

<style>
.select2-container .select2-selection--single .select2-selection__rendered {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    display: list-item;
    flex-wrap: wrap;
}
</style>

