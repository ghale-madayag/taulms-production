<template>
    <div class="d-flex align-items-center page-num-container mb-16pt">
        <div class="page-num">{{ index }}</div>
        <h4>Question {{ index }}</h4>
    </div>
    <div class="text-70 mb-32pt mb-lg-48pt" v-html="quiz.question"></div>

        <ul v-if="quiz.type === 'multiple choice'" v-for="(option, ind) in JSON.parse(quiz.data).options" :key="option.uuid" class="list-quiz">
            <li class="list-quiz-item" :class="quiz.answer[0].answer == option.text ? 'active': ''">
                <span v-if="quiz.answer[0].answer == option.text" class="list-quiz-badge" :class="quiz.final_answer == quiz.answer[0].answer ? 'bg-primary text-white': 'bg-danger text-white'">
                    <i v-if="quiz.final_answer == quiz.answer[0].answer" class="material-icons">check</i>
                    <i v-else class="material-icons">close</i>
                </span>
                <span v-else class="list-quiz-badge"></span>
                <span class="list-quiz-text">{{ option.text }}</span>
            </li>
        </ul>

        <ul v-if="quiz.type === 'multiple answer'" v-for="(option, ind) in JSON.parse(quiz.data).options" :key="option.uuid" class="list-quiz">
            <li class="list-quiz-item" :class="option.text == stringVal(quiz.answer[0].answer) ? 'active': ''">
                <span v-if="option.text == stringVal(quiz.answer[0].answer)" class="list-quiz-badge" :class="quiz.final_answer.includes(JSON.parse(quiz.answer[0].answer)) && option.text == stringVal(quiz.answer[0].answer) ? 'bg-primary text-white': 'bg-danger text-white'">
                    <i v-if="quiz.final_answer.includes(JSON.parse(quiz.answer[0].answer)) && option.text == stringVal(quiz.answer[0].answer)" class="material-icons">check</i>
                    <i v-else class="material-icons">close</i>
                </span>
                <span v-else class="list-quiz-badge"></span>
                <span class="list-quiz-text">{{ option.text }}</span>
            </li>
        </ul>

        <ul v-if="quiz.type === 'short answer'" class="list-quiz">
            <li class="list-quiz-item active">
                <span class="list-quiz-badge" :class="quiz.final_answer == quiz.answer[0].answer ? 'bg-primary text-white': 'bg-danger text-white'">
                    <i v-if="quiz.final_answer == quiz.answer[0].answer" class="material-icons">check</i>
                    <i v-else class="material-icons">close</i>
                </span>
                <span class="list-quiz-text">{{ quiz.answer[0].answer }}</span>
            </li>
        </ul>

        <ul v-if="quiz.type === 'essay'" class="list-quiz">
            <li class="list-quiz-item">
                <span class="list-quiz-text">{{ quiz.answer[0].answer }}</span>
            </li>
        </ul>
        <div v-if="quiz.type === 'essay'">
            <div class="form-row">
                <div class="col-12 col-md-2 mb-2">
                    <div class="form-group">
                        <input type="number" v-model="score" class="form-control" :class="{'is-invalid': msg, 'is-valid': success}" :id="'score_'+quiz.answer[0].id" placeholder="Enter Score..." min="0" max="10" @input="validateScore">
                        <div class="invalid-feedback" required>Min: 0 & Max score: {{ quiz.points }} </div>
                        <div class="valid-feedback"></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" :disabled="loading" :class="{ 'is-loading': loading}" @click="saveScore(score,quiz.points,quiz.answer[0].id, student)">
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ul v-if="quiz.type === 'file upload'" class="list-quiz mb-32pt">
            <li class="list-quiz-item active">
                <span>
                    <!-- <img :src="'/storage/attachement/quiz/'+quiz.answer[0].answer" alt="Uploaded Image" style="max-width: 360px; max-height: 360px;"> -->
                    <a :href="'/storage/attachement/quiz/'+quiz.answer[0].answer" download>
                        <button type="button" class="btn btn-primary">
                            <i class="material-icons icon--left">file_download</i> Download Answer
                        </button>
                    </a>
                </span>
                <!-- <span v-else class="list-quiz-text">Document: {{ quiz.answer[0].answer }}</span> -->
            </li>
        </ul><br>
        <div v-if="quiz.type === 'file upload'">
            <div class="form-row">
                <div class="col-12 col-md-2 mb-2">
                    <div class="form-group">
                        <input type="number" v-model="score" class="form-control" :class="{'is-invalid': msg, 'is-valid': success}" :id="'score_'+quiz.answer[0].id" placeholder="Enter Score..." min="0" max="10" @input="validateScore">
                        <div class="invalid-feedback" required>Min: 0 & Max score: {{ quiz.points }} </div>
                        <div class="valid-feedback"></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" :disabled="loading" :class="{ 'is-loading': loading}" @click="saveScore(score,quiz.points,quiz.answer[0].id, student)">
                            Update
                        </button>
                    </div>
                </div>
            </div>
            <h5 class="mt-32pt mt-lg-48pt text-secondary">
                Points :  {{ quiz.points   }}
            </h5>
        </div>
    
    <h5 v-else class="mt-32pt mt-lg-48pt text-secondary">
        Answer: {{ stringVal(quiz.final_answer ? quiz.final_answer : '') }} / Points :  {{ quiz.points   }}
    </h5>

</template>

<script setup>
import {ref, onMounted, watch} from "vue";


const emit = defineEmits(['saveScore']);
let props = defineProps({
    quiz: Object,
    index: Number,
    process: Object,
    student: String,
})

let score = props.quiz.answer[0].points != -1 ? props.quiz.answer[0].points : "";
let msg = ref(null);
let success = ref(null);
let loading = ref(null);

function saveScore(scores, points, id, student){
    
    msg.value = false;
    success.value = false;
    
    if(parseInt(scores)>=0 && parseInt(scores) <= points){
        success.value = true;
        loading = props.process.processing;
        emit('saveScore',parseInt(scores),id,student);
    }else{
       msg.value = true;
    }
}

function stringVal(val){
    return val.toString().replace(/[\[\]\"\']/g, '');
}

function isImage(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    return extension === 'jpg' || extension === 'jpeg' || extension === 'png' || extension === 'gif';
}


</script>