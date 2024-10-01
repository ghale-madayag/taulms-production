<template>
    <Head>
        <title>Lesson</title>
    </Head>
     <Breadcrumbs pageTitle="Lesson" parentTitle="Courses"/>
     <div class="container page__container">
        <CourseHeader :subjects="subjects" :section="section" :auth="auth" :user="user"/>
        <div class="page-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mt-0 mb-0 rounded-0">
                        <div class="card-header">
                            <form class="form-inline">
                                <label class="mr-sm-2 form-label" for="filter_name">Filter By</label>
                                <input v-model="lesson" id="filter_name" type="text" class="form-control search mb-2 mr-sm-2 mb-sm-0" placeholder="Search by title">
                                <select v-model="status" id="custom-select" class="form-control custom-select">
                                    <option value="0" selected="selected">All</option>
                                    <option value="1">Pubished</option>
                                    <option value="2">Draft</option>
                                </select>
                                <div class="ml-auto mb-2 mb-sm-0 custom-control-inline mr-0">
                                    <div class="col-sm-auto">
                                        <label>Published</label>
                                        <h3>{{ published }}</h3>
                                    </div>
                                    <div class="col-sm-auto">
                                        <label>Draft</label>
                                        <h3>{{ pending }}</h3>
                                    </div>
                                    <div class="col-sm-auto">
                                        <label>Total</label>
                                        <h3>{{ total }}</h3>
                                    </div>
                                </div>
                                <div class="ml-auto mb-2 mb-sm-0 custom-control-inline mr-0">
                                    <a :preserve-scroll="true" class="btn bg-link mr-8pt" href="#" @click="duplicate(subjects.id,section.id)"><i class="material-icons">content_copy</i> &nbsp; Copy to</a>
                                    <Link v-if="auth.permission.includes('create all lesson')" :href="'/lesson/'+subjects.slug+'/'+section.id+'/'+user.id+'/create'" :preserve-scroll="true" class="btn bg-primary text-white"><i class="material-icons">add</i> Create New</Link>
                                    <Link v-else-if="auth.permission.includes('create lesson')"  :href="'/lesson/'+subjects.slug+'/'+section.id+'/create'" :preserve-scroll="true" class="btn bg-primary text-white"><i class="material-icons">add</i> Create New</Link>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Midterm</h5>
                                <div class="d-flex align-items-center">
                                    <span class="mr-2 text-primary card-subtitle">Mark as Completed</span>
                                    <div class="custom-control custom-checkbox-toggle custom-control-inline mr-2">
                                        <input 
                                            type="checkbox" 
                                            id="mid_completed" 
                                            class="custom-control-input"
                                            v-model="isMidtermCompleted" 
                                            @change="saveMidtermState(lessons.lesson_mid.at(-1)?.id || null)"
                                        >
                                        <label class="custom-control-label" for="mid_completed"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-flush mb-0">
                        <draggable
                            :list="lessons.lesson_mid"
                            group="lesson"
                            @add="onAdd($event,1)"
                            itemKey="id"
                            @update="midMove"
                            v-bind="dragOptions"
                            :component-data="{
                                tag: 'ul',
                                type: 'transition-group',
                                name: 'flip-list'
                            }"
                            handle=".handle"
                        >
                            <template #item="{element, index}">
                                <div :key="element.id" :data-id="element.id" :data-index="index" class="list-group-item d-flex flex-column flex-sm-row align-items-sm-center px-12pt">
                                    <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                        <span v-if="lessons.lesson_mid?.at(-1)?.is_midterm_completed == 0" class="handle material-icons mr-2 text-muted" style="cursor: n-resize;">drag_handle</span>
                                        <span v-else class="material-icons mr-2 text-muted" style="cursor: n-resize;">do_not_disturb_on</span>
                                        <div class="avatar avatar-sm mr-8pt">
                                            <div class="avatar avatar-sm mr-8pt">
                                                <img :src="element.thumbnail ? '/storage/'+element.thumbnail : '/images/blank.webp'" alt="Avatar" class="avatar-img rounded">
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <div class="d-flex flex-column">
                                                <p class="mb-0"><strong class="js-lists-values-employee-name">{{element.title }}</strong></p>
                                                <small class="js-lists-values-employee-email text-50">{{ element.formatted_date }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column" style="width:100px">
                                        <div class="mr-3 text-50 text-uppercase posts-card__tag d-flex align-items-center">
                                            <i class="material-icons text-20 icon-20pt">attach_file</i> {{ element.attachement_count }}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column" style="width:100px">
                                        <div v-if="element.published==1" class="d-flex flex-column">
                                            <small class="js-lists-values-status text-50 mb-4pt">Pubished</small>
                                            <span class="indicator-line rounded bg-primary"></span>
                                        </div>
                                        <div v-else class="d-flex flex-column">
                                            <small class="js-lists-values-status text-50 mb-4pt">Draft</small>
                                            <span class="indicator-line rounded bg-warning"></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="dropdown ml-auto">
                                            <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <Link v-if="auth.permission.includes('edit lesson')" :href="'/lesson/'+element.slug+'/edit/'" class="dropdown-item">Edit</Link>
                                                <!-- <Link v-if="auth.permission.includes('edit lesson')" href="#" @click="duplicate(element.id)" class="dropdown-item">Duplicate</Link> -->
                                                <a v-if="auth.permission.includes('delete lesson')" href="#" class="dropdown-item text-danger" @click="destroy(element.id)">
                                                    Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                        <div v-if="lessons.lesson_mid == 0" class="card mt-0 mb-0 rounded-0">
                            <div class="card-body">
                                <p class="mb-0 text-center"><strong class="text-50">No Data Found</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-0 mb-0 rounded-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Finals</h5>
                                <div class="d-flex align-items-center">
                                    <span class="mr-2 text-primary card-subtitle">Mark as Completed</span>
                                    <div class="custom-control custom-checkbox-toggle custom-control-inline mr-2">
                                        <input 
                                            type="checkbox" 
                                            id="fin_completed" 
                                            class="custom-control-input"
                                            v-model="isFinalCompleted" 
                                            @change="saveFinalState (lessons.lesson_fin.at(-1)?.id || null)"
                                        >
                                        <label class="custom-control-label" for="fin_completed"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-flush">
                        <draggable
                            :list="lessons.lesson_fin"
                            group="lesson"
                            @add="onAdd($event,2)"
                            itemKey="id"
                            @update="finMove"
                            v-bind="dragOptions"
                            :component-data="{
                                tag: 'ul',
                                type: 'transition-group',
                                name: 'flip-list'
                            }"
                            handle=".handle"
                        >
                            <template #item="{element, index}">
                                <div :key="element.id" :data-id="element.id" :data-index="index" class="list-group-item d-flex flex-column flex-sm-row align-items-sm-center px-12pt">
                                    <div class="media flex align-items-center mr-sm-16pt mb-8pt mb-sm-0">
                                        <span v-if="lessons.lesson_fin?.at(-1)?.is_finals_completed == 0" class="handle material-icons mr-2 text-muted" style="cursor: n-resize;">drag_handle</span>
                                        <span v-else class="material-icons mr-2 text-muted" style="cursor: n-resize;">do_not_disturb_on</span>
                                        <div class="avatar avatar-sm mr-8pt">
                                            <div class="avatar avatar-sm mr-8pt">
                                                <img :src="element.thumbnail ? '/storage/'+element.thumbnail : '/images/blank.webp'" alt="Avatar" class="avatar-img rounded">
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <div class="d-flex flex-column">
                                                <p class="mb-0"><strong class="js-lists-values-employee-name">{{element.title }}</strong></p>
                                                <small class="js-lists-values-employee-email text-50">{{ element.formatted_date }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column" style="width:100px">
                                        <div class="mr-3 text-50 text-uppercase posts-card__tag d-flex align-items-center">
                                            <i class="material-icons text-20 icon-20pt">attach_file</i> {{ element.attachement_count }}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column" style="width:100px">
                                        <div v-if="element.published==1" class="d-flex flex-column">
                                            <small class="js-lists-values-status text-50 mb-4pt">Pubished</small>
                                            <span class="indicator-line rounded bg-primary"></span>
                                        </div>
                                        <div v-else class="d-flex flex-column">
                                            <small class="js-lists-values-status text-50 mb-4pt">Draft</small>
                                            <span class="indicator-line rounded bg-warning"></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="dropdown ml-auto">
                                            <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted"><i class="material-icons">more_vert</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <Link v-if="auth.permission.includes('edit lesson')" :href="'/lesson/'+element.slug+'/edit'" class="dropdown-item">Edit</Link>
                                                <!-- <Link v-if="auth.permission.includes('edit lesson')" href="#" @click="duplicate(element.id)" class="dropdown-item">Duplicate</Link> -->
                                                <a v-if="auth.permission.includes('delete lesson')" href="#" class="dropdown-item text-danger" @click="destroy(element.id)">
                                                    Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                        
                        <div v-if="lessons.lesson_fin == 0" class="card mt-0 mb-0 rounded-0">
                            <div class="card-body">
                                <p class="mb-0 text-center"><strong class="text-50">No Data Found</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import draggable from "vuedraggable";
export default {
    components: {
        draggable
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                group: "lesson",
            };
        }
    }
}
</script>
<script setup>
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import CourseHeader from '../../Shared/CourseHeader';
import {useForm} from "@inertiajs/inertia-vue3";
import {ref,watch,computed} from "vue";
import  debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import axios from 'axios';

