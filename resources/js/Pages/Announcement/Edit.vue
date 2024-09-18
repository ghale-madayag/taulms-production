<template>
    <Head>
        <title>Edit Announcement</title>
    </Head>
    <Breadcrumbs pageTitle="Edit Announcement" parentTitle="Announcement"/>
    <div class="container page__container">
        <CourseHeader :subjects="announcement.subject" :section="announcement.section" :auth="auth" :user="announcement.user"/>
        <div class="page-section">
            <Alert :msg="msg"/>
            <form @submit.prevent="" class="row">
                <div class="col-md-8">
                    <div class="page-separator">
                        <div class="page-separator__text">Basic information</div>
                    </div>
                    <label class="form-label">Title</label>
                    <div class="form-group mb-24pt">
                        <input v-model="form.title" type="text" :class="{ 'is-invalid': form.errors.title }" class="form-control form-control-lg" placeholder="">
                        <div class="invalid-feedback">{{ form.errors.title }}</div>
                    </div>

                    <div class="form-group mb-32pt">
                        <label class="form-label">Description</label>
                        <quill-editor ref="myEditor" style="background-color: white; height: 300px;"
                                    :toolbar="[ { 'header': [1, 2, 3, 4, 5, 6, false] }, 'bold', 'italic', 'underline', 'strike', 'link',{ 'list': 'ordered'}, { 'list': 'bullet' }, ]"
                                    v-model:content="form.full_text" contentType="html" them="snow" required></quill-editor>
                        <small class="form-text text-muted">Please put the content.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="page-separator">
                        <div class="page-separator__text">Other Details</div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="list-group list-group-flush text-center">
                                <div class="list-group-item">
                                    <button class="btn btn-primary" @click="publish" :disabled="form.processing" :class="{ 'is-loading': publishBtn}" type="button" >Save Changes</button>
                                </div>
                                <div v-if="auth.permission.includes('delete announcement')" class="list-group-item">
                                    <button @click="destroy(announcement.id)" class="btn btn-link text-danger">Delete</button>
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
import Breadcrumbs from "../Components/Breadcrumbs";
import CourseHeader from '../../Shared/CourseHeader';
import {useForm} from "@inertiajs/inertia-vue3";
import Select2 from 'vue3-select2-component';
import "/vendor/select2/select2.min.css";
import "/css/select2.css";
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {onMounted, ref} from "vue";
import Alert from "../../Shared/Alert";

import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

let msg = ref(false);
let publishBtn = ref(false);

let props = defineProps({
    auth:Object,
    errors: Object,
    flash: Object,
    announcement:Object,
});

if(props.flash.message){
    msg.value = props.flash.message;
    setTimeout(() => {
        msg.value = false;
    },10000)
}

let form = useForm('CreateLesson',{
    title: props.announcement.title,
    full_text: props.announcement.full_text,
    subject_id: props.announcement.subject.id,
    section:props.announcement.section.id,
    section_id:props.announcement.section.id,
});

let formSection = useForm();

function publish(){
    form.post('/announcement/'+props.announcement.id+'/update',{
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

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})


function destroy(id) {
    console.log(id)
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

<style scoped>

</style>
