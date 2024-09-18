<template>
    <Head>
        <title>Criteria</title>
    </Head>
    <Breadcrumbs pageTitle="Criteria" parentTitle="Courses"/>
    <div class="container page__container">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
        <div class="page-section">
            <div class="row">
                <div class="col-md-4">
                    <form id="category" @submit.prevent="">
                        <div class="card">
                            <!-- <div class="card-header">
                                <strong class="card-title">Category</strong>
                            </div> -->
                            <div class="card-header d-flex align-items-center">
                                <a href="javascript:void(0);" class="card-title flex mr-12pt">Category</a>
                                <Link :href="'/grades/'+subjects.slug+'/'+section.id+'/'+section.term_id+'/lecture/midterm'" class="btn btn-light btn-sm">View Grades</Link>
                            </div>
                            <div class="card-body">
                                <AlertError v-if="flash.error" :msg="msg"/>
                                <Alert v-if="flash.success" :msg="msg"/>
                                <div class="form-group mb-4">
                                    <label class="form-label" for="cat_name">Name</label>
                                    <input class="form-control" v-model="form.name" :class="{ 'is-invalid': form.errors.name }" type="text" id="cat_name" />
                                    <div class="invalid-feedback">{{ form.errors.name }}</div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="form-label" for="percent">Percentage</label>
                                    <input class="form-control" v-model="form.percentage" :class="{ 'is-invalid': form.errors.percentage }" type="number" id="percent" />
                                    <div class="invalid-feedback">{{ form.errors.percentage }}</div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Select Category</label>
                                    <select class="form-control custom-select" v-model="form.category" :class="{ 'is-invalid': form.errors.category }">
                                        <option value="Lecture">Lecture</option>
                                        <option value="Laboratory" v-if="isLabFunc()">Laboratory</option>
                                    </select>
                                    <div class="invalid-feedback">{{ form.errors.category }}</div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Select Term</label>
                                    <select class="form-control custom-select" v-model="form.tabs" :class="{ 'is-invalid': form.errors.tabs }">
                                        <option value="Midterm">Midterm</option>
                                        <option value="Finals">Finals</option>
                                    </select>
                                    <div class="invalid-feedback">{{ form.errors.tabs }}</div>
                                </div>
                            </div>
                            <div class="card-footer text-left">
                                <div class="button-list">
                                    <button class="btn btn-primary" @click="publish" :disabled="form.processing" :class="{ 'is-loading': publishBtn}"  type="button"><i class="material-icons">add</i> Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-8">
                    <div class="card card-form d-flex flex-column flex-sm-row mb-lg-32pt">
                            <div class="card-form__body card-body-form-group flex">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Lecture</label>
                                            <div v-if="isLabFunc()" class="h3 mb-0">{{ marks ? marks.lecture : 40 }}%</div>
                                            <div v-else class="h3 mb-0">{{ marks ? marks.lecture : 100 }}%</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3" v-if="isLabFunc()">
                                        <div class="form-group">
                                            <label>Laboratory</label><br>
                                            <div class="h3 mb-0">{{ marks ? marks.laboratory : 60 }}%</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Passing</label>
                                            <div class="h3 mb-0">{{ marks ? marks.passing : 60 }}%</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group" style="width: 200px;">
                                            <label>Cut-Off</label>
                                            <div class="h3 mb-0">{{ marks ? marks.cutoff : 75 }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown ml-auto">
                                    <div class="p-card-header">
                                    <a href="#" data-toggle="dropdown" data-caret="false"><i class="material-icons text-50">more_horiz</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="javascript:void(0)" @click="editPassing()" :href="'#'" class="dropdown-item">Edit</a>
                                        <a href="javascript:void(0)" @click="resetPassing()" :href="'#'" class="dropdown-item text-danger">Reset to Default</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="alert alert-info">
                        <div class="d-flex flex-wrap">
                            <div class="mr-8pt"><i class="material-icons">info</i></div>
                            <div class="flex" style="min-width:180px;"><small class="text-100">
                                <strong>Note</strong><br><span>You can arrange the categories by dragging them</span></small>
                            </div>
                        </div>
                    </div>
                    <!-- draggable -->
                    <div class="page-separator mt-32pt">
                        <div class="page-separator__text">Lecture</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mt-0 mb-0">
                                <div class="card-header">
                                    <div class="d-flex align-items-center mb-16pt">
                                        <div class="flex">
                                            <strong class="card-title">Midterm</strong>
                                        </div>
                                        <div class="flex" style="max-width: 100%">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" :class="progressBarClass(midSum())" role="progressbar" :style="'width:'+ midSum() +'%'">{{ midSum() }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="list-group-flush mb-0">
                                        <div class="mt-0 mb-0" v-if="categoriesMidLec.length === 0">
                                            <div class="card-body">
                                                <p class="mb-0 text-center"><strong class="text-50">No Criteria Found</strong></p>
                                            </div>
                                        </div>
                                        <draggable 
                                            :list="categoriesMidLec"
                                            group="lecture"
                                            itemKey="id"
                                            v-bind="dragOptions"
                                            @update="lecMidMove"
                                            :component-data="{
                                                tag: 'ul',
                                                type: 'transition-group',
                                                name: 'flip-list'
                                            }"
                                            handle=".handle"
                                            >
                                            <template #item="{element, index}">
                                                <div class="accordion js-accordion list-group-flush">
                                                    <div class="accordion__item rounded-0">
                                                        <a href="jascript:void(0);" style="cursor: grab;" class="handle accordion__toggle collapsed p-0" :data-target="'#course-toc-'+element.id" data-parent="#parent">
                                                            <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                                                <div class="media-body">
                                                                    <div class="d-flex flex-column">
                                                                        <p class="mb-0"><strong class="card-title">{{element.percentage}}%</strong></p>
                                                                        <small class="js-lists-values-employee-email text-50">
                                                                            {{ element.name }}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown ml-auto">
                                                                <a href="#" data-toggle="dropdown" data-target="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="javascript:void(0);" @click="add(element.id,element.name)" class="dropdown-item">Add {{ element.name }}</a>
                                                                    <a href="javascript:void(0)" @click="editCat(element.id,element.name,element.percentage,element.tabs,element.category)" class="dropdown-item">Edit</a>
                                                                    <a href="javascript:void(0)" @click="destroyCat(element.id)" class="dropdown-item text-danger">Delete</a>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div v-for="sub in element.student_grade_sub" class="accordion__menu collapse show mb-8pt" :id="'course-toc-'+element.id" style="">
                                                            <div class="accordion__menu-link">
                                                                <div class="dropdown ml-auto">
                                                                    <a href="#" data-toggle="dropdown" data-target="false" class="text-muted"><i class="material-icons icon-24pt">more_vert</i> </a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <a href="javascript:void(0)" @click="editSub(sub.id,sub.student_grade_cat_id,sub.name,sub.over)" :href="'#'" class="dropdown-item">Edit</a>
                                                                        <a href="javascript:void(0)" @click="destroySub(sub.id)" class="dropdown-item text-danger">Delete</a>
                                                                    </div>
                                                                </div>
                                                                <strong class="flex">&nbsp; {{ sub.name }}</strong>
                                                                <strong>Score: {{ sub.over }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="accordion_menu collapse show" :id="'course-toc-'+element.id" style="text-align: center;">
                                                            <button @click="add(element.id,element.name)" type="button" class="btn btn-light btn-sm">
                                                                <i class="material-icons icon--left">add</i> Add
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </draggable>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mt-0 mb-0">
                                <div class="card-header">
                                    <div class="d-flex align-items-center mb-16pt">
                                        <div class="flex">
                                            <strong class="card-title">Finals</strong>
                                        </div>
                                        <div class="flex" style="max-width: 100%">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" :class="progressBarClass(finSum())" role="progressbar" :style="'width:'+ finSum() +'%'">{{ finSum() }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="list-group-flush mb-0">
                                        <div class="mt-0 mb-0" v-if="categoriesFinLec.length === 0">
                                            <div class="card-body">
                                                <p class="mb-0 text-center"><strong class="text-50">No Criteria Found</strong></p>
                                            </div>
                                        </div>
                                        <draggable 
                                            :list="categoriesFinLec"
                                            group="lecture"
                                            itemKey="id"
                                            v-bind="dragOptions"
                                            @update="lecFinMove"
                                            :component-data="{
                                                tag: 'ul',
                                                type: 'transition-group',
                                                name: 'flip-list'
                                            }"
                                            handle=".handle"
                                            >
                                            <template #item="{element, index}">
                                                <div class="accordion js-accordion list-group-flush">
                                                    <div class="accordion__item rounded-0">
                                                        <a href="jascript:void(0);" style="cursor: grab;" class="handle accordion__toggle collapsed p-0" :data-target="'#course-toc-'+element.id" data-parent="#parent">
                                                            <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                                                <div class="media-body">
                                                                    <div class="d-flex flex-column">
                                                                        <p class="mb-0"><strong class="card-title">{{element.percentage}}%</strong></p>
                                                                        <small class="js-lists-values-employee-email text-50">
                                                                            {{ element.name }}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown ml-auto">
                                                                <a href="#" data-toggle="dropdown" data-target="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="javascript:void(0);" @click="add(element.id,element.name)" class="dropdown-item">Add {{ element.name }}</a>
                                                                    <a href="javascript:void(0)" @click="editCat(element.id,element.name,element.percentage,element.tabs,element.category)" class="dropdown-item">Edit</a>
                                                                    <a href="javascript:void(0)" @click="destroyCat(element.id)" class="dropdown-item text-danger">Delete</a>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div v-for="sub in element.student_grade_sub" class="accordion__menu collapse show mb-8pt" :id="'course-toc-'+element.id" style="">
                                                            <div class="accordion__menu-link">
                                                                <div class="dropdown ml-auto">
                                                                    <a href="#" data-toggle="dropdown" data-target="false" class="text-muted"><i class="material-icons icon-24pt">more_vert</i> </a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <a href="javascript:void(0)" @click="editSub(sub.id,sub.student_grade_cat_id,sub.name,sub.over)" :href="'#'" class="dropdown-item">Edit</a>
                                                                        <a href="javascript:void(0)" @click="destroySub(sub.id)" class="dropdown-item text-danger">Delete</a>
                                                                    </div>
                                                                </div>
                                                                <strong class="flex">&nbsp; {{ sub.name }}</strong>
                                                                <strong>Score: {{ sub.over }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="accordion_menu collapse show" :id="'course-toc-'+element.id" style="text-align: center;">
                                                            <button @click="add(element.id,element.name)" type="button" class="btn btn-light btn-sm">
                                                                <i class="material-icons icon--left">add</i> Add
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </draggable>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-separator mt-32pt" v-if="isLabFunc()">
                        <div class="page-separator__text">Laboratory</div>
                    </div>
                    <div class="row" v-if="isLabFunc()">
                        <div class="col-md-6">
                            <div class="card mt-0 mb-0">
                                <div class="card-header">
                                    <div class="d-flex align-items-center mb-16pt">
                                        <div class="flex">
                                            <strong class="card-title">Midterm</strong>
                                        </div>
                                        <div class="flex" style="max-width: 100%">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" :class="progressBarClass(midSumLab())" role="progressbar" :style="'width:'+ midSumLab() +'%'">{{ midSumLab() }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="list-group-flush mb-0">
                                        <div class="mt-0 mb-0" v-if="categoriesMidLec.length === 0">
                                            <div class="card-body">
                                                <p class="mb-0 text-center"><strong class="text-50">No Criteria Found</strong></p>
                                            </div>
                                        </div>
                                        <draggable 
                                            :list="categoriesMidLab"
                                            group="lecture"
                                            itemKey="id"
                                            v-bind="dragOptions"
                                            @update="labMidMove"
                                            :component-data="{
                                                tag: 'ul',
                                                type: 'transition-group',
                                                name: 'flip-list'
                                            }"
                                            handle=".handle"
                                            >
                                            <template #item="{element, index}">
                                                <div class="accordion js-accordion list-group-flush">
                                                    <div class="accordion__item rounded-0">
                                                        <a href="jascript:void(0);" style="cursor: grab;" class="handle accordion__toggle collapsed p-0" :data-target="'#course-toc-'+element.id" data-parent="#parent">
                                                            <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                                                <div class="media-body">
                                                                    <div class="d-flex flex-column">
                                                                        <p class="mb-0"><strong class="card-title">{{element.percentage}}%</strong></p>
                                                                        <small class="js-lists-values-employee-email text-50">
                                                                            {{ element.name }}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown ml-auto">
                                                                <a href="#" data-toggle="dropdown" data-target="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="javascript:void(0);" @click="add(element.id,element.name)" class="dropdown-item">Add {{ element.name }}</a>
                                                                    <a href="javascript:void(0)" @click="editCat(element.id,element.name,element.percentage,element.tabs,element.category)" class="dropdown-item">Edit</a>
                                                                    <a href="javascript:void(0)" @click="destroyCat(element.id)" class="dropdown-item text-danger">Delete</a>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div v-for="sub in element.student_grade_sub" class="accordion__menu collapse show mb-8pt" :id="'course-toc-'+element.id" style="">
                                                            <div class="accordion__menu-link">
                                                                <div class="dropdown ml-auto">
                                                                    <a href="#" data-toggle="dropdown" data-target="false" class="text-muted"><i class="material-icons icon-24pt">more_vert</i> </a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <a href="javascript:void(0)" @click="editSub(sub.id,sub.student_grade_cat_id,sub.name,sub.over)" :href="'#'" class="dropdown-item">Edit</a>
                                                                        <a href="javascript:void(0)" @click="destroySub(sub.id)" class="dropdown-item text-danger">Delete</a>
                                                                    </div>
                                                                </div>
                                                                <strong class="flex">&nbsp; {{ sub.name }}</strong>
                                                                <strong>Score: {{ sub.over }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="accordion_menu collapse show" :id="'course-toc-'+element.id" style="text-align: center;">
                                                            <button @click="add(element.id,element.name)" type="button" class="btn btn-light btn-sm">
                                                                <i class="material-icons icon--left">add</i> Add
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </draggable>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mt-0 mb-0">
                                <div class="card-header">
                                    <div class="d-flex align-items-center mb-16pt">
                                        <div class="flex">
                                            <strong class="card-title">Finals</strong>
                                        </div>
                                        <div class="flex" style="max-width: 100%">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" :class="progressBarClass(finSumLab())" role="progressbar" :style="'width:'+ finSumLab() +'%'">{{ finSumLab() }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="list-group-flush mb-0">
                                        <div class="mt-0 mb-0" v-if="categoriesFinLec.length === 0">
                                            <div class="card-body">
                                                <p class="mb-0 text-center"><strong class="text-50">No Criteria Found</strong></p>
                                            </div>
                                        </div>
                                        <draggable 
                                            :list="categoriesFinLab"
                                            group="lecture"
                                            itemKey="id"
                                            v-bind="dragOptions"
                                            @update="labFinMove"
                                            :component-data="{
                                                tag: 'ul',
                                                type: 'transition-group',
                                                name: 'flip-list'
                                            }"
                                            handle=".handle"
                                            >
                                            <template #item="{element, index}">
                                                <div class="accordion js-accordion list-group-flush">
                                                    <div class="accordion__item rounded-0">
                                                        <a href="jascript:void(0);" style="cursor: grab;" class="handle accordion__toggle collapsed p-0" :data-target="'#course-toc-'+element.id" data-parent="#parent">
                                                            <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                                                <div class="media-body">
                                                                    <div class="d-flex flex-column">
                                                                        <p class="mb-0"><strong class="card-title">{{element.percentage}}%</strong></p>
                                                                        <small class="js-lists-values-employee-email text-50">
                                                                            {{ element.name }}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown ml-auto">
                                                                <a href="#" data-toggle="dropdown" data-target="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="javascript:void(0);" @click="add(element.id,element.name)" class="dropdown-item">Add {{ element.name }}</a>
                                                                    <a href="javascript:void(0)" @click="editCat(element.id,element.name,element.percentage,element.tabs,element.category)" class="dropdown-item">Edit</a>
                                                                    <a href="javascript:void(0)" @click="destroyCat(element.id)" class="dropdown-item text-danger">Delete</a>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div v-for="sub in element.student_grade_sub" class="accordion__menu collapse show mb-8pt" :id="'course-toc-'+element.id" style="">
                                                            <div class="accordion__menu-link">
                                                                <div class="dropdown ml-auto">
                                                                    <a href="#" data-toggle="dropdown" data-target="false" class="text-muted"><i class="material-icons icon-24pt">more_vert</i> </a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <a href="javascript:void(0)" @click="editSub(sub.id,sub.student_grade_cat_id,sub.name,sub.over)" :href="'#'" class="dropdown-item">Edit</a>
                                                                        <a href="javascript:void(0)" @click="destroySub(sub.id)" class="dropdown-item text-danger">Delete</a>
                                                                    </div>
                                                                </div>
                                                                <strong class="flex">&nbsp; {{ sub.name }}</strong>
                                                                <strong>Score: {{ sub.over }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="accordion_menu collapse show" :id="'course-toc-'+element.id" style="text-align: center;">
                                                            <button @click="add(element.id,element.name)" type="button" class="btn btn-light btn-sm">
                                                                <i class="material-icons icon--left">add</i> Add
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </draggable>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import CourseHeader from '../../Shared/CourseHeader';
import {useForm} from "@inertiajs/inertia-vue3";
import {ref, watch, computed} from "vue";
import Alert from "../../Shared/Alert.vue";
import AlertError from "../../Shared/AlertError.vue";
import debounce from "lodash/debounce";
import Swal from 'sweetalert2/dist/sweetalert2';
import Draggable from 'vuedraggable';

const dragOptions = computed(() => ({
  animation: 200,
  group: 'lecture',
}));

let msg = ref(false);
let publishBtn = ref(false);

let props = defineProps({
        auth: Object,
        user: Object,
        subjects: Object,
        section: Object, 
        categoriesMidLec: Object,
        categoriesMidLab: Object,
        categoriesFinLec: Object,
        categoriesFinLab: Object,
        errors: Object,
        flash: Object,
        marks: Object,
        isLab: String,
    })

let form = useForm({
    subject_id: props.subjects.id,
    section_id:props.section.id,
    percentage:'',
    name:'',
    tabs:'',
    category:'',
});

let catEdit = useForm({
    subject_id: props.subjects.id,
    section_id:props.section.id,
    percentage:'',
    name:'',
    tabs:'',
    id:'',
    category:'',
});

let subCat = useForm({
    student_grade_cat_id:'',
    name:'',
    over: '',
})

let subCatEdit = useForm({
    id: '',
    student_grade_cat_id:'',
    name:'',
    over: '',
})

let mark = useForm({
    lecture:'',
    laboratory:'',
    passing:'',
    cutoff: '',
    subject_id: props.subjects.id,
    section_id:props.section.id,
    user_id: props.user.id,
    term_id: props.section.term_id,
})

let lecPos = useForm({
    position:[],
})

let loading_btn = '';

if(loading_btn){
    loading_btn = 'btn btn-primary mr-2 is-loading';
}else{
    loading_btn = 'btn btn-primary mr-2';
}

let swalBtn = Swal.mixin({
    customClass: {
        confirmButton: loading_btn,
        cancelButton: 'btn btn-link',
        container: 'modal-criteria',
    },
    buttonsStyling: false
})

let swalDestroy = Swal.mixin({
    customClass: {
        confirmButton: loading_btn,
        cancelButton: 'btn btn-link',
        container: 'modal-destroy',
    },
    buttonsStyling: false
})

if(props.flash.error){
    msg.value = props.flash.error;

    setTimeout(() => {
        msg.value = false;
    },10000)
}else{
    msg.value = props.flash.success;

    setTimeout(() => {
        msg.value = false;
    },10000)
}

function lecMidMove(event){
    props.categoriesMidLec.map((lecture, index) => {
        lecture.position = index + 1
    })

    lecPos.position = props.categoriesMidLec;

    lecPos.put('/criteria/sort',{
        preserveScroll:true,
        onSuccess:()=>{
            lecPos.reset();
        }
    })
}  

function lecFinMove(event){
    props.categoriesFinLec.map((lecture, index) => {
        lecture.position = index + 1
    })

    lecPos.position = props.categoriesFinLec;

    lecPos.put('/criteria/sort',{
        preserveScroll:true,
        onSuccess:()=>{
            lecPos.reset();
        }
    })
}  

function labMidMove(event){
    props.categoriesMidLab.map((lecture, index) => {
        lecture.position = index + 1
    })

    lecPos.position = props.categoriesMidLab;

    lecPos.put('/criteria/sort',{
        preserveScroll:true,
        onSuccess:()=>{
            lecPos.reset();
        }
    })
}  

function labFinMove(event){
    props.categoriesFinLab.map((lecture, index) => {
        lecture.position = index + 1
    })

    lecPos.position = props.categoriesFinLab;

    lecPos.put('/criteria/sort',{
        preserveScroll:true,
        onSuccess:()=>{
            lecPos.reset();
        }
    })
}  

function midSum(){
    return props.categoriesMidLec.reduce((sum, category) => sum + parseInt(category.percentage), 0);
}

function finSum(){
    return props.categoriesFinLec.reduce((sum, category) => sum + parseInt(category.percentage), 0);
}

function midSumLab(){
    return props.categoriesMidLab.reduce((sum, category) => sum + parseInt(category.percentage), 0);
}

function finSumLab(){
    return props.categoriesFinLab.reduce((sum, category) => sum + parseInt(category.percentage), 0);
}

function progressBarClass(percent) {
      const sum = percent;
      if (sum === 100) {
        return 'bg-primary';
      } else if (sum < 75) {
        return 'bg-danger';
      } else {
        return 'bg-warning';
      }
    }

function isLabFunc(){
    if(props.isLab > 0){
        return true;
    }else{
        return false;
    }
}

function editPassing(){
    
    let lab = '';

    if(isLabFunc()){
        lab =   `<div class="form-group mb-4">
                    <label class="form-label" for="lecture">Lecture</label>
                    <input class="form-control" type="number" id="lecture" @input="editLec" v-model="${mark.lecture}"  value="${props.marks ? props.marks.lecture : '40'}" required/>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label" for="laboratory">Laboratory  <span class="text-50">(Auto Compute)</span></label>
                    <input class="form-control" type="number" id="laboratory" v-model="${mark.laboratory}" value="${props.marks ? props.marks.laboratory : '60'}" required disabled/>
                </div>`;
    }

    let content = `
                <form id="mark-form">
                    ${lab}
                    <div class="form-group mb-4">
                        <label class="form-label" for="passing">Passing</label>
                        <input class="form-control" type="number" id="passing" value="${props.marks ? props.marks.passing : '60'}" required/>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label" for="cutoff">Cut-Off</label>
                        <input class="form-control" type="number" id="cutoff" value="${props.marks ? props.marks.cutoff : '75'}" required />
                    </div>
                </form>
                `;

    swalBtn.fire({
        title: 'Edit',
        html: content,
        showCancelButton: true,
        confirmButtonText: 'Update',
        didOpen: () => {
        // Access the input field within the modal and attach an event listener
            const lectureInput = document.getElementById('lecture');
            lectureInput.addEventListener('input', this.editLec);
        },
        preConfirm: () => {
            const lecInput  = isLabFunc() ? document.getElementById('lecture') : 100;
            const labInput  = isLabFunc() ? document.getElementById('laboratory') : 0;
            const passingInput  = document.getElementById('passing');
            const cutoffInput  = document.getElementById('cutoff');

            const lecture = isLabFunc() ? lecInput.value : 100;
            const laboratory = isLabFunc() ? labInput.value : 0;
            const passing = passingInput.value;
            const cutoff = cutoffInput.value;

            console.log(laboratory);

            if (!passing || !cutoff) {
                Swal.showValidationMessage('All fields are required');
            }
            
            return {
                lecture: lecture,
                laboratory: laboratory,
                passing: passing,
                cutoff: cutoff,
            };
            
        }
    }).then((result) => {
        if (result.isConfirmed) {
      
            mark.lecture = result.value.lecture;
            mark.laboratory = result.value.laboratory;
            mark.passing = result.value.passing;
            mark.cutoff = result.value.cutoff;

            console.log(mark);

            mark.post('/criteria/mark',{
                onStart: () => { 
                    publishBtn.value = true 
                },
                onFinish: () => { 
                    publishBtn.value = false
                },
                onSuccess:() => {
                    if(props.flash.success){
                        msg.value = props.flash.success;
                    }else{
                        msg.value = props.flash.error;
                    }
                    
                    setTimeout(() => {
                        msg.value = false;
                    },10000)
                },
                onError:() => {
                   
                },
                preserveScroll: true,
            });
        }
    })

}

function editLec(event){
    let lec = event.target.value;

    let lab = 100 - lec;
    const laboratoryInput = document.getElementById('laboratory'); 
    laboratoryInput.value = lab;
}


function resetPassing(){
    mark.passing = 60;
    mark.cutoff = 75;
    mark.lecture = 40;
    mark.laboratory = 60;

    mark.post('/criteria/mark',{
        onStart: () => { 
            publishBtn.value = true 
        },
        onFinish: () => { 
            publishBtn.value = false
        },
        onSuccess:() => {
            if(props.flash.success){
                    msg.value = props.flash.success;
            }else{
                msg.value = props.flash.error;
            }
            
            setTimeout(() => {
                msg.value = false;
            },10000)
        },
        preserveScroll: true,
    });
}

function publish(){
    let sum = 0;
    if(form.tabs === "Midterm" && form.category === "Lecture"){
        sum = parseInt(form.percentage) + midSum();  
    }else if(form.tabs === "Finals" && form.category === "Lecture"){
        sum = parseInt(form.percentage) + finSum();
    }else if(form.tabs === "Midterm" && form.category === "Laboratory"){
        sum = parseInt(form.percentage) + midSumLab();  
    }else if(form.tabs === "Finals" && form.category === "Laboratory"){
        sum = parseInt(form.percentage) + finSumLab(); 
    }

    if(sum > 100){
        props.flash.error = true;
        msg.value = 'The percentage was exceeded to 100%';
        setTimeout(() => {
            msg.value = false;
        },10000)
    }else{
        form.post('/criteria/category',{
            onStart: () => { 
                publishBtn.value = true 
            },
            onFinish: () => { 
                publishBtn.value = false
            },
            onSuccess:() => {
                if(props.flash.success){
                        msg.value = props.flash.success;
                }else{
                    msg.value = props.flash.error;
                }
                
                setTimeout(() => {
                    msg.value = false;
                    props.flash.success = null;
                },10000)

                form.percentage = '';
                form.name = '';
                form.tabs = '';
                form.category = '';
            },
            preserveScroll: true,
        });
    }
    
}

function editCat(cat_id,cat_name, cat_per,selectedTerm,selectedCat){
    
    let catOption = '';

    if(isLabFunc()){
        catOption = `<option value="Lecture" ${selectedCat === 'Lecture' ? 'selected' : ''}>Lecture</option>
                    <option value="Laboratory" ${selectedCat === 'Laboratory' ? 'selected' : ''}>Laboratory</option>`;
    }else{
        catOption = `<option value="Lecture" ${selectedCat === 'Lecture' ? 'selected' : ''}>Lecture</option>`;
    }

    let content = `
                <div class="form-group mb-4">
                    <label class="form-label" for="edit_cat_name">Name</label>
                    <input class="form-control" value="`+cat_name+`" type="text" id="edit_cat_name" />
                </div>
                <div class="form-group mb-4">
                    <label class="form-label" for="edit_percentage">Percentage</label>
                    <input class="form-control" value="`+cat_per+`" type="number" id="edit_percentage" />
                </div>
                <div class="form-group mb-4"><label class="form-label">Select Category</label>
                    <select class="form-control custom-select" id="edit_select_cat">
                        ${catOption}
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group mb-4"><label class="form-label">Select Term</label>
                    <select class="form-control custom-select" id="edit_select_term">
                        <option value="Midterm" ${selectedTerm === 'Midterm' ? 'selected' : ''}>Midterm</option>
                        <option value="Finals" ${selectedTerm === 'Finals' ? 'selected' : ''}>Finals</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                `;

    swalBtn.fire({
        title: 'Edit '+ cat_name,
        html: content,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
            const subNameInput = document.getElementById('edit_cat_name');
            const scoreInput = document.getElementById('edit_percentage');
            const selectedTerm = document.getElementById('edit_select_term').value;
            const selectedCat = document.getElementById('edit_select_cat').value;
          
            const name = subNameInput.value;
            const score = scoreInput.value;

            if (!name || !score) {
                Swal.showValidationMessage('All fields are required');
            }

            return {
                id: cat_id,
                tabs: selectedTerm,
                name: name,
                percentage: score,
                category: selectedCat
            }
            
        }
    }).then((result) => {
        if (result.isConfirmed) {
            catEdit.id = result.value.id;
            catEdit.name = result.value.name;
            catEdit.tabs = result.value.tabs;
            catEdit.percentage = result.value.percentage;
            catEdit.category = result.value.category;

            catEdit.post('/criteria/category/'+result.value.id+'/edit',{
                onStart: () => { 
                    publishBtn.value = true 
                },
                onFinish: () => { 
                    publishBtn.value = false
                },
                onSuccess:() => {
                    if(props.flash.success){
                            msg.value = props.flash.success;
                    }else{
                        msg.value = props.flash.error;
                    }
                    
                    setTimeout(() => {
                        msg.value = false;
                    },10000)
                },
                preserveScroll: true,
            });

        }
    })
    
}

