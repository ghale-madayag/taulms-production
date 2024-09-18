<template>
    <Head>
        <title>Forgot Password</title>
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
                                        <h4 class="mt-1 mb-5 pb-1 text-dark">Reset Password</h4>
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
                                        <h6 class="text-muted">Please enter your email address</h6>
                                        <div class="form-outline mb-4">
                                            <input v-model="form.email" type="email" id="email" class="form-control" :class="form.email ? 'active' : ''" name="email" autofocus autoComplete='none' required/>
                                            <label class="form-label text-50" for="email">Email Address.</label>
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
import {ref} from "vue";

let msg = ref(false);
let form = useForm({
    email: '',
});

let submit = () =>{
    form.post('/forgot-password ',{
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            msg.value = true;
        }
    });
}
</script>

<style>
.form-outline .form-control.active~.form-label{
    transform: translateY(-1rem) translateY(-0.4rem) scale(.8);
}
</style>

