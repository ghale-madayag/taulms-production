<template>
    <div v-if="auth.role == 'student'">
        <div v-if="question.type === 'multiple choice'">
            <div v-for="(option, ind) in question.data.options" :key="option.uuid">
                <div class="form-group">
                    <div class="custom-control custom-radio">
                        <input :id="option.uuid"
                            :name="'question' + question.data.id"
                            :value="option.text"
                            v-model="currentAns"
                            @change="emits('update:modelValue', $event.target.value)"
                            type="radio"
                            class="custom-control-input">
                        <label :for="option.uuid" class="custom-control-label">{{ option.text }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="question.type === 'multiple answer'">
            <div v-for="(option, ind) in question.data.options" :key="option.uuid">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input :id="option.uuid"
                            v-model="model"
                            :value="option.text"
                            type="checkbox"
                            @change="onCheckboxChange"
                            class="custom-control-input">
                        <label :for="option.uuid" class="custom-control-label">{{ option.text }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8" v-if="question.type === 'select'">
            <div class="form-group input-group-lg">
                <select
                    v-model="currentAns"
                    @change="emits('update:modelValue', $event.target.value)"
                    class="form-control custom-select">
                    <option v-for="option in question.data.options" :key="option.uuid" :value="option.text">
                        {{ option.text }}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-lg-8" v-else-if="question.type === 'short answer'">
            <div class="form-group input-group-lg">
                <input
                    type="text"
                    v-model="currentAns"
                    @input="emits('update:modelValue', $event.target.value)"
                    class="form-control"
                    placeholder="Please type your answer here..."
                />
            </div>
        </div>
        <div class="col-lg-12" v-else-if="question.type === 'essay'">
            <div class="form-group input-group-lg">
            <textarea rows="6"
                    v-model="currentAns"
                    @input="emits('update:modelValue', $event.target.value)"
                    class="form-control"
                    placeholder="Please type your answer here..."
            ></textarea>
            </div>
        </div>
        <div class="col-lg-12" v-else-if="question.type === 'file upload'">
            <div class="form-group input-group-lg">
                <div class="custom-file mb-3">
                    <div ref="dropRef" id="drop" class="dropzone custom-dropzone"></div>
                    <div class="dropzone preview-container" ref="dropPreview"></div>
                    <div class="invalid-feedback">The file format is invalid.</div>
                </div>
                <!-- <a class="js-image mb-4" data-position="center" data-height="auto" :style="'display: block; position: relative; overflow: hidden; background-image: url('+previewImage+'); background-size: cover; background-position: center center; height: 168px;'">
                    <img :src="previewImage" class="uploading-image" style="visibility: hidden;"/>
                </a> -->
            </div>
        </div>
    </div>
    <div v-else>
        <div v-if="question.type === 'multiple choice'">
            <div v-for="(option, ind) in question.data.options" :key="option.uuid">
                <div class="form-group">
                    <div class="custom-control custom-radio">
                        <input :id="option.uuid"
                            :name="'question' + question.data.id"
                            :value="option.text"
                            type="radio"
                            class="custom-control-input" disabled="disabled">
                        <label :for="option.uuid" class="custom-control-label">{{ option.text }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="question.type === 'multiple answer'">
            <div v-for="(option, ind) in question.data.options" :key="option.uuid">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input :id="option.uuid"
                            :value="option.text"
                            type="checkbox"
                            class="custom-control-input" disabled="disabled">
                        <label :for="option.uuid" class="custom-control-label">{{ option.text }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8" v-if="question.type === 'select'">
            <div class="form-group input-group-lg">
                <select
                    class="form-control custom-select">
                    <option v-for="option in question.data.options" :key="option.uuid" :value="option.text">
                        {{ option.text }}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-lg-8" v-else-if="question.type === 'short answer'">
            <div class="form-group input-group-lg">
                <input
                    type="text"
                    class="form-control"
                    placeholder="Please type your answer here..."
                    disabled="disabled"
                />
            </div>
        </div>
        <div class="col-lg-12" v-else-if="question.type === 'essay'">
            <div class="form-group input-group-lg">
            <textarea rows="6"
                    class="form-control"
                    placeholder="Please type your answer here..."
            ></textarea>
            </div>
        </div>
    </div>
</template>

<script setup>
import Dropzone from "dropzone";
import {ref, onMounted, watch} from "vue";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const dropRef = ref(null);
let dropPreview = ref();
let img = null;

let model;
let currentAns = ref();
let mockFile = [];
let name = "";
let id = "";

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

const { question, index, modelValue, answer } = defineProps({
    auth:Object,
    question: Object,
    index: Number,
    modelValue: [String, Array],
    answer: Object,
});

const emits = defineEmits(["update:modelValue"]);

const customPreview = `
    <ul class="dz-preview dz-preview-multiple list-group list-group-flush mt-16pt">
        <li class="list-group-item">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <div class="avatar avatar-xs">
                        <img src="/images/icon/attached_file.png"
                            class="avatar-img rounded"
                            data-dz-thumbnail>
                    </div>
                </div>
                <div class="col">
                    <div class="font-weight-bold" data-dz-name>...</div>
                </div>
                <div class="col-auto">
                    <a href="#"
                    class="text-muted-light"
                    data-dz-remove>
                        <i class="material-icons">close</i>
                    </a>
                </div>
            </div>
        </li>
    </ul>
    `

onMounted(() => {

if(dropRef.value !== null) {
    let myDropzone = new Dropzone(dropRef.value, {
        autoProcessQueue : false,
        uploadMultiple: false,
        previewTemplate: customPreview,
        url: 'http://localhost:3011/file/',
        method: 'POST',
        acceptedFiles: "image/jpeg,image/png,image/jpg,.pdf,.docs,.docx",
        previewsContainer: dropRef.value.parentElement.querySelector('.preview-container'),
        maxFilesize: 10,
        init: function () {
            this.on("addedfile", function (file) {
                if(currentAns.value){
                    showSwal('error', 'Maximum of 1 file. Please remove the existing file');
                    this.removeFile(file); 
                    currentAns.value = '';
                    emits("update:modelValue", currentAns);
                }else if (file.size > this.options.maxFilesize * 1024 * 1024) {
                    this.removeFile(file);
                    showSwal('error', 'File size exceeds the maximum limit of 10MB.');
                } else {
                    currentAns.value = file;
                    emits("update:modelValue", currentAns);
                }
            });

            this.on("removedfile", function (file) {
                currentAns.value = '';
                emits("update:modelValue", currentAns);
            });
        }
    })

    if(dropRef.value.querySelector('.dz-default')) {
        dropRef.value.querySelector('.dz-default').innerHTML = `
        <div style="display: flex; justify-content: center;">
            <svg width="10em" height="10em" viewBox="0 0 16 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="pointer-events: none;">
            <path fill-rule="evenodd" d="m 8.0274054,0.49415269 a 5.53,5.53 0 0 0 -3.594,1.34200001 c -0.766,0.66 -1.321,1.52 -1.464,2.383 -1.676,0.37 -2.94199993,1.83 -2.94199993,3.593 0,2.048 1.70799993,3.6820003 3.78099993,3.6820003 h 8.9059996 c 1.815,0 3.313,-1.43 3.313,-3.2270003 0,-1.636 -1.242,-2.969 -2.834,-3.194 -0.243,-2.58 -2.476,-4.57900001 -5.1659996,-4.57900001 z m 2.3539996,5.14600001 -1.9999996,-2 a 0.5,0.5 0 0 0 -0.708,0 l -2,2 a 0.5006316,0.5006316 0 1 0 0.708,0.708 l 1.146,-1.147 v 3.793 a 0.5,0.5 0 0 0 1,0 v -3.793 l 1.146,1.147 a 0.5006316,0.5006316 0 0 0 0.7079996,-0.708 z"/>
            </svg>
        </div>
        <small class="form-text text-muted mb-2"><strong>Drag and drop files to upload</strong></small>
        <button class="dz-button btn btn-outline-secondary" type="button">or select files</button>
        `
    }


    if(answer){
        name = answer.answer;
        mockFile.push({id: name, name: name})
    }

    for (let i = 0; i < mockFile.length; i++) {
        myDropzone.options.addedfile.call(myDropzone, mockFile[i]);
    }

}
})


if(question.type === 'multiple answer'){
    model = ref([]);
}

if(answer){
    switch (question.type) {
        case 'multiple choice':
            currentAns.value = answer.answer;
            emits("update:modelValue", currentAns);
            break;
        case 'short answer':
            currentAns.value = answer.answer;
            emits("update:modelValue", currentAns);
            break;
        case 'essay':
            currentAns.value = answer.answer;
            emits("update:modelValue", currentAns);
            break;
        case 'file upload':
            currentAns.value = answer.answer;
            emits("update:modelValue", currentAns);
            break;
        case 'select':
            currentAns.value = answer.answer;
            emits("update:modelValue", currentAns);
            break;
        case 'multiple answer':
            const selectedOptions = [];
            JSON.parse(answer.answer).forEach(element => {
                selectedOptions.push(element);
            });
            model.value = selectedOptions;
            emits("update:modelValue", selectedOptions);
            break;
    }
}

function onCheckboxChange($event) {

    const checkboxValue = event.target.value;
    emits("update:modelValue", model.value);
    console.log(model.value);
}

function showSwal(icon, message) {
    swalBtn.fire({
        icon: icon,
        title: "Oops...",
        text: message,
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        allowEscapeKey:false,
    })
}
</script>

<style scoped>

</style>
