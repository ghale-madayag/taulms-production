<template>
    <Head>
        <title>Students</title>
    </Head>
    <Breadcrumbs pageTitle="Students"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <div class="row card-group-row mb-lg-8pt">
                <div class="col-lg-7 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-6">
                                <div class="card-body">
                                    <h6 class="text-50">Overview</h6>

                                    <div class="h2 mb-0">{{ validated+notvalidated }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Total Students</strong>
                                        <small class="text-50">This Semester</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="h2 mb-0">0</div>
                                    <div class="d-flex flex-column">
                                        <strong>Active Today</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 border-left">
                                <div class="card-body">
                                    <h6 class="text-50">Validated</h6>

                                    <div class="h2 mb-0">{{ validated }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Total Views</strong>
                                        <small class="text-50">This Semester</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="h2 mb-0">{{ notvalidated }}</div>
                                    <strong>Not Validated</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 card-group-row__col">
                    <div class="card card-group-row__card">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="mb-8pt" v-for="year in displayTotal">
                                <p class="d-flex align-items-center mb-4pt">
                                    <small class="flex lh-24pt"><strong>{{ checkLvl(year.year_lvl) }}</strong></small>
                                    <small class="text-50 lh-24pt">{{ year.cnt*(validated+notvalidated) }}%</small>
                                </p>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" :style="'width:'+year.cnt*(validated+notvalidated)+'%;'" :aria-valuenow="year.cnt" aria-valuemin="0" aria-valuemax="10"></div>
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
                        <div class="w-auto ml-sm-auto table flex align-items-center mb-2 mb-sm-0">
                            <div class="form-inline">
                                <label class="mr-sm-2 form-label" for="inlineFormFilterBy">Filter by:</label>
                                <input v-model="search"
                                       type="text"
                                       class="flex w-auto form-control search mb-2 mr-sm-2 mb-sm-0"
                                       id="inlineFormFilterBy"
                                       placeholder="Search ...">
                                <select id="inlineFormRole" class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="selected">
                                    <option value="all">All Students</option>
                                    <option value="validated">Validated</option>
                                    <option value="notvalidated">Not Validated</option>
                                </select>
                            </div>
                        </div>
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
                    <ListPagination :links="student.meta.links"/>
                    <!-- End Card-->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import AvatarName from "../Components/AvatarName.vue";
import ListCard from '../../Shared/StudentListCard.vue';
import ListPagination from "../../Shared/ListPagination.vue";
import {ref,watch} from "vue";
import  debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";
import {loadScript} from "vue-plugin-load-script";

loadScript('/js/sidebar-mini.js');
loadScript('/js/app.js');
loadScript('/js/script.js');
loadScript('/js/preloader.js');

let props = defineProps({
    student: Object,
    validated: Number,
    notvalidated:Number,
    displayTotal: Object,
    filters:Object,
})

let search = ref(props.filters.search);
let selected = ref(props.filters.selected);

function checkLvl($year){
    switch ($year){
        case '1':
            return '1st Year'
            break;
        case '2':
            return '2nd Year'
            break;
        case '3':
            return '3rd Year'
            break;
        case '4':
            return '4th Year'
            break;
        case '5':
            return '5th Year'
            break;
        case '6':
            return '6th Year'
            break;
        case '7':
            return '7th Year'
            break;
        case '8':
            return '8th Year'
            break;
        case '9':
            return '9th Year'
            break;
        case '10':
            return '10th Year'
            break;
    }
}

watch([search, selected], debounce(function (value){
    Inertia.get(
        '/student',
        {search: value[0],selected: value[1]},
        {
            preserveState: true,
            replace: true
        })
}, 500))
</script>
