<template>
    <div class="d-flex align-items-center page-num-container mb-16pt">
        <div class="page-num">{{ index }}</div>
        <h4>Question {{ index }}</h4>
    </div>
    <div class="text-70 mb-32pt mb-lg-48pt" v-html="quiz.question"></div>

        <ul v-if="quiz.type === 'multiple choice'" v-for="(option, ind) in JSON.parse(quiz.data).options" :key="option.uuid" class="list-quiz">
            <li class="list-quiz-item" :class="quiz.answer == option.text ? 'active': ''">
                <span v-if="quiz.answer == option.text" class="list-quiz-badge bg-primary text-white">
                    <i class="material-icons">check</i>
                </span>
                <span v-else class="list-quiz-badge"></span>
                <span class="list-quiz-text">{{ option.text }}</span>
            </li>
        </ul>

        <ul v-if="quiz.type === 'multiple answer'" v-for="(option, ind) in JSON.parse(quiz.data).options" :key="option.uuid" class="list-quiz">
            <li class="list-quiz-item" :class="option.text == stringVal(quiz.answer) ? 'active': ''">
                <span v-if="option.text == stringVal(quiz.answer[0].answer)" class="list-quiz-badge bg-primary text-white">
                    <i class="material-icons">check</i>
                </span>
                <span v-else class="list-quiz-badge"></span>
                <span class="list-quiz-text">{{ option.text }}</span>
            </li>
        </ul>

        <ul v-if="quiz.type === 'short answer'" class="list-quiz">
            <li class="list-quiz-item active">
                <span class="list-quiz-badge bg-primary text-white">
                    <i class="material-icons">check</i>
                </span>
                <span class="list-quiz-text">{{ quiz.answer }}</span>
            </li>
        </ul>

        <ul v-if="quiz.type === 'essay'" class="list-quiz">
            <li class="list-quiz-item active">
                <span class="list-quiz-badge bg-primary text-white">
                    <i class="material-icons">check</i>
                </span>
                <span class="list-quiz-text">{{ quiz.answer }}</span>
            </li>
        </ul> 
        
    <h5 class="mt-32pt mt-lg-48pt text-secondary">
        Points :  {{ quiz.points   }}
    </h5>

</template>

<script setup>
let props = defineProps({
    quiz: Object,
    index: Number,
})

function stringVal(val){
    return val.toString().replace(/[\[\]\"\']/g, '');
}

</script>