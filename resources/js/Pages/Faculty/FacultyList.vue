<template>
    <Head>
        <title>Faculty List</title>
    </Head>
    <Breadcrumbs pageTitle="Faculty"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <div class="row card-group-row mb-lg-8pt">
                <div class="col-lg-4 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-6">
                                <div class="card-body">
                                    <h6 class="text-50">Overview</h6>

                                    <div class="h2 mb-0">{{ total }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Total Faculty</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="alert alert-warning" role="alert">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt"><i class="material-icons">access_time</i></div>
                            <div class="flex" style="min-width: 180px;">
                                <small class="text-black-100"><strong>Important Notice: </strong> 
                                    Please add  email address and verify before attempting to reset password. We need email address to send the instructions on how to reset the password. Once you have entered the email address, click the <strong>"Reset Password"</strong> button to proceed
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="page-separator">
                        <div class="page-separator__text">Faculty List</div>
                    </div>
                    <AlertError v-if="!result" :msg="msg"/>
                    <Alert v-if="result" :msg="msg"/>
                    <div class="card mb-0">
                        <div class="card-header">
                            <form class="form-inline">
                                <label class="mr-sm-2 form-label"
                                    for="inlineFormFilterBy">Filter by:</label>
                                <input v-model="search" type="text"
                                    class="form-control search mb-2 mr-sm-2 mb-sm-0"
                                    id="inlineFormFilterBy"
                                    placeholder="Search ...">

                                <label class="sr-only"
                                    for="inlineFormRole">Role</label>
                                <select v-model="roles" id="inlineFormRole"
                                        class="custom-select mb-2 mr-sm-2 mb-sm-0" style="text-transform: capitalize;">
                                        <option value="0" selected="selected">All Roles</option>
                                        <option v-for="role in select_roles" :value="role"> {{ role }}</option>
                                </select>
                            </form>
                        </div>
                        <div class="list-group list-group-flush">
                            <div v-for="user in faculty.data" class="list-group-item d-flex flex-column flex-sm-row align-items-sm-center px-12pt">
                                <div class="media flex d-flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                    <div class="avatar avatar-sm mr-8pt">
                                        <div class="avatar avatar-sm mr-8pt">
                                            <span class="avatar-title rounded-circle">{{ user.fl }}</span>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div class="d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-employee-name">{{ user.full_name }}</strong></p>
                                            <small class="js-lists-values-employee-email text-50">{{ user.id }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex">
                                        <Link :href="'/faculty/'+user.id+'/email'" class="btn btn-outline-secondary btn-sm btn-rounded mr-1" v-html="user.email ? 'Change Email' : 'Add Email'"></Link>
                                        <button v-if="user.email && user.email_verified_at" type="button" @click="resetPassword(user.email)" class="btn btn-link btn-sm" :disabled="form.processing" :class="{ 'is-loading': publishBtn}">Reset Password</button>
                                        <span v-if="user.email && !user.email_verified_at" class="badge badge-pill badge-warning">Not Verified</span>
                                    </div>
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <Link v-if="auth.permission.includes('view all subject')" :href="'/subject/all?faculty='+user.id" class="dropdown-item">Course</Link>
                                            <Link v-if="auth.permission.includes('view all lesson')" :href="'/lesson/all?faculty='+user.id" class="dropdown-item">Lesson</Link>
                                            <Link v-if="auth.permission.includes('create all quiz')" :href="'/quiz/all?faculty='+user.id" class="dropdown-item">Quiz</Link>
                                            <!-- <a href="javascript:void(0)" class="dropdown-item">Announcement</a>
                                            <a href="javascript:void(0)" class="dropdown-item">Conference</a> -->
                                            <div class="dropdown-divider"></div>
                                            <!-- <a href="javascript:void(0)" class="dropdown-item">Profile</a> -->
                                            <Link v-if="auth.permission.includes('view all time records')" :href="'/recorder/'+user.id" class="dropdown-item">Time</Link>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div v-if="faculty.data == 0" class="list-group-item">
                                <p class="mb-0 text-center"><strong class="text-50">No Data Found</strong></p>
                            </div>
                        </div>
                        <div class="card-footer p-8pt d-inline-block">
                            <ul class="pagination justify-content-start pagination-xsm m-0">
                                <li v-for="link in faculty.links" class="page-item" :class="link.url ? '' : 'disabled'">
                                    <Component :is="link.url ? 'Link' : 'span'" :class="link.active ? 'text-white bg-primary' : ''" class="page-link" :href="link.url" preserve-scroll>
                                        <span v-html="link.label"></span>
                                    </Component>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Card-->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Alert from "../../Shared/Alert.vue";
import AlertError from "../../Shared/AlertError.vue";
import Breadcrumbs from '../Components/Breadcrumbs';
import ListCard from '../../Shared/FacultyListCard';
import ListPagination from '../../Shared/ListPagination';
import {ref,watch} from "vue";
import  debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";
import {loadScript} from "vue-plugin-load-script";
import { useForm } from '@inertiajs/inertia-vue3';

loadScript('/js/sidebar-mini.js');
loadScript('/js/app.js');
loadScript('/js/script.js');
loadScript('/js/preloader.js');

let props = defineProps({
    auth: Object,
    faculty:Object,
    filters:Object,
    select_roles: Object,
    total:Number,
})
let search = ref(props.filters.search);
let roles = ref(props.filters.roles);
let result = ref(false);
let msg = ref(false);
let publishBtn = ref(false);

watch([search, roles], debounce(function (value){
    Inertia.get(
        '/faculty',
        {search: value[0],roles: value[1]},
        {
            preserveState: true,
            replace: true
        })
}, 500))

let form = useForm({
    email: '',
});

function resetPassword(email){
    form.email = email;
    form.post('/reset_password_without_token',{
        preserveScroll: true,
        onStart: () => { publishBtn.value = true },
        onFinish: () => { publishBtn.value = false},
        onSuccess: page => {
            result.value = true;
            msg.value = 'Reset password link has been sent to the user email address';
            setTimeout(() => {
                msg.value = false;
            },10000)
        },
        onError: errors => {
            result.value = false;
            msg.value = 'There was an error. Please try again later';
            setTimeout(() => {
                msg.value = false;
            },10000)
        },
    });
}
</script>

