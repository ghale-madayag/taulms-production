<template>
    <Head>
        <title>Reset Password</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.css" rel="stylesheet"/><!-- Preloader -->
    </Head>
    <section class="vh-100 gradient-form bg-white">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-12">
                    <div class="card rounded-3 text-dark shadow-none border-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-lg-8 d-flex align-items-center">
                                <img width="500" src="/images/forgot-password.gif"/>
                            </div>
                            <div class="col-lg-4">
                                <div class="card-body pb-10 pt-5 pr-5 pl-5 shadow-lg">
                                    <div class="text-center">
                                        <h4 class="mt-1 mb-5 pb-1 text-dark">Password Reset</h4>
                                    </div>
                                    <form @submit.prevent="submit">
                                        <div class="alert alert-primary" role="alert" v-if="msg">
                                            <div class="d-flex flex-wrap align-items-start">
                                                <div class="mr-8pt">
                                                    <i class="material-icons">done</i>
                                                </div>
                                                <div class="flex" style="min-width: 180px">
                                                    <small class="text-black-100">
                                                        <strong>Success!</strong> <p>Reset password link has been sent to the your email address</p>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="form.errors.password || form.errors.email" class="alert alert-accent" role="alert">
                                            <div class="d-flex flex-wrap align-items-start">
                                                <div class="mr-8pt">
                                                    <i class="material-icons">cancel</i>
                                                </div>
                                                <div class="flex" style="min-width: 180px">
                                                    <small style="color:#8e1b40 !important;">
                                                        <!--error here-->
                                                        {{ form.errors.password || form.errors.email}}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-muted">Please enter new password</h6>
                                        <div class="form-outline mb-4">
                                            <input type="password" v-model="form.password" :class="form.password ? 'active' : ''" id="password" name="password" class="form-control" />
                                            <label class="form-label text-50" for="password">New Password</label>
                                        </div>
                                        <div class="form-outline mb-4">
                                            <input type="password" v-model="form.password_confirmation" :class="form.password_confirmation ? 'active' : ''" id="password_confirmation" name="password_confirmation" class="form-control" />
                                            <label class="form-label text-50" for="password">Confirm Password</label>
                                        </div>
                                        <div class="text-left pt-1 mb-5 pb-1">
                                            <button class="btn btn-primary btn-block fa-lg mb-3" type="submit" :disabled="form.processing" :class="{'is-loading' : form.processing}">Reset Password</button>
                                            <Link href="/" class="h-auto text-muted small"><i class="material-icons icon--left">keyboard_backspace</i> Back to login</Link>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import {useForm} from "@inertiajs/inertia-vue3";
import {ref, onMounted} from "vue";

let props = defineProps({
    email: String,
    token: String,
})
let msg = ref(false);
let form = useForm({
    email: props.email,
    password: '',
    password_confirmation:'',
    token: props.token,
});

function getToken(token){
    let query = token.split('/')[2];
    let value = query.split('?')[0];

    return value;
}

let submit = () =>{
    form.post('/reset-password',{
        preserveScroll: true,
    });
}


</script>

<style>
.form-outline .form-control.active~.form-label{
    transform: translateY(-1rem) translateY(-0.4rem) scale(.8);
}
</style>
