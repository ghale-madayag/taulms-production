<template>
    <Head>
        <title>Create Role</title>
    </Head>
    <Breadcrumbs pageTitle="Create Role" parentTitle="Roles"/>
    <div class="container page__container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="page-separator">
                    <div class="page-separator__text">Role List</div>
                </div>
                <div class="card stack stack--1">
                    <div class="card-header d-flex align-items-center">
                        <div class="alert alert-soft-warning mb-24pt">
                            <div class="d-flex flex-wrap align-items-start">
                                <div class="mr-8pt">
                                    <i class="material-icons">access_time</i>
                                </div>
                                <div class="flex" style="min-width: 180px">
                                    <small class="text-black-100">
                                        <strong>Please Note:</strong><br>
                                        <span>The <strong>Admin</strong>, <strong>Faculty</strong>, and <strong>Student</strong> role are not deletable.</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" v-for="role in roles">
                            <a href="javascript:void(0);" 
                            style="text-transform: capitalize;" 
                            class="text-body d-flex align-items-center">
                                <strong class="flex">{{ role.name }}</strong> 
                                <div class="dropdown"><a href="#" data-toggle="dropdown" data-caret="false" class="text-muted" aria-expanded="false"><i class="material-icons">more_horiz</i></a>
                                    <div class="dropdown-menu dropdown-menu-right" style="">
                                        <Link :href="'/roles/'+role.name+'/edit'" class="dropdown-item">Edit</Link>
                                        <a v-if="role.name != 'student' && role.name !='faculty' && role.name != 'administrator'" 
                                        href="javascript:void(0);" 
                                        class="dropdown-item text-danger"
                                        @click="destroy(role.id)"
                                        >Delete</a>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-8">
                <div class="page-separator">
                    <div class="page-separator__text">Basic information</div>
                </div>
                <label class="form-label">Title</label>
                <div class="form-group mb-24pt">
                    <input v-model="form.title" type="text" :class="{ 'is-invalid': form.errors.title }" class="form-control form-control-lg" placeholder="">
                    <div class="invalid-feedback">{{ form.errors.title }}</div>
                </div>
                <div class="page-separator">
                    <div class="page-separator__text">Please Select Privileges</div>
                </div>
                <div class="card mb-lg-0">
                    <div class="card-header d-flex align-items-center">
                        <div class="custom-control custom-checkbox flex">
                            <input class="custom-control-input" type="checkbox" id="select-all" v-model="allSelected" @change="toggleAllCheckboxes">
                            <label class="custom-control-label" for="select-all">Select All</label>
                        </div>
                        <div><AlertError v-if="flash.error" :msg="msg"/></div>
                        <div><AlertError v-if="form.errors.selectedItems" :msg="msg"/></div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled list-todo column-list" id="todo">
                            <li v-for="(permission, index) in permissions" :key="index">
                                <div class="custom-control custom-checkbox">
                                    <div v-if="index == 0"><h4>Subject</h4></div>
                                    <div v-if="index == 2" class="mt-16pt"><h4>Lesson</h4></div>
                                    <div v-if="index == 11" class="mt-16pt"><h4>Quiz</h4></div>
                                    <div v-if="index == 21" class="mt-16pt"><h4>Password</h4></div>
                                    <div v-if="index == 24" class="mt-16pt"><h4>Email</h4></div>
                                    <div v-if="index == 27" class="mt-16pt"><h4>Announcement</h4></div>
                                    <div v-if="index == 34" class="mt-16pt"><h4>Conference</h4></div>
                                    <div v-if="index == 39" class="mt-16pt"><h4>Students</h4></div>
                                    <div v-if="index == 42" class="mt-16pt"><h4>Time Record</h4></div>
                                    <div v-if="index == 46" class="mt-32pt"><h4>Faculty and Role</h4></div>
                                    <input type="checkbox" class="custom-control-input" ref="checkboxes" :id="permission.id" v-model="form.selectedItems" :value="permission.id">
                                    <label class="custom-control-label" style="text-transform: capitalize;" :for="permission.id">{{ permission.name }}</label>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer p-8pt">
                        <div class="button-list text-right">
                            <button class="btn btn-primary" @click="publish" :disabled="form.processing" :class="{ 'is-loading': publishBtn}" type="button">Create</button>
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
import { ref, watchEffect } from 'vue';
import AlertError from "../../Shared/AlertError.vue";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

let msg = ref(false);
let publishBtn = ref(false);
let titleChecker = ref('');

let props = defineProps({
    permissions: Object,
    roles: Object,
    errors: Object,
    flash: Object,
});

let form = useForm('CreateRole',{
    title: '',
    selectedItems: [],
});

if(form.errors.selectedItems){
    msg.value = form.errors.selectedItems;

    setTimeout(() => {
        msg.value = false;
    },10000)
}else{
    msg.value = props.flash.error;

    setTimeout(() => {
        msg.value = false;
    },10000)
}

function publish(){
    form.selectedItems = [];
    checkboxes.value.forEach((checkbox) => {
        if (checkbox.checked) {
            form.selectedItems.push(checkbox.value);
        }
    });

    form.post('/roles/store',{
        onStart: () => { publishBtn.value = true },
        onFinish: () => { publishBtn.value = false},
        onSuccess:() =>{
            if(props.flash.error){
                msg.value = props.flash.error;
            }else{
                form.selectedItems = [];
            }
            setTimeout(() => {
                msg.value = false;
            },10000)
        },
        onError:() => { 
           if(form.errors.selectedItems){
                msg.value = form.errors.selectedItems;
           }

          
        }
    });
}

function destroy(id){
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
            form.delete('/roles/'+id,{
                preserveScroll:true,
                onSuccess:() => {           
                    swalBtn.fire(
                        'Deleted!',
                        'Your data has been deleted.',
                        'success'
                    )
                },onError:()=>{
                    if(props.errors.message){
                        swalBtn.fire(
                            'Oops...',
                            form.errors.message,
                            'error'
                        )
                    }
                }
            });
        }
    })
}

const allSelected = ref(false);
const checkboxes = ref([]);

// Watch for changes to the checkboxes array and update the allSelected ref accordingly
watchEffect(() => {
  allSelected.value = checkboxes.value.every((checkbox) => checkbox.checked);
});

// Function to toggle all checkboxes
const toggleAllCheckboxes = () => {
   checkboxes.value.forEach((checkbox) => {
        checkbox.checked = allSelected.value;
  });
};

</script>

<style>
    .column-list {
        columns: 2;
        -webkit-columns: 2;
        -moz-columns: 2;
    }

    .list-todo li:not(:last-child) {
        margin-bottom: 0.3rem!important;
    }

    .list-todo label.custom-control-label {
        font-weight: 400!important;
    }

    .list-todo h4{
        font-size: 18px!important;
    }
</style>