<template>
    <Head>
        <title>Create Quiz</title>
    </Head>
    <Breadcrumbs pageTitle="Create Quiz" parentTitle="Quiz"/>
    <div class="container page__container mt-4">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
       <div class="page-section">
            <form @submit.prevent="" class="row">
                <div class="col-md-8">
                    <div class="page-separator">
                        <div class="page-separator__text">Basic information</div>
                    </div>
                    <label class="form-label">Quiz title</label>
                    <div class="form-group mb-24pt">
                        <input v-model="form.title" type="text" class="form-control form-control-lg" :class="{ 'is-invalid': form.errors.title }" placeholder="">
                        <div class="invalid-feedback">{{ form.errors.title }}</div>
                    </div>
                    <div class="form-group mb-32pt">
                        <label class="form-label">Instructions</label>
                        <!-- <textarea class="form-control" rows="3" placeholder="Course description"></textarea> -->
                        <quill-editor ref="myEditor" style="background-color: white; height: 400px;"
                                    :toolbar="[ { 'header': [1, 2, 3, 4, 5, 6, false] }, 'bold', 'italic', 'underline', 'strike', 'link', 'image', 'video',{ 'list': 'ordered'}, { 'list': 'bullet' }, ]"
                                    v-model:content="form.description" contentType="html" them="snow" required></quill-editor>
                        <small class="form-text text-muted">Please put the content of this Quiz.</small>
                    </div>
                    <div class="page-separator">
                        <div class="page-separator__text">Questions</div>
                    </div>
                    <div class="text-danger mb-3" v-if="questionError">The questions field is required.</div>
                    <div class="alert alert-soft-warning mb-lg-32pt" v-if="!model.questions.length">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex">
                                <small class="text-100">
                                    You don't have any questions created
                                </small>
                            </div>
                            <a @click="addQuestion()" class="btn btn-warning mb-24pt mb-sm-0 text-white">Create Now!</a>
                        </div>
                    </div>
                    <div class="alert alert-soft-info mb-lg-32pt">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="mr-8pt">
                                <i class="material-icons">info</i>
                            </div>
                            <div class="flex">
                                <small class="text-100">
                                    By default, all questions are in random order when the quiz starts!
                                </small>
                            </div>
                        </div>
                    </div>
                    <div v-for="(quiz, index) in model.questions">
                        <QuestionEditor
                            :question="quiz"
                            :index="index"
                            @change="questionChange"
                            @addQuestion="addQuestion"
                            @duplicate="duplicate"
                            @deleteQuestion="deleteQuestion"
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="page-separator">
                        <div class="page-separator__text">Other Details</div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="form-label">Lesson Title</label>
                                <input type="hidden" v-model="form.lesson_id">
                                <Multiselect
                                    mode="tags"
                                    v-model="form.lesson_tags"
                                    placeholder="Select Lesson"
                                    :close-on-select="false"
                                    :searchable="true"
                                    :options="trimmedLabels"
                                />
                                <!-- <Select2 placeholder="Please Select Lesson" v-model="form.lesson_id" :options="lesson" :class="{ 'is-invalid': form.errors.lesson_id }"/> -->
                                <div class="invalid-feedback">{{ form.errors.lesson_id }}</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Quiz Schedule</label>
    <!--                            <input type="datetime-local" class="form-control" v-model="form.quiz_schedule">-->
                                <!-- <Datepicker :class="{ 'is-invalid': form.errors.quiz_schedule }" placeholder="Select Date & Time" v-model="form.quiz_schedule" :minDate="new Date()" utc="preserve"></Datepicker> -->
                                <Datepicker :class="{ 'is-invalid': form.errors.quiz_schedule }" placeholder="Select Date & Time Range" v-model="form.quiz_schedule" range multi-calendars multi-calendars-solo utc="preserve"></Datepicker>
                                <div class="invalid-feedback">{{ form.errors.quiz_schedule }}</div>
                            </div>
                            <!-- <div class="form-group">
                                <label class="form-label">Duration</label>
                                <Datepicker placeholder="Select Time Limit" v-model="form.duration" timePicker minutesIncrement="5"></Datepicker>
                            </div> -->
                            <div class="form-grmloup mb-4">
                                <label class="form-label">Featured Image</label>
                                <div class="custom-file mb-3">
                                    <input type="file"
                                        @change="uploadImg"
                                        class="custom-file-input" :class="{ 'is-invalid': form.errors.thumbnail }">
                                    <div class="invalid-feedback">{{ form.errors.thumbnail }}</div>
                                    <label for="file"
                                        class="custom-file-label">{{ previewTitle }}</label>
                                </div>
                                <a class="js-image mb-4" data-position="center" data-height="auto" :style="'display: block; position: relative; overflow: hidden; background-image: url('+previewImage+'); background-size: cover; background-position: center center; height: 168px;'">
                                    <img :src="previewImage" class="uploading-image" style="visibility: hidden;"/>
                                </a>

                            </div>
                            <div class="form-group">
                                <label class="form-label">Exclude Students</label>
                                <Multiselect
                                    mode="tags"
                                    v-model="form.excluded"
                                    placeholder="Select Students"
                                    :close-on-select="false"
                                    :searchable="true"
                                    :options="student"
                                />
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <AlertError :msg="error"/>
                            <div class="button-list">
                                <button class="btn btn-link" @click="publish(0)" type="button" :disabled="form.processing" :class="{ ' is-loading': draftBtn}"><strong>Save Draft</strong></button>
                                <button class="btn btn-primary" @click="publish(1)" :disabled="form.processing" :class="{ 'is-loading': publishBtn}" type="submit">Publish</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
       </div>
    </div>
