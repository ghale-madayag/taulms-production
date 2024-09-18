<template>
     <Head>
        <title>Maintenace</title>
    </Head>
    <Breadcrumbs pageTitle="Maintenance"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Current Term</div>
            </div>
            <div class="row pb-24pt">
                <div class="col-lg-10 p-0"> 
                    <AlertError v-if="flash.error" :msg="flash.error"/>
                    <Alert v-if="flash.success" :msg="flash.success"/>
                    <div class="list-group list-group-form">
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-5">Select School Year & Term:</label>
                                <div class="col-sm-4">
                                    <select id="term" class="form-control custom-select">
                                        <option v-for="sy in term" :value="sy.id" :selected="sy.isTerm === '1'">{{ sy.academic_year }} - {{ sy.term }}</option>
                                    </select>
                                </div>
                               <div class="col-sm-3">
                                    <div class="invalid-feedback">{{ termForm.errors.schedule }}</div>
                                    <a href="javascript:void(0)" :disabled="termForm.processing" class="btn btn-outline-secondary" @click="saveTerm()">Update</a>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-separator">
                <div class="page-separator__text">Sync Database</div>
            </div>
            <div class="row pb-24pt">
                <div class="col-lg-10 p-0"> 
                    <div class="alert alert-info" role="alert">
                        <div class="d-flex flex-wrap align-items-start">
                            <div class="flex" style="min-width: 180px">
                                <small class="text-black-100">
                                    <strong>Info:</strong> {{ msg }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-form">
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-4">Sync Schedule :</label>
                                <div class="col-sm-4">
                                    <select id="backup_sched" class="form-control custom-select">
                                        <option selected="">Select</option>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                               <div class="col-sm-4">
                                    <div class="invalid-feedback">{{ form.errors.schedule }}</div>
                                    <a href="javascript:void(0)" class="btn btn-outline-secondary" @click="backup()">Save</a>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 p-0">
                    <div class="page-separator">
                        <div class="page-separator__text">Database Reset :</div>
                    </div>
                    <div class="alert alert-warning">
                        <div class="d-flex flex-wrap">
                            <div class="mr-8pt">
                                <i class="material-icons">info</i>
                            </div>
                            <div class="flex" style="min-width: 180px">
                                <small class="text-100">
                                    <strong>Important</strong><br>
                                    <span>This action will result in the removal of all data currently stored in the database. Please backup your database and files.</span>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-form">
                        <div class="list-group-item">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-form-label form-label col-sm-4">Database Reset</label>
                                <div class="col-sm-8">
                                    <a href="javascript:void(0)" class="btn btn-outline-secondary" @click="reset();" :disabled="form.processing" :class="{ 'is-loading': publishBtn}">Reset</a>
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
import Breadcrumbs from "../Components/Breadcrumbs";
import {useForm} from "@inertiajs/inertia-vue3";
import Alert from "../../Shared/Alert.vue";
import AlertError from "../../Shared/AlertError.vue";
import {ref} from "vue";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

let props = defineProps({
    flash: Object,
    errors: Object,
    msg: Object,
    term: Object,
});

let publishBtn = ref(false);

let form = useForm({
    name: 'maintenance',
    command: 'sync:database',
    interval: '',
    schedule_time: '',
    days_of_week: '',
    day_of_month: '',
    month:''
    
});

let termForm = useForm({
    isTerm: '',
})

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link',
        container: 'modal-maintenance',
    },
    buttonsStyling: false
})


function reset(){
    swalBtn.fire({
        title: 'Are you sure?',
        text: "This action will result in the removal of all data currently stored in the database. Please backup your database and files.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Reset Now!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.post('/migrate/reset-database',{
                onStart: () => { 
                                swalBtn.fire({
                                    title: 'Please wait!',
                                    html: 'Don\'t close or refresh your browser',
                                    timer: 3600000,
                                    timerProgressBar: true,
                                    allowOutsideClick: false,
                                    backdrop: 'static',
                                    didOpen: () => {
                                        swalBtn.showLoading()
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
                                    'Finished!',
                                    ' '+props.flash.success+' ',
                                    'success'
                                )

                            },
                            onError:() =>{
                                swalBtn.fire(
                                    'Error!',
                                    ' '+props.flash.error+' ',
                                    'error'
                                )
                            },
                            preserveScroll: true,
            });
        }
    });

   
}


