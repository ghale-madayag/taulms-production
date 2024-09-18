<template>
    <div class="page-section pb-0">
        <div class="card mb-2">
            <img v-if="subjects.thumbnail" :src="'/storage/'+subjects.thumbnail"
                    class="card-img"
                    style="max-height: 33vh; width: initial;">
            <img v-else src="/images/subject_background.jpg"
            class="card-img"
            style="max-height: 100%; width: initial;">
            <div class="fullbleed bg-primary"
                    style="opacity: .5;"></div>
            <div class="card-body d-flex align-items-center justify-content-center fullbleed">
                <div>
                    <h2 class="text-white mb-16pt text-center">{{ subjects.code }} | {{ subjects.description }}</h2>
                    <div class="d-flex align-items-center mb-16pt justify-content-center">
                        <div class="d-flex align-items-center mr-16pt">
                             <span class="material-icons icon-16pt text-white mr-4pt">book</span>
                            <p class="flex text-white lh-1 mb-0">{{ section.name }}</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="material-icons icon-16pt text-white mr-4pt">person</span>
                            <p class="flex text-white lh-1 mb-0">{{ user.full_name }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center" v-if="$page.component == 'Subject/SubjectIndex' && subjects.lesson_mid.length > 0">
                        <Link :href="'/subject/'+subjects.slug+'/lesson/'+checkLesson(subjects.lesson)+'/term/1/'+section.id" class="btn btn-white mr-8pt">View Course</Link>
                    </div>
                </div>
            </div>
        </div>
        <div class="navbar justify-content-start">
            <div class="d-none d-md-block">
                <Link v-if="auth.permission.includes('view all subject')" :href="'/subject/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Subject/SubjectIndex'}" :preserve-scroll="true" class="btn btn-light" ><i class="material-icons icon--left">school</i> Overview</Link>
                <Link v-else-if="auth.permission.includes('view subject')" :href="'/subject/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Subject/SubjectIndex'}" :preserve-scroll="true" class="btn btn-light" ><i class="material-icons icon--left">school</i> Overview</Link>
                
                <Link v-if="auth.permission.includes('view all students')" :href="'/student/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Student/Index'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">account_box</i> Students</Link>
                <Link v-else-if="auth.permission.includes('view students')" :href="'/student/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Student/Index'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">account_box</i> Students</Link>

                <Link v-if="auth.permission.includes('view all lesson')" :href="'/lesson/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Lesson/Index' || $page.component == 'Lesson/Create' || $page.component == 'Lesson/Edit'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">class</i> Lesson</Link>
                <Link v-else-if="auth.permission.includes('view lesson dashboard')" :href="'/lesson/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Lesson/Index' || $page.component == 'Lesson/Create' || $page.component == 'Lesson/Edit'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">class</i> Lesson</Link>
                
                <Link v-if="auth.permission.includes('create all quiz')" :href="'/quiz/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Quiz/Index' || $page.component == 'Quiz/Create' || $page.component == 'Quiz/Edit'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">dvr</i> Quiz</Link>
                <Link v-else-if="auth.permission.includes('view quiz dashboard')" :href="'/quiz/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Quiz/Index' || $page.component == 'Quiz/Create' || $page.component == 'Quiz/Edit'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">dvr</i> Quiz</Link>

                <Link v-if="auth.permission.includes('create all announcement')" :href="'/announcement/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Announcement/Index' || $page.component == 'Announcement/Create' || $page.component == 'Announcement/Edit'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">message</i> Announcement</Link>
                <Link v-else-if="auth.permission.includes('view announcement dashboard')" :href="'/announcement/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Announcement/Index' || $page.component == 'Announcement/Create' || $page.component == 'Announcement/Edit'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">message</i> Announcement</Link>

                <Link v-if="auth.permission.includes('create all conference')" :href="'/meet/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Meet/Create'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">videocam</i> Conference</Link>
                <Link v-else-if="auth.permission.includes('view conference dashboard')" :href="'/meet/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Meet/Create'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">videocam</i> Conference</Link>

                <Link v-if="auth.permission.includes('view all students')" :href="'/grades/'+subjects.slug+'/'+section.id+'/'+section.term_id+'/lecture/midterm'" :class="{'active': $page.component == 'Report/Grades' || $page.component == 'Report/Criteria'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">insert_chart</i> Grades</Link>
                <Link v-else-if="auth.permission.includes('manage grade')" :href="'/grades/'+subjects.slug+'/'+section.id+'/'+section.term_id+'/lecture/midterm'" :class="{'active': $page.component == 'Report/Grades' || $page.component == 'Report/Criteria'}" :preserve-scroll="true" class="btn btn-light ml-8pt"><i class="material-icons icon--left">insert_chart</i> Grades</Link>
            </div>
            <div class="btn-group d-md-none">
                <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> Menu <span class="caret"></span> </button>
                <div class="dropdown-menu">
                    <Link v-if="auth.permission.includes('view all subject')" :href="'/subject/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Subject/SubjectIndex'}" :preserve-scroll="true" class="dropdown-item" ><i class="material-icons icon--left">school</i> Overview</Link>
                    <Link v-else-if="auth.permission.includes('view subject')" :href="'/subject/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Subject/SubjectIndex'}" :preserve-scroll="true" class="dropdown-item" ><i class="material-icons icon--left">school</i> Overview</Link>
                    
                    <Link v-if="auth.permission.includes('view all students')" :href="'/student/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Student/Index'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">account_box</i> Students</Link>
                    <Link v-else-if="auth.permission.includes('view students')" :href="'/student/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Student/Index'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">account_box</i> Students</Link>

                    <Link v-if="auth.permission.includes('view all lesson')" :href="'/lesson/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Lesson/Index' || $page.component == 'Lesson/Create' || $page.component == 'Lesson/Edit'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">class</i> Lesson</Link>
                    <Link v-else-if="auth.permission.includes('view lesson dashboard')" :href="'/lesson/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Lesson/Index' || $page.component == 'Lesson/Create' || $page.component == 'Lesson/Edit'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">class</i> Lesson</Link>
                    
                    <Link v-if="auth.permission.includes('create all quiz')" :href="'/quiz/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Quiz/Index' || $page.component == 'Quiz/Create' || $page.component == 'Quiz/Edit'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">dvr</i> Quiz</Link>
                    <Link v-else-if="auth.permission.includes('view quiz dashboard')" :href="'/quiz/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Quiz/Index' || $page.component == 'Quiz/Create' || $page.component == 'Quiz/Edit'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">dvr</i> Quiz</Link>

                    <Link v-if="auth.permission.includes('create all announcement')" :href="'/announcement/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Announcement/Index' || $page.component == 'Announcement/Create' || $page.component == 'Announcement/Edit'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">message</i> Announcement</Link>
                    <Link v-else-if="auth.permission.includes('view announcement dashboard')" :href="'/announcement/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Announcement/Index' || $page.component == 'Announcement/Create' || $page.component == 'Announcement/Edit'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">message</i> Announcement</Link>

                    <Link v-if="auth.permission.includes('create all conference')" :href="'/meet/'+subjects.slug+'/'+section.id+'/'+user.id" :class="{'active': $page.component == 'Meet/Create'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">videocam</i> Conference</Link>
                    <Link v-else-if="auth.permission.includes('view conference dashboard')" :href="'/meet/'+subjects.slug+'/'+section.id" :class="{'active': $page.component == 'Meet/Create'}" :preserve-scroll="true" class="btn-link dropdown-item"><i class="material-icons icon--left">videocam</i> Conference</Link>

                    <Link v-if="auth.permission.includes('view all students')" :href="'/grades/'+subjects.slug+'/'+section.id+'/'+section.term_id+'/lecture/midterm'" :class="{'active': $page.component == 'Report/Grades' || $page.component == 'Report/Criteria'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">insert_chart</i> Grades</Link>
                    <Link v-else-if="auth.permission.includes('manage grade')" :href="'/grades/'+subjects.slug+'/'+section.id+'/'+section.term_id+'/lecture/midterm'" :class="{'active': $page.component == 'Report/Grades' || $page.component == 'Report/Criteria'}" :preserve-scroll="true" class="dropdown-item"><i class="material-icons icon--left">insert_chart</i> Grades</Link>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>

let props = defineProps({
    auth: Object,
    user:Object,
    subjects: Object,
    section: Object
})

const checkLesson = (lessons) =>{
    for(const lesson of lessons){
        if(lesson.position === '1' && lesson.term === '1'){
            return lesson.slug
        }
    }
}

</script>

<style>
h2.text-white.mb-16pt.text-center {
    font-size: clamp(1rem, 0.5992rem + 1.5564vw, 2rem);
}
</style>