<template>
    <div v-if="auth.role=='student'" class="nav-item d-flex flex-column flex-sm-row ml-sm-16pt">
        <a href="javascript:void(0)" @click="introduction()" class="btn justify-content-center btn-outline-secondary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt">Review Instructions</a>
        <a class="btn justify-content-center btn-outline-secondary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt"
              href="javascript:void(0)"
              @click="prev(question.quiz.slug,link[link_prev].id)"
              :class="link[link_prev].id == active ? 'disabled' : ''"
        > <i class="material-icons icon--left">keyboard_arrow_left</i> Previous
        </a>

        <Link class="btn justify-content-center btn-primary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt"
              v-if="link[link_next].id != active"
              :disabled="nextBtn" :class="nextBtn == true ? 'disabled is-loading': ''"
              @click="finish(link[link_next].id)">
            Next Question <i class="material-icons icon--right">keyboard_arrow_right</i></Link>

        <button @click="complete"
                class="btn btn-primary ml-sm-16pt"
                v-if="link[link_next].id == active"
                :disabled="nextBtn"
                :class="{ 'is-loading': nextBtn}" >Complete<i class="material-icons icon--right">done</i></button>
    </div>
    <div v-else class="nav-item d-flex flex-column flex-sm-row ml-sm-16pt">
        <a href="javascript:void(0)" @click="introduction()" class="btn justify-content-center btn-outline-secondary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt">Review Instructions</a>
        <Link class="btn justify-content-center btn-outline-secondary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt"
              :href="link[link_prev].id"
              :class="link[link_prev].id == active ? 'disabled' : ''"
        > <i class="material-icons icon--left">keyboard_arrow_left</i> Previous
        </Link>
        <Link class="btn justify-content-center btn-primary w-100 w-sm-auto mb-16pt mb-sm-0 ml-sm-16pt"
              v-if="link[link_next].id != active"
              :disabled="nextBtn" :class="nextBtn == true ? 'disabled is-loading': ''"
              :href="link[link_next].id"
        >
            Next Question <i class="material-icons icon--right">keyboard_arrow_right</i>
        </Link>
        <Link
            :href="'/quiz/'+question.quiz.lesson.subject.slug+'/'+question.quiz.lesson.section_id"
            class="btn btn-primary ml-sm-16pt"
            v-if="link[link_next].id == active"
            :disabled="nextBtn"
            :class="{ 'is-loading': nextBtn}" >Back To Quiz<i class="material-icons icon--right">done</i></Link>
    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import {Inertia} from "@inertiajs/inertia";
import { useForm } from '@inertiajs/inertia-vue3';
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

let nextBtn = ref(false);

let props = defineProps({
    auth: Object,
    question: Object,
    link_next: Number,
    link_prev: Number,
    active: Number,
    link: Array,
    answer:Object,
})
let form = useForm({
    next: 0,
    complete: false,
    quizzes_id: props.question.quiz_id,
    questions_id: props.question.id,
    answers: [],
    hrs: 0,
    min: 0,
    sec: 0,
});


const emit = defineEmits(['finish','prev']);

onMounted(() => {
   if(props.link[0].id == props.question.id){
       introduction();
   }
});

function strippedContent(string) {
    return string.replace(/<\/?[^>]+>/ig, " ");
}

function prev(quiz,question){
    emit('prev', form,quiz,question)
}

function finish(next){
    form.next = next;
    emit('finish',form);
}

function complete(){
    form.complete = true;
    emit('finish',form);
}

function introduction(){
    swalBtn.fire({
        title: 'Instructions',
        text: strippedContent(props.question.quiz.description),
        icon: 'info',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        allowEscapeKey:false,
        confirmButtonText: 'Got it!'
    })
}
</script>

<style scoped>

</style>
