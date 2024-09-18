<template>
    <Head>
        <title>Login</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.css" rel="stylesheet"/><!-- Preloader -->
    </Head>
    <section class="vh-100 gradient-form bg-white">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-12">
                    <div class="card rounded-3 text-dark shadow-none border-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-lg-8 d-flex align-items-center">
                                <img src="images/login.gif"/>
                            </div>
                            <div class="col-lg-4">
                                <div class="card-body pb-10 pt-5 pr-5 pl-5 shadow-lg">
                                    <div class="text-center">
                                        <img src="images/tau.png" style="width:100px;" alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1 text-dark">Learning Management System</h4>
                                    </div>
                                    <form @submit.prevent="submit">
                                        <div class="alert alert-primary" role="alert" v-if="msg">
                                            <div class="d-flex flex-wrap align-items-start">
                                                <div class="mr-8pt">
                                                    <i class="material-icons">done</i>
                                                </div>
                                                <div class="flex" style="min-width: 180px">
                                                    <small class="text-black-100">
                                                        <strong>Success!</strong> <p>Your password has been reset!</p>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="form.errors.email" class="alert alert-accent" role="alert">
                                            <div class="d-flex flex-wrap align-items-start">
                                                <div class="mr-8pt">
                                                    <i class="material-icons">cancel</i>
                                                </div>
                                                <div class="flex" style="min-width: 180px">
                                                    <small style="color:#8e1b40 !important;">
                                                        <!--error here-->
                                                        {{ form.errors.email }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-muted">Please login your account</h6>
                                        <div class="form-outline mb-4 mt-4">
                                            <input v-model="form.email" type="text" id="email" class="form-control" :class="form.email ? 'active' : ''" name="email" autofocus autoComplete='none' required/>
                                            <label class="form-label text-50" for="email">Email/Employee/Student No.</label>
                                        </div>
                                        <div class="form-outline mb-4">
                                            <input v-model="form.password" type="password" id="password" name="password" class="form-control" :class="form.password ? 'active' : ''" autocomplete="none" required/>
                                            <label class="form-label text-50" for="password">Password</label>
                                        </div>
                                        <div class="text-center pt-1 mb-5 pb-1">
                                            <button class="btn btn-primary btn-block fa-lg mb-3" type="submit" :disabled="form.processing" :class="{'is-loading' : form.processing}">Login</button>
                                            <Link href="forgot-password" class="text-muted small">Forgot password?</Link>
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
import {ref} from "vue";

let msg = ref(false);

let props = defineProps({
    flash:Object,
})

if(props.flash.status){
    msg.value = props.flash.status;
    setTimeout(() => {
        msg.value = false;
    },10000)
}

let form = useForm({
    email: '',
    password: ''
});
let submit = () =>{
    form.post('login',{
        onSuccess:(page)=>{
            window.location.href = page.url
        }
    });
}
</script>

<style>
.form-outline .form-control.active~.form-label{
    transform: translateY(-1rem) translateY(-0.4rem) scale(.8) !important;
}

@media only screen and (max-width: 600px) {
    .col-lg-8 img {
        width: 340px;
    }
}
</style>