let props = defineProps({
    auth: Object,
    subjects: Object,
    section: Object,
    user: Object,
    lessons: Object,
    filters:Object,
    total: Number,
    published: Number,
    pending: Number,
    flash: Object
})

const isMidtermCompleted = ref(false);
const isFinalCompleted = ref(false);

if (props.lessons.lesson_mid?.at(-1)?.is_midterm_completed == 1) {
    isMidtermCompleted.value = true;
} else {
    isMidtermCompleted.value = false;
}

if(props.lessons.lesson_fin?.at(-1)?.is_finals_completed == 1){
    isFinalCompleted.value = true;
}else{
    isFinalCompleted.value = false;
}


let lessForm = useForm({
    position: [],
    term:[],
    id:[],
})

let form = useForm();

const isTerm = useForm({
    id:null,
    is_midterm_completed: isMidtermCompleted.value,
    is_finals_completed: isFinalCompleted.value,
});

// Function to save toggle state to the server
const saveMidtermState = (id) => {
    isTerm.id = id;
    isTerm.is_midterm_completed = isMidtermCompleted.value;

    isTerm.put('/lesson/ismidterm/'+id, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('State updated successfully!');
        },
        onError: () => {
            console.error('Failed to update state.');
        },
    });
};

const saveFinalState  = (id) => {
    isTerm.id = id;
    isTerm.is_finals_completed = isFinalCompleted.value;

    isTerm.put('/lesson/isfinals/'+id, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('State updated successfully!');
        },
        onError: () => {
            console.error('Failed to update state.');
        },
    });
};