function backup(){
    const sched = document.getElementById('backup_sched').value;

    switch (sched) {
        case 'daily':
            let content = `
                <div class="form-group">
                    <input type="time" id="timePicker" class="swal2-input">
                </div>
                `;
            
            swalBtn.fire({
                title: 'Select Time',
                html: content,
                showCancelButton: true,
                confirmButtonText: 'Schedule Daily',
                preConfirm: () => {
                    const timeVal = document.getElementById('timePicker').value;
                    return {
                        timeVal: timeVal
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.schedule_time = result.value.timeVal;
                    form.interval = sched;
                    form.post('/migrate/maintenance',{
                        onStart: () => { },
                        onFinish:() => {},
                        onSuccess:() => {},
                        onError:() => {},
                    })
                }
            })

            break;
        case 'weekly':
            let contentWeek = `
                <div class="form-group">
                    <label class="form-label" for="day">Select Day</label>
                    <select id="day" class="form-control custom-select">
                        <option selected="">Select Day</option>
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                        <option value="7">Sunday</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="timePicker">Select Time</label>
                    <input type="time" id="timePicker" class="form-control">
                </div>
                `;
            
            swalBtn.fire({
                title: 'Select Time',
                html: contentWeek,
                showCancelButton: true,
                confirmButtonText: 'Schedule Weekly',
                preConfirm: () => {
                    const dayVal = document.getElementById('day').value;
                    const timeVal = document.getElementById('timePicker').value;
                    return {
                        dayVal: dayVal,
                        timeVal: timeVal
                    }
                }
            }).then((result) => {
                console.log(result)
                if (result.isConfirmed) {

                    form.days_of_week = result.value.dayVal;
                    form.schedule_time = result.value.timeVal;
                    form.interval = sched;

                    form.post('/migrate/maintenance',{
                        onStart: () => { },
                        onFinish:() => {},
                        onSuccess:() => {},
                        onError:() => {},
                    })
                }
            })
            
            break;
        case 'monthly':
            const days = Array.from({ length: 31 }, (_, index) => (index + 1).toString());

            let contentMonth = `
                <div class="form-group">
                <label class="form-label" for="day">Select Day of the Month</label>
                <select id="day" class="form-control custom-select">
                    <option selected>Select Day</option>`;
            
            days.forEach((day) => {
                contentMonth += `<option value="${day}">${day}</option>`;
            });

            contentMonth += `
                </select>
                </div>
                <div class="form-group">
                <label class="form-label" for="timePicker">Select Time</label>
                <input type="time" id="timePicker" class="form-control">
                </div>`;
            
            swalBtn.fire({
                title: 'Select Time',
                html: contentMonth,
                showCancelButton: true,
                confirmButtonText: 'Schedule Weekly',
                preConfirm: () => {
                    const dayVal = document.getElementById('day').value;
                    const timeVal = document.getElementById('timePicker').value;
                    return {
                        dayVal: dayVal,
                        timeVal: timeVal
                    }
                }
            }).then((result) => {
                console.log(result)
                if (result.isConfirmed) {

                    form.day_of_month = result.value.dayVal;
                    form.schedule_time = result.value.timeVal;
                    form.interval = sched;

                    form.post('/migrate/maintenance',{
                        onStart: () => { },
                        onFinish:() => {},
                        onSuccess:() => {},
                        onError:() => {},
                    })
                }
            })
            break;
        default:
            console.log('no value');
            break;
    }
}

function saveTerm(){
    const termInput = document.getElementById('term');
    termForm.isTerm = termInput.value;

    termForm.post('/migrate/maintenance/term',{
            onStart: () => {},
            onFinish:() => {},
            onSuccess:() => {},
            onError:() => {},
        })
}
</script>