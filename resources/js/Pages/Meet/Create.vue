<template>
    <Head>
        <title>Create Video Conference</title>
    </Head>
    <Breadcrumbs pageTitle="Conference"/>
    <div class="container page__container">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
        <div class="page-section">
            <form @submit.prevent="" class="row">
                <div v-if="auth.permission.includes('create conference')" class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <AlertError v-if="flash.error || msg" :msg="msg"/>
                            <Alert v-if="flash.success" :msg="msg"/>
                            <div class="form-group mb-3">
                                <label class="form-label">Link</label>
                                <input class="form-control" type="text" v-model="form.link" />
                            </div>
                        </div>
                        <div class="card-footer text-left">
                            <div class="button-list">
                                <button class="btn btn-primary" @click="publish" :disabled="form.processing" :class="{ 'is-loading': publishBtn}" type="button">Create</button>
                                <a href="https://meet.google.com/" target="_blank" type="button" class="btn btn-link">
                                    <i class="material-icons icon--left">launch</i> Go to Meet
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group mb-24pt">
                        <label class="form-label">Start Date</label>
                        <Datepicker :class="{ 'is-invalid': form.errors.startDate }" placeholder="Select Date & Time" v-model="form.startDate" :minDate="new Date()" utc="preserve"></Datepicker>
                        <div class="invalid-feedback">{{ form.errors.title }}</div>
                    </div>
                    <div class="form-group mb-24pt">
                        <label class="form-label">End Date</label>
                        <Datepicker :class="{ 'is-invalid': form.errors.endDate }" placeholder="Select Date & Time" v-model="form.endDate" :minDate="new Date()" utc="preserve"></Datepicker>
                        <div class="invalid-feedback">{{ form.errors.title }}</div>
                    </div> -->
                </div>
                <div class="col-md-8">
                    <div class="alert alert-warning" role="alert">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <strong>Important Notice: </strong> There is a only single link per class. If you wish to update the link, please delete the existing one first before adding a new one. Thank you for your cooperation.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div v-for="meeting in meet.data">
                        <div class="card js-overlay card-sm overlay--primary-dodger-blue stack stack--1 mb-16pt" data-toggle="popover" data-trigger="click">
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
                                                <small class="card-title">{{ meeting.section.name }}</small>
                                                <p class="flex text-50 lh-1 mb-0">
                                                    <small>{{ meeting.subject.title }}</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <a v-if="auth.permission.includes('delete conference')" href="javascript:void(0);" @click="destroy(meeting.id)" class="ml-4pt btn btn-sm btn-link text-secondary">Delete</a>
                                    <a :href="meeting.link" target="_blank" class="ml-4pt btn btn-sm btn-custom text-white">Join</a>
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
import Alert from "../../Shared/Alert.vue";
import AlertError from "../../Shared/AlertError.vue";
import {useForm} from "@inertiajs/inertia-vue3";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import Select2 from 'vue3-select2-component';
import "/vendor/select2/select2.min.css";
import "/css/select2.css";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import {ref} from "vue";

let publishBtn = ref(false);
let msg = ref(false);

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

let props = defineProps({
    auth: Object,
    subjects: Object,
    section: Object,
    meet: Object,
    errors: Object,
    flash: Object,
    user: Object,
});

if(props.flash.error){
    msg.value = props.flash.error;

    setTimeout(() => {
        msg.value = false;
    },10000)
}else{
    msg.value = props.flash.success;

    setTimeout(() => {
        msg.value = false;
    },10000)
}


let form = useForm({
    subject_id: props.subjects.id,
    section:'',
    section_id:props.section.id,
    link:'',
    user_id: props.user.id
});

function destroy(id){
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
            form.delete('/meet/'+id,{
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

function publish(){

    if(isValidUrl(form.link)){
         form.post('/meet/store',{
            onStart: () => { publishBtn.value = true },
            onFinish: () => { publishBtn.value = false},
            onSuccess:() => {
            
                if(props.flash.success){
                    msg.value = props.flash.success;
                }else{
                    msg.value = props.flash.error;
                }
                setTimeout(() => {
                    msg.value = false;
                },10000)
            }
        });
    }else{
        msg.value = 'Invalid link.'

        setTimeout(() => {
            msg.value = false;
        },10000)
    }

   
}

function isValidUrl(url) {
    const meetPattern = /^https:\/\/meet\.google\.com\/[\w-]{10,}$/;
    return meetPattern.test(url);
}

</script>

<style>
.btn-custom{
    background: #1b6ddd!important;
}
</style>