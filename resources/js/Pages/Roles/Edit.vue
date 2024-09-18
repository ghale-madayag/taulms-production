<template>
    <Head>
        <title>Edit Role</title>
    </Head>
    <Breadcrumbs pageTitle="Edit Role" parentTitle="Roles"/>
    <div class="container page__container mt-4">
        <div class="col-md-8">
            <div class="page-separator">
                <div class="page-separator__text">Basic information</div>
            </div>
            <Alert v-if="flash.success" :msg="msg"/>
            <label class="form-label">Title</label>
            <div class="form-group mb-24pt">
                <input v-model="form.title" type="text" :class="{ 'is-invalid': form.errors.title }" class="form-control form-control-lg" placeholder="" style="text-transform: capitalize;">
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
                                <input type="checkbox" class="custom-control-input" :checked="permission.check" ref="checkboxes" :id="permission.id" :value="permission.id">
                                <label class="custom-control-label" style="text-transform: capitalize;" :for="permission.id">{{ permission.name }}</label>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="card-footer p-8pt">
                    <div class="button-list text-right">
                        <button class="btn btn-primary" @click="publish" type="button" :disabled="form.processing" :class="{ 'is-loading': publishBtn}">Update</button>
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
import Alert from "../../Shared/Alert.vue";
import AlertError from "../../Shared/AlertError.vue";

let msg = ref(false);
let old = ref(props.role.name);
let publishBtn = ref(false);

let props = defineProps({
    role: Object,
    permissions: Object,
    errors: Object,
    flash: Object,
});

let form = useForm('CreateRole',{
    id: props.role.id,
    oldname: old,
    title: props.role.name,
    selectedItems: [],
});

if(form.errors.selectedItems){
    msg.value = form.errors.selectedItems;

    setTimeout(() => {
        msg.value = false;
    },10000)
}else if(props.flash.success){
    msg.value = props.flash.success;

    setTimeout(() => {
        msg.value = false;
    },10000)
}else{
    console.log(props.flash.error);
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

    form.post('/roles/'+form.id+'/edit-role',{
        onStart: () => { publishBtn.value = true },
        onFinish: () => { publishBtn.value = false},
        onSuccess:() => {
            if(props.flash.success){
                form.selectedItems = [];
                msg.value = props.flash.success;
                old.value = form.title;
            }else if(props.flash.error){
                msg.value = props.flash.error;
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
</style>