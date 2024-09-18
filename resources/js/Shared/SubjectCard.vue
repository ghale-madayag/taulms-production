<template>
    <div class="card-group-row__col col-md-6" v-for="data in subjects">
        <div class="card card-group-row__card card-sm">
            <div class="card-body d-flex align-items-center">
                <a href="javascript:void(0)"
                      class="avatar avatar-4by3 mr-12pt">
                    <img v-if="data.thumbnail" :src="'/storage/'+data.thumbnail"
                         class="avatar-img rounded">
                    <img v-else-if="!data.thumbnail" src="/images/blank.webp"
                         class="avatar-img rounded">
                    <span class="overlay__content"></span>
                </a>
                <div class="flex mr-12pt">
                    <a class="card-title"
                          href="javascript:void(0)">{{ data.title }}</a>
                    <div class="card-subtitle text-50">{{ data.code }}</div>
                </div>
                <div v-if="data.student_lesson_statuses.length">
                    <div class="text-right">
                        <span class="lead text-headings lh-1">{{ getValue(data.student_lesson_statuses.length,data.lesson_term.map(a=>a.section_id).filter(x => x==data.section_id).length  ) }}</span><br>
                        <small class="text-50 text-uppercase text-headings">Complete</small>
                    </div>
                </div>
            </div>
            <div v-if="!auth.includes('view all students')" class="progress rounded-0" style="height: 5px;">
                <div class="progress-bar bg-primary"
                     role="progressbar"
                     :style="'width:'+getValue(data.student_lesson_statuses.length,data.lesson_term.map(a=>a.section_id).filter(x => x==data.section_id).length)"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center">
                    <div class="flex mr-2">
                        <Link v-if="data.student_courses.length == 0 && data.lesson_term.length && data.lesson_term.map(a=>a.section_id).includes(data.section_id) && !auth.includes('view all students')"
                            @click="loadStart(data.slug, 1, data.section_id)"
                              class="btn btn-light btn-sm">
                            Start<i class="material-icons icon--right">arrow_forward_ios</i>
                        </Link>
                        <Link v-else-if="data.student_courses.length !=0 && data.student_courses.map(a=>a.finished).includes('0')
                        && data.student_courses.map(a=>a.term).includes('1') && !auth.includes('view all students')"
                              @click="loadContinue(data.slug, 1, data.section_id)"
                              class="btn btn-light btn-sm">
                            <i class="material-icons icon--left">refresh</i> Resume Midterm
                        </Link>
                        <Link v-else-if="data.student_courses.length !=0 && data.student_courses.map(a=>a.finished).includes('0')
                        && data.student_courses.map(a=>a.term).includes('2') && !auth.includes('view all students') && data.lesson_term.map(a=>a.term).includes('2')"
                              @click="loadStart(data.slug,2, data.section_id)"
                              class="btn btn-light btn-sm">
                            <i class="material-icons icon--left">refresh</i> Resume Finals
                        </Link>
                        <Link v-else-if="data.lesson_term.length > 0 && !data.lesson_term.map(a=>a.term).includes('2')" class="ml-4pt btn btn-sm btn-accent text-white">Finals Not Ready</Link>
                        <button v-else-if="data.student_courses.map(a=>a.finished).includes('1') && !auth.includes('view all students')" class="btn btn-warning btn-sm text-white">
                            Completed<i class="material-icons icon--right">done</i>
                        </button>
                    </div>
                    <div class="d-flex mr-3">
                        <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                        <p class="flex text-50 lh-1 mb-0"><small>{{ data.sched_date }}</small></p>
                    </div>
                    <div class="dropdown" v-if="!auth.includes('view all students')">
                        <a href="#"
                           data-toggle="dropdown"
                           data-caret="false"
                           class="text-muted"><i class="material-icons">more_horiz</i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <Link :href="'/subject/'+data.slug+'/'+data.section.id"
                                  class="dropdown-item">View Course</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
let props = defineProps({
    auth: Object,
    subjects: Object,
})

let form = useForm({
    subject: null,
    term: null,
    section: null,
});

function loadStart(subject, term, section){
    form.subject = subject;
    form.term = term;
    form.section = section;
    form.post('/lesson/'+subject+'/start');
}

function loadContinue(subject, term,section){
    form.subject = subject;
    form.term = term;
    form.section = section;

    form.post('/lesson/'+subject+'/getActive');

}

function getValue($active, $total){
    let total = $active/$total * 100;
    return total.toFixed()+'%';
}
</script>

