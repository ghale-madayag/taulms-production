<template>
    <Head>
        <title>All Courses</title>
    </Head>
    <Breadcrumbs pageTitle="All Courses"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <div class="row card-group-row mb-lg-8pt">
                <div class="col-lg-4 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="card-body">
                                <h6 class="text-50">Overview</h6>

                                <div class="h2 mb-0">{{ total }}</div>
                                <div class="d-flex flex-column">
                                    <strong>Total Subject</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="page-separator">
                        <div class="page-separator__text">Filter By</div>
                    </div>
                    <div class="card mb-0">
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label class="form-label" for="inlineFormSubject">Subject</label>
                                    <div class="search-form search-form--dark">
                                        <input v-model="subject" type="text" class="form-control" placeholder="Search subject..." id="inlineFormSubject">
                                        <button class="btn" type="button"><i class="material-icons">search</i></button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="inlineFormFaculty">Faculty</label>
                                    <div class="search-form search-form--dark">
                                        <input v-model="faculty" type="text" class="form-control" placeholder="Search faculty..." id="inlineFormFaculty">
                                        <button class="btn" type="button"><i class="material-icons">search</i></button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="inlineFormSection">Section</label>
                                    <div class="search-form search-form--dark">
                                        <input v-model="section" type="text" class="form-control" placeholder="Search section..." id="inlineFormSection">
                                        <button class="btn" type="button"><i class="material-icons">search</i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="page-separator">
                        <div class="page-separator__text">Courses List</div>
                    </div>
                    <div class="card mb-0">
                        <div class="list-group list-group-flush">
                            <div v-for="subject in subjects.data" class="list-group-item d-flex flex-column flex-sm-row align-items-sm-center px-12pt">
                                <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                    <div class="avatar avatar-sm mr-8pt">
                                        <div class="avatar avatar-sm mr-8pt">
                                            <img :src="subject.subject.thumbnail ? '/storage/'+subject.subject.thumbnail : '/images/blank.webp'" alt="Avatar" class="avatar-img rounded">
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div class="d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-employee-name">{{subject.subject.title }}</strong></p>
                                            <small class="js-lists-values-employee-email text-50">{{ subject.user.full_name }} | {{ subject.subject.code }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-column flex-sm-row posts-card__meta">
                                    <div class="mr-3 text-50 text-uppercase posts-card__tag d-flex align-items-center">
                                        <i class="material-icons text-muted-light mr-1">school</i> {{ subject.section.name }}
                                    </div>
                                    <div class="mr-3 text-50 posts-card__date">
                                        <small>{{ subject.sched_date }}</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="dropdown ml-auto">
                                        <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <Link :href="'/subject/'+subject.subject.slug+'/'+subject.section_id+'/'+subject.user_id" class="dropdown-item">View</Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="subjects.data == 0" class="list-group-item">
                                <p class="mb-0 text-center"><strong class="text-50">No Data Found</strong></p>
                            </div>
                        </div>
                        <div class="card-footer p-8pt d-inline-block">
                            <ul class="pagination justify-content-start pagination-xsm m-0">
                                <li v-for="link in subjects.links" class="page-item" :class="link.url ? '' : 'disabled'">
                                    <Component :is="link.url ? 'Link' : 'span'" :class="link.active ? 'text-white bg-primary' : ''" class="page-link" :href="link.url" preserve-scroll>
                                        <span v-html="link.label"></span>
                                    </Component>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import {ref,watch} from "vue";
import  debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";
import { useForm } from '@inertiajs/inertia-vue3';
import Select2 from 'vue3-select2-component';
import "/vendor/select2/select2.min.css";
import "/css/select2.css";

let props = defineProps({
    subjects: Object,
    filters:Object,
    total: Number,
})

let subject = ref(props.filters.search);
let faculty = ref(props.filters.faculty);
let section = ref(props.filters.section);

watch([subject, faculty, section], debounce(function (value){
    Inertia.get(
        '/subject/all',
        {subject: value[0],faculty: value[1], section: value[2]},
        {
            preserveState: true,
            replace: true
        })
}, 500))


</script>

<style>
.select2-container .select2-selection--single .select2-selection__rendered {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    display: list-item;
    flex-wrap: wrap;
}
</style>