function editSub(id,student_grade_cat_id,subCat,score){
    let content = `
                <div class="form-group mb-4">
                    <label class="form-label" for="edit_sub_name">Name</label>
                    <input class="form-control" value="`+subCat+`" type="text" id="edit_sub_name" />
                </div>
                <div class="form-group mb-4">
                    <label class="form-label" for="edit_score">Score</label>
                    <input class="form-control" value="`+score+`" type="number" id="edit_score" />
                </div>
                `;

    swalBtn.fire({
        title: 'Edit '+ subCat,
        html: content,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
            const subNameInput = document.getElementById('edit_sub_name');
            const scoreInput = document.getElementById('edit_score');

            const name = subNameInput.value;
            const score = scoreInput.value;

            if (!name || !score) {
                Swal.showValidationMessage('All fields are required');
            }

            return {
                id: id,
                student_grade_cat_id: student_grade_cat_id,
                name: name,
                score: score
            };
            
        }
    }).then((result) => {

        if (result.isConfirmed) {
            subCatEdit.id = result.value.id;
            subCatEdit.student_grade_cat_id = result.value.student_grade_cat_id;
            subCatEdit.name = result.value.name;
            subCatEdit.over = result.value.score;

            subCatEdit.post('/criteria/subcategory/'+result.value.id+'/edit',{
                onStart: () => { 
                    publishBtn.value = true 
                },
                onFinish: () => { 
                    publishBtn.value = false
                },
                onSuccess:() => {
                    if(props.flash.success){
                            msg.value = props.flash.success;
                    }else{
                        msg.value = props.flash.error;
                    }
                    
                    setTimeout(() => {
                        msg.value = false;
                    },10000)
                },
                preserveScroll: true,
            });
        }
    })
}

