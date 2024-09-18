<template>
    <Head>
        <title>Email Management</title>
    </Head>
    <div class="container page__container">
        <div class="page-section d-flex flex-column flex-sm-row align-items-sm-center">
            <div class="flex">
                <Link :href="'/'+url+'/'+user.id+'/edit'" class="h-auto"><i class="material-icons icon--left">keyboard_backspace</i> Back to Profile</Link>
                <h1 class="h2 mb-0">Email</h1>
                <p class="text-breadcrumb">Profile Management</p>
            </div>
            <p class="d-sm-none"></p>
            <a v-if="!auth.permission.includes('add all email')" href="javascript:void(0);" class="btn btn-outline-secondary flex-column">
                Need Help?
                <span class="btn__secondary-text">Contact the Admin</span>
            </a>
        </div>
        <div class="page-section p-0">
            <div class="col-lg-6 p-0">
                <div class="alert alert-primary" role="alert" v-if="msg">
                    <div class="d-flex flex-wrap align-items-start">
                        <div class="mr-8pt">
                            <i class="material-icons">done</i>
                        </div>
                        <div class="flex" style="min-width: 180px">
                            <small class="text-black-100">
                                <strong>Success!</strong> You're email was successfully added. Please verify your email
                            </small>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit">
                    <div class="form-group">
                        <label class="form-label" for="email">Please enter your email address</label>
                        <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }">
                        <div class="invalid-feedback">{{ errors.email }}</div>
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing" :class="{'is-loading' : form.processing}">
                        Submit Email
                    </button>
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
    user:Object,
    auth:Object,
    errors: Object,
    flash: Object
})

let form = useForm({
    id: props.user.id,
    email: '',
});

let url = '';

if(props.auth.url=='student'){
    url = 'student';
}else{
    url = 'faculty';
}


let submit = () => {
    form.post('/store',{
            preserveScroll: true,
            onSuccess: () => {
                msg.value = true;
                form.post('/email/verification-notification')
                form.reset();
                setTimeout(() => {
                    msg.value = false;
                },10000)
            }
    })

}

</script>

