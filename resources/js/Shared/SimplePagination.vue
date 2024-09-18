<template>
    <div class="nav-item d-flex flex-column flex-sm-row ml-sm-16pt">

        <a v-if="finished_quiz != 0 && finished_quiz != null" href="javascript:void(0)" @click="viewScore()"
           class="btn btn-outline-secondary ml-sm-16pt mb-sm-0 mb-16pt"><i class="material-icons icon--left">live_help</i>Quiz Result</a>

        <Link class="btn justify-content-center btn-outline-secondary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt"
              :href="'/subject/'+lesson.subject.slug+'/lesson/'+link[link_prev].slug+'/term/'+lesson.term+'/'+lesson.section_id"
              :class="link[link_prev].slug == active ? 'disabled' : ''"
        >
            <i class="material-icons icon--left">keyboard_arrow_left</i> Previous
        </Link>

        <Link  v-if="auth.permission.includes('review quiz')" @click="preview((link[link_next].slug))" :class="link[link_next].slug == active ? 'disabled' : ''" class="btn justify-content-center btn-primary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt">
            Review Next Lesson <i class="material-icons icon--right">keyboard_arrow_right</i>
        </Link>

        <Link v-if="(lesson.quiz.length != 0 && link[link_next].slug != active && !auth.permission.includes('review quiz')) && (finished_quiz == 0 || finished_quiz == null)" class="btn justify-content-center btn-outline-secondary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt"
              @click="finish(link[link_next].slug)">
            Skip Quiz <i class="material-icons icon--right">skip_next</i></Link>

        <Link class="btn justify-content-center btn-primary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt"
              v-if="(link[link_next].slug != active && lesson.quiz.length == 0 && !auth.permission.includes('review quiz')) || (finished_quiz != 0 && finished_quiz != null)"
              :disabled="nextBtn" :class="nextBtn == true ? 'disabled is-loading': ''"
              @click="finish(link[link_next].slug)">
            Next Lesson <i class="material-icons icon--right">keyboard_arrow_right</i></Link>


        <div v-for="(quiz,index) in lesson.quiz" v-if="(finished_quiz == 0 || finished_quiz == null) && !auth.permission.includes('review quiz')">
            <button v-if="!lesson.quiz[index].student_quiz_status_pending.length" @click="startQuiz(lesson.quiz[index].slug)" class="btn btn-primary ml-sm-16pt">Take Quiz {{ index + 1}}</button>
            <button v-else-if="lesson.quiz[index].student_quiz_status_pending.length" @click="continueQuiz(lesson.quiz[index].slug,lesson.quiz[index].student_quiz_status_pending[0].question_id)" class="btn btn-primary ml-sm-16pt">Resume Quiz</button>
        </div>


        <button @click="complete"
                class="btn btn-primary ml-sm-16pt"
                v-if="link[link_next].slug == active && auth.role != 'faculty'"
                :disabled="nextBtn"
                :class="{ 'is-loading': nextBtn}" >Complete {{ lesson.term == 1 ? 'Midterm' : 'Finals'}}<i class="material-icons icon--right">done</i></button>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia'
import { ref } from 'vue';
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

let nextBtn = ref(false);
let $term;

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})


let props = defineProps({
    auth:Object,
    lesson: Object,
    link_next: Number,
    link_prev: Number,
    active: String,
    link: Array,
    finished_quiz: Object,
})

let form = useForm({
    next: '',
    complete: false,
    slug: props.active,
    quiz_id: '',
});

function preview(next){
    Inertia.get(
        '/subject/'+props.lesson.subject.slug+'/lesson/'+next+'/term/'+props.lesson.term+'/'+props.lesson.section_id
    )
}

function finish(next){
    form.next = next;
    form.post('/lesson/'+props.lesson.slug+'/finished',{
        onStart: () => { nextBtn.value = true},
        onSuccess:()=>{
            nextBtn.value = false
            Inertia.get(
                '/subject/'+props.lesson.subject.slug+'/lesson/'+next+'/term/'+props.lesson.term+'/'+props.lesson.section_id
            )
        }
    });
}

function complete(next){
    form.complete = true;
    form.post('/lesson/'+props.lesson.slug+'/finished',{
        onStart: () => { nextBtn.value = true},
        onSuccess:()=>{
            nextBtn.value = false
            if(props.lesson.term == 1){
                $term = 'Midterm';
            }else{
                $term = 'Finals';
            }
            if(form.complete){
                swalBtn.fire({
                    title: 'Congratulations!',
                    text: "You finished "+ props.lesson.subject.title+' '+ $term,
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    allowOutsideClick: false,
                    allowEscapeKey:false,
                    confirmButtonText: 'BACK TO COURSE LIST'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Inertia.get('/subject/')
                    }
                })
            }
        }
    });
}

function viewScore() {

    const score = props.finished_quiz.score;
    const totals = props.finished_quiz.question.map(a => a.points);

    let sum = totals.reduce((accumulator, currentValue) => parseInt(accumulator) + parseInt(currentValue), 0);
    let avg = (score / sum) * 100;
    let icon = avg < 60 ? "error" : "success";

    const questionTypes = props.finished_quiz.question.map(a => a.type);
    const hasEssayOrFileUpload = questionTypes.includes('essay') || questionTypes.includes('file upload');

    if (hasEssayOrFileUpload) {
        if(props.finished_quiz.submitted_score > 0){
            swalBtn.fire({
                title: 'Pending Score: ' + score + '/' + sum,
                text: 'Due to the inclusion of essay/file upload questions in this quiz, your score is currently pending. Please await further announcements regarding the assessment of these submissions. Thank you for your patience.',
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'BACK TO LESSON'
            })
        }else{
            swalBtn.fire({
                title: 'Your score: ' + score + '/' + sum,
                text: props.lesson.quiz[0].title + ' ' + avg.toFixed() + '% Marked',
                icon: icon,
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'BACK TO LESSON'
            })
        }
        
    } else {
        swalBtn.fire({
            title: 'Your score: ' + score + '/' + sum,
            text: props.lesson.quiz[0].title + ' ' + avg.toFixed() + '% Marked',
            icon: icon,
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'BACK TO LESSON'
        })
    }
}

function startQuiz(quiz){
    form.post('/question/'+quiz+'/start');
}

function continueQuiz(quiz,question){
    Inertia.get('/quiz/'+quiz+'/question/'+question);
}
</script>

<style scoped>

</style>
