<template>
    <div class="posts-cards mb-24pt">
        <div class="card posts-card" v-for="data in subjects">
            <div class="accordion js-accordion accordion--boxed list-group-flush"
                 id="parent">
                <div class="accordion__item">
                    <a href="#"
                       class="accordion__toggle"
                       data-toggle="collapse"
                       :data-target="'#course-toc-'+data.id"
                       data-parent="#parent">
                        <div class="avatar avatar-md mr-3"><a href=""><img :src="data.thumbnail ? '/storage/'+data.thumbnail : '/images/blank.webp'" alt="avatar" class="avatar-img rounded"></a></div>
                        <span class="flex mr-4">{{ data.title }}</span>
<!--                        <span class="text-50 text-uppercase posts-card__tag d-flex align-items-center mr-5">Midterm: 80%</span>-->
<!--                        <span class="text-50 text-uppercase posts-card__tag d-flex align-items-center">Finals: 60%</span>-->
                        <div class="flex mr-3" style="max-width: 100%">
                            <span class="lead text-headings lh-1">{{ getValue(data.student_lesson_statuses.length,data.lesson.length) }}</span>
                            <div class="progress" style="height: 4px;" v-if="getValue(data.student_lesson_statuses.length,data.lesson.length )">
                                <div class="progress-bar bg-primary" role="progressbar"
                                     :style="'width:'+getValue(data.student_lesson_statuses.length,data.lesson.length)"
                                ></div>
                            </div>
                        </div>
                        <span class="accordion__toggle-icon material-icons">keyboard_arrow_down</span>
                    </a>
                    <div class="accordion__menu collapse"
                            :id="'course-toc-'+data.id" v-for="(lesson,indexLesson) in data.lesson">
                           <div class="accordion__menu-link" v-for="(quiz,index) in lesson.quiz">
                               {{ getQuiz(1) }}
                                <span class="icon-holder icon-holder--small icon-holder--light rounded-circle d-inline-flex icon--left">
                                <i class="material-icons icon-16pt">hourglass_empty</i>
                                </span>
                                <a class="flex" href="javascript:void(0)"><strong>{{ lesson.term == 1 ? 'Midterm' : 'Finals' }} Quiz:</strong> {{ quiz.title }}</a>
                               
                               <div class="ml-auto" v-for="result in quiz.student_quiz">
                                       <span v-if="result.finished != 0" class="badge mr-3 " :class="getAvg(result.score,result.total) == 'Passed' ? 'badge-primary' : 'badge-accent'">{{ getAvg(result.score,result.total) }}</span>
                                      
                                       <!-- <span v-if="result.finished != 0" class="mr-3"><i class="material-icons">schedule</i>{{ getDiff(quiz.quiz_schedule,result.remaining) }}</span> -->
                                       <button v-if="result.finished != 0" class="btn-outline-secondary btn btn-rounded ml-auto">{{ result.score }}/{{ result.total }}</button>
                                        <span v-if="result.finished == 0" class="badge mr-3 badge-warning">Pending</span>
                               </div>
                           </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

let quiz = [];

let props = defineProps({
    subjects: Object,
    student_result: Object,
})

const emit = defineEmits(['delete','quiz']);
function destroy(id) {
    emit('delete', id);
}

function getDiff(duration, remaining){
    var d = duration;
    var r = remaining;

    d = d.substring(0, d.indexOf('.'));
    r = r.substring(0, r.indexOf('.'));

    var date1 = new Date("11/11/2022 "+d);
    var date2 = new Date("11/11/2022 "+r);

    var diff = date1.getTime() - date2.getTime();

    var msec = diff;
    var hh = Math.floor(msec / 1000 / 60 / 60);
    msec -= hh * 1000 * 60 * 60;
    var mm = Math.floor(msec / 1000 / 60);
    msec -= mm * 1000 * 60;
    var ss = Math.floor(msec / 1000);
    msec -= ss * 1000;

    return hh + ":" + mm + ":" + ss;
}

function getValue($active, $total){
    let total = $active/$total * 100;
    if(!isNaN(total)){
        total = total.toFixed()+'%'
    }else{
        total = 0+'%';
    }
    return total;
}

function getAvg($active, $total){
    let total = $active/$total * 100;
    let avg;
    if(total <= 60){
        avg = 'Failed';
    }else{
        avg = 'Passed';
    }

    return avg;
}

function getQuiz(total){
    quiz.push(total);
    const sum = quiz.reduce((a, b) => a + b, 0);
    emit('quiz', sum);
}
</script>

<style>
.draft{
    background:#272c3313 !important;
}
[dir] .accordion--boxed .accordion__item{
    border:none;
}

[dir] .badge-primary {
    background-color: #5b906d;
}

[dir=ltr] .accordion__menu-link {
    padding: .5rem 1rem .5rem 1.5rem;
    margin-bottom: -1px;
    border-bottom: 1px solid #e9edf2;
}
</style>
