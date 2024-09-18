<template>
    <Head>
        <title>Students Info</title>
    </Head>
    <Breadcrumbs :pageTitle='section.name+" | "+subject.title'/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <div class="row card-group-row mb-lg-8pt">
                <div class="col-lg-4 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-6">
                                <div class="card-body">
                                    <h6 class="text-50">Overview</h6>

                                    <div class="h2 mb-0">{{ total }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Total Students</strong>
                                        <small class="text-50">This Semester</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="page-separator">
                        <div class="page-separator__text">Student List</div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-24pt" style="white-space: nowrap;">
                        <small class="flex text-muted text-headings text-uppercase mr-3 mb-2 mb-sm-0"></small>
<!--                        <div class="w-auto ml-sm-auto table flex align-items-center mb-2 mb-sm-0">-->
<!--                            <div class="form-inline">-->
<!--                                <label class="mr-sm-2 form-label" for="inlineFormFilterBy">Filter by:</label>-->
<!--                                <input v-model="search"-->
<!--                                       type="text"-->
<!--                                       class="flex w-auto form-control search mb-2 mr-sm-2 mb-sm-0"-->
<!--                                       id="inlineFormFilterBy"-->
<!--                                       placeholder="Search ...">-->
<!--                                <select id="inlineFormRole" class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="selected">-->
<!--                                    <option value="all">All Students</option>-->
<!--                                    <option value="validated">Validated</option>-->
<!--                                    <option value="notvalidated">Not Validated</option>-->
<!--                                </select>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                    <!--Card-->
                    <div class="alert alert-warning mb-0" role="alert" v-if="!student.data.length">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="mr-8pt">
                                <i class="material-icons">access_time</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <strong>No data found!</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                    <ListCard :data="student.data"/>
                    <ListPagination :links="student.links"/>
                    <!-- End Card-->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from "../Components/Breadcrumbs";
import ListCard from '../../Shared/StudentSubjectCard.vue';
import ListPagination from "../../Shared/ListPagination.vue";
import {ref, watch} from "vue";
import debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";

let props = defineProps({
    student:Object,
    subject: Object,
    section: Object,
    total: Number,
    filters: Object,
})

let search = ref(props.filters.search);
let selected = ref(props.filters.selected);

watch([search, selected], debounce(function (value){
    Inertia.get(
        '/student/'+props.subject.slug+'/'+props.section.id,
        {search: value[0],selected: value[1]},
        {
            preserveState: true,
            replace: true
        })
}, 500))
</script>

