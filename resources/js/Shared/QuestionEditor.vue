<template>
    <div class="accordion js-accordion accordion--boxed mb-24pt" id="parent">
        <div class="accordion__item open">
            <a href="#"
               class="accordion__toggle collapsed"
               data-toggle="collapse"
               :data-target="'#course-toc-'+question.id"
               data-parent="#parent">
                <div class="d-flex align-items-center page-num-container">
                    <div class="page-num">{{ index + 1 }}.</div>
                </div>
                <span class="flex lead">Question {{ index + 1 }}</span>
                <span class="accordion__toggle-icon material-icons">keyboard_arrow_down</span>
            </a>
            <div class="accordion__menu collapse show"
                 :id="'course-toc-'+question.id">
                <div class="row p-20pt">
                    <div class="col-md-12">
                        <!-- <label class="form-label">Question title</label>
                        <div class="form-group mb-24pt">
                            <input v-model="model.question" @change="dataChange"  type="text" class="form-control form-control-md" placeholder="">
                        </div> -->
                        <div class="form-group mb-24pt">
                            <label class="form-label">Type your question</label>
                            <quill-editor ref="myEditor" style="background-color: white; height: 400px;" :toolbar="[ { 'header': [1, 2, 3, 4, 5, 6, false] }, 'bold', 'italic', 'underline', 'strike', 'link', 'image',{ 'list': 'ordered'}, { 'list': 'bullet' }, ]" v-model:content="model.question" @textChange="dataChange"  contentType="html" them="snow" required></quill-editor>
                            <!-- <textarea class="form-control" rows="3" placeholder="Short Description" v-model="model.description"></textarea> -->
                        </div>
                        <div class="form-group">
                            <label class="form-label">Question Type</label>
                            <select name="question_type" id="question_type" v-model="model.type" @change="typeChange" class="form-control custom-select">
                                <option v-for="types in questionType" :key="types" :value="types">
                                    {{ upperCaseFirst(types) }}
                                </option>
                            </select>
                            <p v-if="model.type == 'file upload'" class="card-subtitle mt-16pt mb-lg-0 text-70">Maximum number of files: 1<br>Maximum file size: 10 MB</p>
                        </div>
                        <div class="form-group" v-if="!shouldHaveOptions() && model.type == 'short answer'">
                            <label class="form-label">Correct Answer:</label>
                            <input v-model="model.answer" @change="dataChange" type="text" class="form-control form-control-md" placeholder="Type the correct answer here...">
                        </div>
                        <div class="form-group" v-if="!shouldHaveOptions()">
                            <label class="form-label">Total Points</label>
                            <input v-model="model.points" @change="dataChange"
                                   type="number"
                                   min="1" max="100"
                                   class="form-control col-md-2"
                            >
                        </div>
                    </div>
                    <!--Data-->
                    <div class="col-md-12" v-if="shouldHaveOptions()" >
                        <div class="d-flex align-items-center form-group">
                            <button @click="addOption()" class="btn btn-outline-secondary mr-12pt ml-auto"><span class="material-icons icon--left">add</span> Add Option</button>
                        </div>
                        <div class="alert alert-warning" role="alert" v-if="!model.data.options">
                            <div class="d-flex flex-wrap align-items-start">
                                <div class="mr-8pt">
                                    <i class="material-icons">error_outline</i>
                                </div>
                                <div class="flex" style="min-width: 180px">
                                    <small class="text-black-100">
                                        You don't have any options defined
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" v-for="(option, index) in model.data.options " :key="option.uuid">
                            <div class="input-group input-group-merge">
                                <input type="text" v-model="option.text" @change="dataChange" class="form-control form-control-prepended" placeholder="Your option here...">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{ index + 1 }}.
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <a href="javascript:;void(0);"  class="input-group-text">
                                        <i class="material-icons text-danger" @click="removeOption(option)">delete</i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-20pt" v-if="model.data.options">
                            <label class="form-label">Select the Correct Answer:</label>
                        </div>

                        <div class="form-group" v-for="(option, index) in model.data.options" :key="option.uuid">
                            <div class="form-group" v-if="question.type != 'multiple answer'">
                                <div class="custom-controls-stacked">
                                    <div class="custom-control custom-radio">
                                        <input :id="option.uuid" :name="question.id" type="radio" class="custom-control-input" :value="option.text" v-model="model.answer" @change="dataChange">
                                        <label :for="option.uuid" class="custom-control-label">{{ option.text }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" v-else-if="question.type === 'multiple answer'">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" :name="question.id" :id="option.uuid" :value="option.text" v-model="model.answer" @change="dataChange">
                                    <label :for="option.uuid" class="custom-control-label">{{ option.text }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Total Points</label>
                            <input v-model="model.points" @change="dataChange"
                                   type="number"
                                   min="1" max="100"
                                   class="form-control col-md-2"
                            >
                        </div>
                    </div>
                    <div class="col-md-12 mt-20pt mb-20pt">
                        <button @click="addQuestion()" class="btn btn-outline-secondary mr-12pt"><span class="material-icons icon--left">add</span> Add Question</button>
                        <button @click="duplicate()" class="btn btn-link"><i class="material-icons icon--left">file_copy</i>Duplicate</button>
                        <button @click="deleteQuestion()" class="btn btn-link text-danger"><i class="material-icons icon--left">delete</i>Delete</button>
                    </div>
                    <!--/Data-->
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import {ref,computed} from 'vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { v4 as uuidv4} from "uuid";

let props = defineProps({
    question: Object,
    index: Number,
})

const questionType = ['short answer','essay','multiple choice','multiple answer','file upload'];

const emit = defineEmits(["change","addQuestion","deleteQuestion","duplicate"]);

const model = ref(JSON.parse(JSON.stringify(props.question)));

const questionTypes = computed(()=>questionType);

if(props.question.type === 'multiple answer'){
    const selectedOptions = [];
    JSON.parse(model.value.answer).forEach(element => {
        selectedOptions.push(element);
    });

    model.value.answer = selectedOptions;
}

function upperCaseFirst(str){
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function shouldHaveOptions(){
    return ['multiple choice', 'multiple answer'].includes(model.value.type);
}

function getOptions() {
    return  model.value.data.options;
}

function setOptions(options) {
    return model.value.data.options = options;
}

function addOption(){
    setOptions([
        ...getOptions(),
        { uuid: uuidv4(), text:""},
    ]);
    dataChange();
}

function removeOption(op){
    setOptions(getOptions().filter((opt) => opt != op));
    model.value.answer = [];
    dataChange();
}

function typeChange() {
    if(shouldHaveOptions()){
        model.value.answer = [];
        setOptions(getOptions() || []);
    }

    dataChange();
}

function dataChange(){
    const data = model.value;
    
    emit("change", data);
}

function addQuestion() {
    emit("addQuestion", props.index + 1);
}

function duplicate(){
    emit('duplicate', props.index);
}

function deleteQuestion() {
    emit("deleteQuestion", props.question);
}


</script>

<style scoped>

</style>