function add(id,category){
    let content = `
                <div class="form-group mb-4">
                    <label class="form-label" for="sub_name">Name</label>
                    <input class="form-control" type="text" id="sub_name" />
                </div>
                <div class="form-group mb-4">
                    <label class="form-label" for="score">Total Score</label>
                    <input class="form-control" type="number" id="score" />
                </div>
                `;

    swalBtn.fire({
        title: 'Add '+ category,
        html: content,
        showCancelButton: true,
        confirmButtonText: 'Save',
        preConfirm: () => {
            const subNameInput = document.getElementById('sub_name');
            const scoreInput = document.getElementById('score');

            const name = subNameInput.value;
            const score = scoreInput.value;

            if (!name || !score) {
                Swal.showValidationMessage('All fields are required');
            }

            return {
                id: id,
                name: name,
                score: score
            };
            
        }
    }).then((result) => {
        if (result.isConfirmed) {
            subCat.student_grade_cat_id = result.value.id;
            subCat.name = result.value.name;
            subCat.over = result.value.score;

            subCat.post('/criteria/subcategory',{
                onStart: () => { 
                    publishBtn.value = true 
                },
                onFinish: () => { 
                    publishBtn.value = false
                },
                onSuccess:() => {
                    if(props.flash.success){
                            msg.value = props.flash.success;
                    }else{
                        msg.value = props.flash.error;
                    }
                    
                    setTimeout(() => {
                        msg.value = false;
                    },10000)
                },
                preserveScroll: true,
            });
        }
    })
}

function destroyCat(id){
    swalDestroy.fire({
        title: 'Are you sure?',
        text: "All student scores in this category will be permanently deleted.You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            catEdit.delete('/criteria/category/'+id,{
                preserveScroll:true,
                onSuccess:() => {
                    swalDestroy.fire(
                        'Deleted!',
                        'Your data has been deleted.',
                        'success'
                    )
                }
            });
        }
    })
}

function destroySub(id){
    swalDestroy.fire({
        title: 'Are you sure?',
        text: "All student scores in this category will be permanently deleted. You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            subCat.delete('/criteria/subcategory/'+id,{
                preserveScroll:true,
                onSuccess:() => {
                    swalDestroy.fire(
                        'Deleted!',
                        'Your data has been deleted.',
                        'success'
                    )
                }
            });
        }
    })
}


</script>

<style>

.modal-destroy h2#swal2-title{
    text-align: center;
}

.modal-criteria h2#swal2-title {
    text-align: left;
}

.form-group.mb-4 {
    text-align: left;
}
</style>