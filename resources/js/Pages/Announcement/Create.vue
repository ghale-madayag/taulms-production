<template>
    <Head>
        <title>Create Announcement</title>
    </Head>
    <Breadcrumbs pageTitle="Create Announcement" parentTitle="Announcement"/>
    <div class="container page__container">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
        <div class="page-section">
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
                        <div class="page-separator__text">Publish</div>
                    </div>
                    <div class="card">
                        <div class="card-body text-center">
                                <button class="btn btn-primary" @click="publish" type="button">Publish</button>
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

let props = defineProps({
    auth: Object,
    subjects: Object,
    section: Object,
    errors: Object,
    flash: Object,
    user: Object,
});

let form = useForm('CreateLesson',{
    title: '',
    full_text: '',
    subject_id: props.subjects.id,
    section:'',
    section_id:props.section.id,
    user_id: props.user.id,
});

function publish(){
    form.post('/announcement/store',{
        // onStart: () => { publishBtn.value = true },
        // onFinish: () => { publishBtn.value = false},
    });
}
</script>