// Watch for changes in isMidtermCompleted and update the form value
watch(isMidtermCompleted, (newValue) => {
    isTerm.is_midterm_completed = newValue;
});

watch(isFinalCompleted, (newValue) => {
    isTerm.is_finals_completed = newValue;
});

let dup = useForm({
    subject: '',
    section: '',
    currentSection: '',
});

let publishBtn = ref(false);

let status = ref(props.filters.status);
let lesson = ref(props.filters.lesson);

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link',
        container: 'modal-lesson',
    },
    buttonsStyling: false
})


watch([lesson, status], debounce(function (value){
    Inertia.get(
        '/lesson/'+props.subjects.slug+'/'+props.section.id,
        {lesson: value[0], status: value[1]},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        })
}, 500))

function destroy(id) {
    swalBtn.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete('/lesson/'+props.subjects.slug+'/'+props.section.id+'/'+id,{
                preserveScroll:true,
                onSuccess:() => {
                    swalBtn.fire(
                        'Deleted!',
                        'Your data has been deleted.',
                        'success'
                    )
                }
            });
        }
    })
}

function midMove(event){
    props.lessons.lesson_mid.map((lesson, index) =>{
        lesson.position = index + 1
    })
    lessForm.position = props.lessons.lesson_mid;

    lessForm.put('/lesson/sort',{
        preserveScroll:true,
        onSuccess:()=>{
            lessForm.reset();
        }
    });
}