</template>

<script setup>
import Multiselect from '@vueform/multiselect';
import Breadcrumbs from "../Components/Breadcrumbs.vue";
import CourseHeader from '../../Shared/CourseHeader';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {useForm} from "@inertiajs/inertia-vue3";
import {onMounted, ref} from "vue";
import Dropzone from "dropzone";
// import Select2 from 'vue3-select2-component';
// import "/vendor/select2/select2.min.css";
// import "/css/select2.css";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import QuestionEditor from '../../Shared/QuestionEditor.vue';
import { v4 as uuidv4 } from "uuid";
import AlertError from "../../Shared/AlertError.vue";

let previewImage = ref("/images/blank.webp");
let previewTitle = ref("Browse File");
const dropRef = ref(null);
let dropPreview = ref();
let error = ref(false);

let publishBtn = ref(false);
let draftBtn = ref(false);
let myEditor = ref();
let questionError = ref();

let props = defineProps({
    auth: Object,
    user: Object,
    subjects: Object,
    section: Object,
    selected: String,
    errors: Object,
    lesson: Object,
    student: Object,
});


const trimmedLabels = props.lesson.map(item => {
    return {
      value: item.value,
      label: item.label.slice(0, 20) + '...',
    };
  });

const time = ref({
    hours: 1,
    minutes: 0
});

let model = ref({
    title: "",
    status: false,
    description: null,
    expire_date: null,
    questions: [],
});

let form = useForm('CreateQuiz',{
    title: '',
    description: '',
    lesson_id: '',
    lesson_tags:[],
    excluded: [],
    quiz_schedule:'',
    duration: time,
    published: '',
    thumbnail: '',
    questions: [],
    user_id: props.user.id,
});

function publish($val){
    error.value = false;
    form.published = $val;

    const emptyAnswerQuestion = model.value.questions.find(question => {
        const isNotEssayOrFileUpload = question.type !== "essay" && question.type !== "file upload";
        return isNotEssayOrFileUpload && (!question.answer || (Array.isArray(question.answer) && question.answer.length === 0));
    });

    if (emptyAnswerQuestion) {

        const questionNumber = model.value.questions.indexOf(emptyAnswerQuestion) + 1;
        error.value = `Please fill all the fields in question number ${questionNumber}`;
        return; 

    }

    form.questions.push(...model.value.questions);
    form.post('/quiz/store',{
        onError: () => {
            questionError.value = true;
            setTimeout(() => {
                questionError.value = false;
            },10000)
        },
        onStart: () => {
            publishBtn.value = true
        },
        onFinish: () => {
            publishBtn.value = false;
            form.questions.length = 0;
        },
        onSuccess: ()=> {
            msg.value = true;
            setTimeout(() => {
                msg.value = false;
            },10000)
        },
        preserveState: true,
    });
}

function uploadImg(e){

    const image = e.target.files[0];
    const reader = new FileReader();
    reader.readAsDataURL(image);
    reader.onload = e => {
        previewImage.value = e.target.result;
        previewTitle.value = image.name;
    }

    form.thumbnail = image;
}

function addQuestion(index) {

    const newQuestion = {
        id: uuidv4(),
        type: "text",
        question: "",
        description: null,
        data: {},
        answer: [],
        points: 1,
    };

   model.value.questions.splice(index, 0, newQuestion);
}

function duplicate(index) {

    const modelVal = model.value.questions[index];
    const len = model.value.questions.length;

    const dupQuestion = {
        id: uuidv4(),
        type: modelVal.type,
        question: modelVal.question,
        description: null,
        data: {},
        answer: modelVal.answer,
        points: modelVal.points,
    };
    
    //console.log();

    model.value.questions.splice(len, 0, dupQuestion);
}

function deleteQuestion(question) {
    model.value.questions = model.value.questions.filter((q) => q !== question);
}

function questionChange(questions) {
   
    if(questions.data !==null){
        if (questions.data.options) {
            questions.data.options = [...questions.data.options];
        }
    }

    model.value.questions = model.value.questions.map((q) => {
        if (q.id === questions.id) {
            if (questions.type === "short answer" || questions.type === 'essay') {
                return {
                    ...questions,
                    data: []
                };
            } else {
                return {
                    ...questions,
                    data: {
                        ...q.data,
                        options: questions.data.options
                    }
                };
            }
        }
        return q;
    });

}

</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<style>
span.multiselect-tag {
    font-weight: 400;
}
/* .select2-container .select2-selection--single .select2-selection__rendered {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    display: list-item;
    flex-wrap: wrap;
} */                                                                        
</style>
