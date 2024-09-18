<template>
    <Head>
        <title>Update Profile</title>
    </Head>
    <AvatarProfile :profile="user"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="row mb-lg-8pt">
                <div class="col-lg-8 mb-lg-32pt">
                    <div class="page-separator">
                        <div class="page-separator__text">Basic Information</div>
                    </div>
                    <div class="list-group list-group-form">
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-4">First Name</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <div class="flex">{{ user.fname }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-4">Middle Name</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <div class="flex">{{ user.mname }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-4">Last Name</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <div class="flex">{{ user.lname }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-4">Ext. Name</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <div class="flex">{{ user.extname ? user.extname : "N/A" }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-separator mt-32pt">
                        <div class="page-separator__text">Login Details</div>
                    </div>
                    <div class="alert alert-primary" role="alert" v-if="msg">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">done</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    {{ msg }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-soft-warning mb-3 p-8pt" v-if="!user.email && !owner">
                        <div class="d-flex align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">error_outline</i>
                            </div>
                            <div class="flex">
                                <small class="text-100">Note: You need an email address to reset your password</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-form">
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-4">Email Address</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <div class="flex">{{ user.email.toLowerCase() ? user.email.toLowerCase() : "N/A" }}</div>
                                    <Link :href="'/'+url+'/create'" class="btn btn-outline-secondary mr-2" v-if="auth.permission.includes('change email') ||  auth.permission.includes('add email')">Add/Change</Link>
                                    <form @submit.prevent="sendVerification">
                                        <button type="submit" class="btn btn-outline-secondary" v-if="owner && user.email && !user.email_verified_at" :disabled="form.processing" :class="{'is-loading' : form.processing}"><i class="material-icons">refresh</i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-4">Password</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <div class="flex">********</div>
                                    <Link :href="'/'+url+'/password'" class="btn btn-outline-secondary" v-if="owner && auth.permission.includes('reset password')">Change</Link>
                                    <form @submit.prevent="sendEmail">
                                        <button type="submit" class="btn btn-outline-secondary" v-if="!owner && user.email" :disabled="form.processing" :class="{'is-loading' : form.processing}">Send Reset Link</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4" v-if="!auth.permission.includes('add all email')">
                    <div class="alert alert-soft-warning mb-0 p-8pt">
                        <div class="d-flex align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">error_outline</i>
                            </div>
                            <div class="flex">
                                <small class="text-100">Note: You can only change your password and email. If you want to change other information, please contact your administrator.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import AvatarProfile from "../Shared/AvatarProfile";
import { useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import {loadScript} from "vue-plugin-load-script";

loadScript('/js/sidebar-mini.js');
loadScript('/js/app.js');
loadScript('/js/script.js');
loadScript('/js/preloader.js');

let props = defineProps({
    auth:Object,
    user: Object,
    owner:Boolean,
})
let msg = ref(false);

let form = useForm({
    email: props.user.email
});

let url = '';

if(props.auth.url=='student'){
    url = 'student';
}else{
    url = 'faculty';
}

let sendEmail = () =>{
    form.post('/reset_password_without_token', {
        preserveScroll: true,
        onSuccess: () => {
            msg.value = 'Success! Reset password link has been sent to the user.';
            setTimeout(() => {
                msg.value = false;
            },10000)
        }
    });
}

let sendVerification = () =>{
    form.post('/email/verification-notification', {
        preserveScroll: true,
        onSuccess: () => {
            msg.value = 'Success! Please check your email';
            setTimeout(() => {
                msg.value = false;
            },10000)
        }
    });
}
</script>

<style scoped>

</style>