function finMove(event){
    props.lessons.lesson_fin.map((lesson, index) =>{
        lesson.position = index + 1
    })
    
    lessForm.position = props.lessons.lesson_fin;

    lessForm.put('/lesson/sort',{
        preserveScroll:true,
        onSuccess:()=>{
            lessForm.reset();
        }
    });
}

function onAdd(event,term){
   if(term==1){
       props.lessons.lesson_mid.map((lesson, index) =>{
           lesson.term = term;
           lesson.position = index + 1
       });
       lessForm.position = props.lessons.lesson_mid;

       lessForm.put('/lesson/sort',{
           preserveScroll:true,
           onSuccess:()=>{
               lessForm.reset();
           }
       });
   }else{
       props.lessons.lesson_fin.map((lesson, index) =>{
           lesson.term = term;
           lesson.position = index + 1
       });
       lessForm.position = props.lessons.lesson_fin;

       lessForm.put('/lesson/sort',{
           preserveScroll:true,
           onSuccess:()=>{
               lessForm.reset();
           }
       });
   }

}

function duplicate(subject,section){
    
   const lesMid = props.lessons.lesson_mid.length;
   const lesFin = props.lessons.lesson_fin.length;
   console.log(lesFin);

   if (lesMid > 0 || lesFin) {

        axios.get('/recorder/getSubject')
            .then(response => {
                let options = response.data.subject;
                options = options.filter(option => option.section.id !== section);
                let selectOptions = options.map(option => `<option value="${option.section.id}">${option.section.name+' - '+option.title}</option>`).join('');
                
                let content = `
                <p>Choose a section to copy the lesson.</p>
                <select id="selectSection" class="form-control custom-select mb-4">
                    ${selectOptions}
                </select>
                `;
                this.swalBtn.fire({
                    title: 'Please select your class',
                    html: content,
                    showCancelButton: true,
                    confirmButtonText: 'Copy',
                    preConfirm: () => {
                        const sectionId = document.getElementById('selectSection').value;
                        return {
                            sectionId: sectionId
                        }
                    }
                }).then((result) => {
                    
                    if (result.isConfirmed) {
                        dup.subject = subject;
                        dup.section = result.value.sectionId;
                        dup.currentSection = section;

                        dup.post('/lesson/duplicate',{
                            onStart: () => { 
                                swalBtn.fire({
                                    title: 'Please wait!',
                                    html: 'Don\'t close or refresh your browser while copying the data',
                                    timer: 3600000,
                                    timerProgressBar: true,
                                    allowOutsideClick: false,
                                    backdrop: 'static',
                                    didOpen: () => {
                                        this.swalBtn.showLoading()
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval)
                                    }
                                }).then((result) => {
                                    if (result.dismiss === swalBtn.DismissReason.timer) {
                                        console.log('I was closed by the timer')
                                    }
                                })
                               // publishBtn.value = true 
                            },
                            onFinish: () => { 
                                //publishBtn.value = false
                            },
                            onSuccess:() => {
                                swalBtn.fire(
                                    'Saved!',
                                    'Lessons are  successfully copied as Draft. Please publish them',
                                    'success'
                                )

                            },
                            onError:() =>{
                                swalBtn.fire(
                                    'Error!',
                                    'There was an error. Please try again later.',
                                    'error'
                                )
                            },
                            preserveScroll: true,
                        });
                    }
                });

            })
            .catch(error => {
                console.error(error);
            });
   }else{
     swalBtn.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'No lessons found!',
     })
   }

   
}
</script>

<style>

.modal-lesson .swal2-popup.swal2-modal.swal2-show {
    padding: 2% 3%;
}

.modal-lesson select#select {
    font-size: 16px;
}
</style>