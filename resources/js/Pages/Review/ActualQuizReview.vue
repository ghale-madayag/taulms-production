<template>
    <Head>
       <title>Review Quiz</title>
   </Head>
   <div class="navbar navbar-list navbar-light bg-white border-bottom-2 border-bottom navbar-expand-sm" style="white-space: nowrap;">
        <div class="container page__container">
            <nav class="nav navbar-nav">
                <div class="nav-item navbar-list__item">
                    <Link :href="'/quiz/'+question.quiz.lesson.subject.slug+'/'+question.quiz.lesson.section_id" class="nav-link h-auto"><i class="material-icons icon&#45;&#45;left">keyboard_backspace</i> Back to Quiz</Link>
                </div>
            </nav>
        </div>
    </div>
    <div class="bg-header pb-lg-64pt pb-lg-4pt py-32pt">
        <div class="container page__container">
            <TopPaginationQuiz :links='links' :active="active" :auth="auth"/>
            <div v-for="(link, index) in links">
                <div class="d-flex flex-wrap align-items-end mb-16pt" v-if="question.id==links[index].id">
                    <h1 class="h2 mb-0 flex m-0">Question {{ index+1 }} of {{ links.length }} (Preview) </h1>
                </div>
            </div>
            <p class="hero__lead measure-hero-lead text-70 mb-24pt">
                <div v-if="question.question" v-html="question.question"></div>
            </p>
            <p class="hero__lead measure-hero-lead text-70 mb-24pt">
                {{ question.description ? strippedContent(question.description) : '' }}
            </p>
        </div>
    </div>
    <div class="navbar navbar-expand-md navbar-list navbar-light bg-white border-bottom-2 "
         style="white-space: nowrap;">
        <div class="container page__container">
            <ul class="nav navbar-nav flex navbar-list__item">
                <li class="nav-item navbar-list__item">
                    <div class="media align-items-center">
                        <ul class="nav navbar-nav flex navbar-list__item">
                            <li class="nav-item" v-if="question.type==='multiple answer' || question.type==='select' || question.type==='multiple choice'">
                                <i class="material-icons text-50 mr-8pt">tune</i>
                                Choose the correct answer below:
                            </li>
                            <li class="nav-item" v-if="question.type==='short answer' || question.type==='essay'">
                                <i class="material-icons text-50 mr-8pt">tune</i>
                                Please type your answer below:
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div class="nav navbar-nav ml-sm-auto navbar-list__item" v-for="(link,index) in links">
                <SimplePaginationQuiz v-if="link.id==active"
                                      :link_next='index+1 < links.length ? index+1 : index'
                                      :link="links"
                                      :question="question"
                                      :active="active"
                                      @finish="setNext"
                                      @prev="setPrev"
                                      :link_prev="index == 0 ? index : index-1"
                                      :auth="auth"
                />
            </div>
        </div>
    </div>
    <div class="container page-section">

        <div class="page-separator">
            <div class="page-separator__text">Your Answer</div>
        </div>
        <QuestionViewer
            :question="question"
            :auth="auth"
        />
        <p class="text-50 mb-0" v-if="question.type === 'multiple answer'">Note: There can be multiple correct answers to this question.</p>
    </div>
</template>

<script setup>
    import TopPaginationQuiz from "../../Shared/TopPaginationQuiz";
    import SimplePaginationQuiz from "../../Shared/SimplePaginationQuiz";
    import QuestionViewer from "../../Shared/QuestionViewer";

    let props = defineProps({
        auth: Object,
        question: Object,
        links: Object,
        active: Number,
    })
</script>
<style>
[dir] .course-nav::before {
    background-color: #303840;
}

.bg-header {
    background-color: #dfdfdf !important
}

.col-lg-8.imgWidth img {
    max-width: 560px !important;
}

iframe.ql-video {
    width: 600px !important;
    height: 400px !important;
}
</style>