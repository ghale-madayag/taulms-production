<template>
    <Head>
        <title>Change Password</title>
    </Head>
    <div class="container page__container">
        <div class="page-section d-flex flex-column flex-sm-row align-items-sm-center">
            <div class="flex">
                <Link :href="'/'+url+'/'+user.id+'/edit'" class="h-auto"><i class="material-icons icon--left">keyboard_backspace</i> Back to Profile</Link>
                <h1 class="h2 mb-0">Change Password</h1>
                <p class="text-breadcrumb">Profile Management</p>
            </div>

            <a v-if="!auth.permission.includes('add all email')" href="javascript:void(0);" class="btn btn-outline-secondary flex-column">
                Need Help?
                <span class="btn__secondary-text">Contact the Admin</span>
            </a>
        </div>
        <div class="page-section">
            <div class="col-lg-6 p-0">
                <div class="alert alert-primary" role="alert" v-if="msg">
                    <div class="d-flex flex-wrap align-items-start">
                        <div class="mr-8pt">
                            <i class="material-icons">done</i>
                        </div>
                        <div class="flex" style="min-width: 180px">
                            <small class="text-black-100">
                                <strong>Success!</strong> You've successfuly update your password.
                            </small>
                        </div>
                    </div>
                </div>
                <form @submit.prevent="submit">
                    <div class="form-group">
                        <label class="form-label" for="current_password">Current password</label>
                        <input v-model="form.current_password" type="password" class="form-control" :class="{ 'is-invalid': errors.current_password }" placeholder="Current Password ..." id="current_password" name="current_password" required>
                        <div class="invalid-feedback">Invalid Current Password</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">New password</label>
                        <input v-model="form.password" type="password" class="form-control" :class="{ 'is-invalid': errors.password }" placeholder="Password ..." id="password" name="password" required>
                        <div class="invalid-feedback">
                            <ul>
                                <li>The password must be at least 6 characters in length</li>
                                <li>Must contain at least one lowercase letter</li>
                                <li>Must contain at least one uppercase letter</li>
                                <li>Must contain at least one digit</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation ">Confirm password</label>
                        <input v-model="form.password_confirmation" type="password" class="form-control" :class="{ 'is-invalid': errors.password_confirmation }"  placeholder="Confirm password ..." id="password_confirmation" name="password_confirmation"  required>
                        <div class="invalid-feedback">{{ errors.password_confirmation }}</div>
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing"><div class="loader loader-sm loader-primary" style="margin-right:10px;" v-if="form.processing"></div> Save Password</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/inertia-vue3';
import {loadScript} from "vue-plugin-load-script";

loadScript('/js/sidebar-mini.js');
loadScript('/js/app.js');
loadScript('/js/script.js');
loadScript('/js/preloader.js');

let msg = ref(false);
let props = defineProps({
    user: Object,
    auth:Object,
    errors: Object,
})
let form = useForm({
    email: props.user.email,
    current_password:'',
    password:'',
    password_confirmation:'',
});
let url = '';

if(props.auth.url=='student'){
    url = 'student';
}else{
    url = 'faculty';
}

let submit = () => {
    form.post('/reset_password_with_token', {
        preserveScroll: true,
        onSuccess: () => {
            msg.value = true;
            form.reset();
            setTimeout(() => {
                msg.value = false;
            },10000)
        }
    });
}
</script>

<style scoped>

</style>
