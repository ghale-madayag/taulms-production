<template>
     <Head>
        <title>Report Grade</title>
    </Head>
    <Breadcrumbs pageTitle="Report Grades" parentTitle="Courses"/>
    <div class="container page__container">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
        <div class="page-section">
            <AlertError v-if="flash.error" :msg="msg"/>
            <Alert v-if="flash.success" :msg="msg"/>
            <div class="col-md-12 p-0">
                <div class="card p-0 nav">
                    <div class="row no-gutters flex" role="tablist">
                        <div class="col-auto">
                            <div class="p-card-header d-flex align-items-center" v-if="tabs !=='finalgrade'">
                                <div class="h2 mb-0 mr-3" style="text-transform: capitalize;">{{ category }} - {{ tabs }}</div>
                            </div>
                            <div class="p-card-header d-flex align-items-center" v-else>
                                <div class="h2 mb-0 mr-3">Final Grade</div>
                            </div>
                        </div>
                        <div class="col-auto border-left">
                            <div class="p-card-header d-flex align-items-center">
                                <div class="h2 mb-0 mr-3">{{ passedCount }}</div>
                                <div class="flex">
                                    <p class="card-title">Passed</p>
                                    <p class="card-subtitle text-50" style="text-transform: capitalize;" v-text="tabs != 'finalgrade' ? tabs : 'Final Grade'"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto border-left">
                            <div class="p-card-header d-flex align-items-center">
                                <div class="h2 mb-0 mr-3 text-danger">{{ failedCount  }}</div>
                                <div class="flex">
                                    <p class="card-title text-danger">Failed</p>
                                    <p class="card-subtitle text-danger" style="text-transform: capitalize;" v-text="tabs != 'finalgrade' ? tabs : 'Final Grade'"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto ml-sm-auto">
                            <div class="dropdown ml-auto">
                                    <div class="p-card-header">
                                    <a href="#" data-toggle="dropdown" data-caret="false"><i class="material-icons text-50">more_horiz</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <Link v-if="auth.permission.includes('create all lesson')" :href="'/criteria/'+subjects.slug+'/'+section.id+'/'+section.term_id+'/criteria'" :preserve-scroll="true" class="dropdown-item"><i class="material-icons">settings</i> &nbsp; Settings</Link>
                                        <Link v-else-if="auth.permission.includes('create lesson')"  :href="'/criteria/'+subjects.slug+'/'+section.id+'/'+section.term_id+'/criteria'" :preserve-scroll="true" class="dropdown-item"><i class="material-icons">settings</i> &nbsp; Settings</Link>
                                        <a href="javascript:void(0)" @click="exportToExcel()" :href="'#'" class="dropdown-item"><i class="material-icons text-50">file_download</i>Export to Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card dashboard-area-tabs p-relative mb-0">
                <div class="card-header">
                    <div class="form-inline">
                        <label class="mr-sm-2 form-label" for="inlineFormFilterBy">Filter by:</label>

                        <select id="inlineFormCat" class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="inlineFormCat" @change="redirectToLink" v-if="tabs != 'finalgrade'">
                            <option value="lecture">Lecture</option>
                            <option value="laboratory" v-if="isLab !=='0.0'">Laboratory</option>
                        </select>

                        <select id="inlineFormTab" class="custom-select mb-2 mr-sm-2 mb-sm-0" v-model="inlineFormTab" @change="redirectToLink">
                            <option value="midterm">Midterm</option>
                            <option value="finals">Finals</option>
                            <option value="finalgrade">Final Grade</option>
                        </select>

                        <div class="ml-auto mb-2 mb-sm-0 custom-control-inline mr-0" v-if="tabs != 'finalgrade'">
                            <button v-if="criteria.length != 0" @click="toggleEditingMode" class="btn btn-link text-primary"><i class="material-icons">edit</i> &nbsp;{{ editingMode ? 'Saved Changes' : 'Bulk Edit' }}</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 thead-border-top-0 table-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 150px;">
                                    <a href="javascript:void(0)"
                                        class="sort"
                                        data-sort="js-lists-values-project">Name</a>
                                </th>
                                <th style="width: 48px;" v-for="ctr in criteria">
                                    <a href="javascript:void(0)"
                                        class="sort"
                                        data-sort="js-lists-values-lead">{{ ctr.name }}/{{ ctr.over }}</a>
                                </th>
                                <th style="width: 48px;" v-if="tabs === 'finalgrade'">
                                    <a href="javascript:void(0)"
                                        class="sort"
                                        data-sort="js-lists-values-status" v-text="isLab == '0.0' ? 'Midterm' : 'Lecture'"></a>
                                </th>
                                <th style="width: 48px;" v-if="tabs === 'finalgrade'">
                                    <a href="javascript:void(0)"
                                        class="sort"
                                        data-sort="js-lists-values-status" v-text="isLab == '0.0' ? 'Finals' : 'Laboratory'"></a>
                                </th>
                                <th style="width: 48px;">
                                    <a href="javascript:void(0)"
                                        class="sort"
                                        data-sort="js-lists-values-status">Grade</a>
                                </th>
                            </tr>
                        </thead>
                       
                        <tbody class="list"
                                id="projects" v-for="(student,index) in students" :key="index">
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small class="js-lists-values-budget"><strong>{{ student.Name }}</strong></small>
                                    </div>
                                </td>
                                
                                <td v-for="ctr in criteria">
                                    <div class="d-flex flex-column" v-if="!editingMode">
                                        <small class="js-lists-values-budget"><strong>{{ student[ctr.name]['total_score'] }}</strong></small>
                                    </div>
                                    <div class="d-flex flex-column" v-else>
                                        <input class="form-control" v-model="student[ctr.name]['total_score']" @blur="saveEdit(index, ctr, student[ctr.name]['id'],student.id)"/>
                                    </div>
                                </td>
                                <td v-if="tabs==='finalgrade'">
                                    <div class="d-flex flex-column">
                                        <small class="js-lists-values-budget" :class="student.midtermGrade > 3.0 ? 'text-danger' : 'text-100'" ><strong>{{ student.midtermGrade }}</strong></small>
                                    </div>
                                </td>
                                <td v-if="tabs==='finalgrade'">
                                    <div class="d-flex flex-column">
                                        <small class="js-lists-values-budget" :class="student.fintermGrade > 3.0 ? 'text-danger' : 'text-100'" ><strong>{{ student.fintermGrade }}</strong></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column mr-auto">
                                        <small class="js-lists-values-budget" :class="student.grade > 3.0 ? 'text-danger' : 'text-100'" ><strong>{{ student.grade }}</strong></small>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import CourseHeader from '../../Shared/CourseHeader';
