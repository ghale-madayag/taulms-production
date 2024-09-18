<template>
    <Head>
        <title>Report</title>
    </Head>
    <Breadcrumbs pageTitle="Report"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <!-- <div class="row">
                <div class="col-md-8">
                    <ReportCard :subjects="subjects"/>
                </div>
                <div class="col-md-4"></div>
            </div> -->
            <div class="row card-group-row mb-lg-8pt">
                <div class="col-lg-7 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-6">
                                <div class="card-body">
                                    <h6 class="text-50">Overview</h6>
                                    <div class="h1 mb-0">{{ total_subject }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Total Subject</strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="h1 mb-0">{{ completed }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Completed Subject</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 border-left">
                                <div class="card-body">
                                    <h6 class="text-50">Engagement</h6>
                                    <div class="h1 mb-0">{{ started(subject.map(a=>a.student_courses.length)) }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Started Subject</strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="h1 mb-0">{{ student_result }}</div>
                                    <strong>Quizzes</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="total != 0" class="col-lg-5">
                    <div class="mb-24pt mb-sm-0">
                            <div class="border-left-3 border-left-primary pl-24pt pl-md-32pt mb-3">
                                <div class="alert alert-primary p-24pt" role="alert">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="mr-8pt mt-4pt">
                                            <i class="material-icons text-primary">access_time</i>
                                        </div>
                                        <div class="flex" style="min-width: 180px">
                                            <h4 class="mb-16pt text-primary">Attention Student:</h4>
                                            <p class="text-70 mb-24pt mb-lg-0">Please be advised that the displayed grade is not yet final and may be subject to changes. Your performance is still under review, and adjustments may occur at any time. Please wait for your faculty announcement regarding any updates.</p>
                                            <br>
                                            <p class="text-70 md-24pt mb-lg-0">Thank you for your understanding and patience.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="page-separator mb-32pt">
                <div class="page-separator__text">Subject List</div>
            </div>
            
            <ReportCard :subjects="subjects"/>
        </div>
    </div>

</template>

<script setup>
import Breadcrumbs from "../Components/Breadcrumbs";
import ReportCard from "../../Shared/ReportGradeCard";
import ListPagination from "../../Shared/ListPagination";
import {ref, computed} from "vue";
import debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";

let quiz = ref();
const props = defineProps({
    subject: Object,
    subjects: Object,
    total: Number,
    total_subject: Number,
    completed: Number,
    student_result: Number,
});



function started(total){
    const sum = total.reduce((a, b) => a + b, 0);
    return sum;
}


</script>

<style scoped>

</style>
