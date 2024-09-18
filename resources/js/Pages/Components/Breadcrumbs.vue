<template>
    <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left pt-32pt">
        <div class="flex d-flex flex-column flex-sm-row align-items-center mb-24pt mb-md-0">
            <div class="mb-24pt mb-sm-0 mr-sm-24pt">
                <!-- <Link class="h-auto" href="/subject/" v-if="$page.component == 'Lesson/Edit'"><i class="material-icons icon--left">keyboard_backspace</i> Back to Subject List</Link> -->
                <Link class="h-auto" href="/lesson/all" v-if="$page.component == 'Lesson/EditAll'"><i class="material-icons icon--left">keyboard_backspace</i> Back to Lesson List</Link>
                <Link class="h-auto" href="/faculty/" v-if="$page.component == 'Subject/AllSubjectList'"><i class="material-icons icon--left">keyboard_backspace</i> Back</Link>
                <h2 class="mb-0">{{ pageTitle }}</h2>
                <ol class="breadcrumb p-0 m-0">
                    <li class="breadcrumb-item">
                        <Link href="/">Home</Link>
                    </li>
                    <li class="breadcrumb-item" v-if="parentTitle">
                        {{ parentTitle }}
                    </li>
                    <li class="breadcrumb-item active">{{ pageTitle }}</li>
                </ol>
            </div>
        </div>
        <div class="row" role="tablist">
            <div class="col-auto">
                <!-- <Link v-if="$page.component == 'Lesson/Edit' && auth.includes('create lesson')" :href="'/lesson/create'" class="btn btn-outline-secondary">
                    <i class="material-icons icon--left">add</i> Create Another Lesson</Link> -->
                <Link v-if=" $page.component == 'Lesson/LessonList' && auth.includes('create lesson')" :href="'/lesson/create'" class="btn btn-outline-secondary">Create New Lessson</Link>
                <Link v-if=" $page.component == 'Roles/Create'" :href="'/roles'" class="btn btn-outline-secondary">Manage Roles</Link>
                <Link v-if=" $page.component == 'Roles/Edit' || $page.component == 'Roles/Role'" :href="'/roles/create'" class="btn btn-outline-secondary">Create New Role / View All Roles</Link>
                <Link v-if=" $page.component == 'Quiz/QuizList' && auth.includes('create quiz')" :href="'/quiz/create'" class="btn btn-outline-secondary">Create New Quiz</Link>
                <Link v-if=" $page.component == 'Announcement/AnnouncementList' && auth.includes('create announcement')" :href="'/announcement/create'" class="btn btn-outline-secondary">Create New Announcement</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
    let props = defineProps({
        pageTitle:String,
        parentTitle: String,
        auth: Object
    })
</script>