import {useForm} from "@inertiajs/inertia-vue3";
import { Inertia } from '@inertiajs/inertia'
import Alert from "../../Shared/Alert.vue";
import AlertError from "../../Shared/AlertError.vue";
import {ref, computed } from "vue";
import * as XLSX from 'xlsx';
import jsPDF from 'jspdf';


let msg = ref(false);
const editingMode =  ref(false);
let selectedTab = '';

let props = defineProps({
        auth: Object,
        user: Object,
        subjects: Object,
        section: Object, 
        criteria: Object,
        students: Object,
        flash: Object,
        tabs: String,
        midtermGrade: Number,
        fintermGrade: Number,
        category: String,
        isLab: String,
    })

let form = useForm({
    score:0,
    scoreId:'',
    stud: '',
});

if(props.tabs === 'midterm'){
    selectedTab = 'Midterm'
}else if(props.tabs === 'finals'){
    selectedTab = 'Finals'
}else{
    selectedTab = 'Final Grade'
}

const inlineFormCat = ref('lecture');
const inlineFormTab = ref('midterm');

const redirectToLink = (event) => {
  const ElinlineFormTab = inlineFormTab.value;
  const ElinlineFormCat = inlineFormCat.value;
  
  const url = '/grades/'+props.subjects.slug+'/'+props.section.id+'/'+props.section.term_id+'/'+ElinlineFormCat+'/'+ElinlineFormTab;

  Inertia.visit(url, { preserveState: true,preserveScroll: true })


}

function formatDataForExport(studentsData) {
  return studentsData.map(student => {
    const formattedStudent = {
      Name: student.Name,
      // Add other properties here as needed
    };

    // Add scores from different categories (Assign, Lab, Quiz, etc.)

    for (const categoryKey in student) {
      if (
        categoryKey !== 'id' &&
        categoryKey !== 'Name' &&
        categoryKey !== 'grade' &&
        student[categoryKey].total_score !== undefined
      ) {
        //formattedStudent[categoryKey] = student[categoryKey].total_score;
        const category = categoryKey + '/' + student[categoryKey].over;
        formattedStudent[category] = student[categoryKey].total_score;
      }
    }

    if(props.tabs === 'finalgrade'){
        formattedStudent['Midterm'] = student['midtermGrade'];
        formattedStudent['Finals'] = student['fintermGrade'];
    }

    if (student['grade'] !== undefined) {
      formattedStudent['Grade'] = student['grade'];
    }

    return formattedStudent;
  });
}

function exportToExcel() {
  const formattedData = formatDataForExport(props.students);
  const ws = XLSX.utils.json_to_sheet(formattedData);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Students');
  XLSX.writeFile(wb, props.subjects.code+'_'+props.section.name+'_'+props.tabs+'.xlsx');
}


const passedCount = computed(() =>
  Object.values(props.students).filter(student => parseFloat(student.grade) <= 3.0).length
);

const failedCount = computed(() =>
  Object.values(props.students).filter(student => parseFloat(student.grade) > 3.0).length
);



//console.log(passedCount);

function toggleEditingMode() {
    editingMode.value = !editingMode.value;
}


function saveEdit(index, field, id, stud){
    const over = parseInt(props.students[index][field.name]['over']);
    form.score = parseInt(props.students[index][field.name]['total_score']);

    if(form.score > over){
        props.flash.error = 'Invalid value';
        msg.value = props.flash.error;
    }else{
        form.score = props.students[index][field.name]['total_score'];

        form.scoreId = id;
        form.stud = stud;

        form.post('/grade/upsert',{
            preserveScroll: true,
            onStart: () => { 
                //publishBtn.value = true 
            },
            onFinish: () => { 
                //publishBtn.value = false
            },
            onSuccess:() => {
                //props.flash.success = 'Successfully Saved';
               //msg.value = props.flash.success;
            }
        });
    }

    setTimeout(() => {
        msg.value = false;
    },5000)
    

}
</script>