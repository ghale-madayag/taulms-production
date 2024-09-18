<template>
    <Head>
        <title>Courses</title>
    </Head>
    <Breadcrumbs pageTitle="Courses"/>
    <div class="container page__container">
        <div class="page-section">
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
                    </div>
                </div>
            </div>
            <div class="page-separator">
                <div class="page-separator__text">Course List</div>
            </div>
            <div class="row card-group-row">
                <SubjectCardFaculty :comp="$page.component" :subjects="subject.data" v-if="auth.role!='student'"/>
                <SubjectCard v-else :subjects="subject.data" :auth="auth.permission"/>
            </div>
            <ListPagination :links="subject.links" class="mb-32pt"/>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import {loadScript} from "vue-plugin-load-script";
import {ref, watch} from "vue";
import  debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";
import ListPagination from "../../Shared/ListPagination.vue";
import SubjectCardFaculty from "../../Shared/SubjectCardFaculty.vue";
import SubjectCard from "../../Shared/SubjectCard.vue";

loadScript('/vendor/popper.min.js');
loadScript('/vendor/bootstrap.min.js');
loadScript('/vendor/perfect-scrollbar.min.js');
loadScript('/vendor/dom-factory.js');
loadScript('/vendor/material-design-kit.js');
loadScript('/js/sidebar-mini.js');
loadScript('/js/app.js');
loadScript('/js/script.js');
loadScript('/js/preloader.js');

let props = defineProps({
    auth: Object,
    subject: Object,
    filters:Object,
})
let search = ref(props.filters.search);

watch(search, debounce(function (value){
    Inertia.get(
        '/subject',
        {search: value},
        {
            preserveState: true,
            replace: true
        })
}, 500))
</script>

