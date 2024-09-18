<template>
    <Head>
        <title>Manage Roles</title>
    </Head>
    <Breadcrumbs pageTitle='Manage Roles'/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <div class="row card-group-row mb-lg-8pt">

                <div class="col-sm-3 card-group-row__col" v-for="role in role_cnt">
                    <div class="card card-sm stack stack--1 card-group-row__card">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <div class="flex">
                                    <div class="d-flex align-items-center">
                                        <div class="flex">
                                            <div class="h2 mb-0">{{ role.users_count }}</div>
                                            <strong class="text-50" style="text-transform: capitalize;">Total {{ role.name }}</strong>
                                        </div>
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
                        <div class="page-separator__text">List of Users</div>
                    </div>
                    <div class="card mb-0">
                        <div class="table-responsive"
                             data-toggle="lists"
                             data-lists-sort-by="js-lists-values-employee-name"
                             data-lists-values='["js-lists-values-employee-name", "js-lists-values-employer-name", "js-lists-values-projects", "js-lists-values-activity", "js-lists-values-earnings"]'>

                            <div class="card-header">
                                <form class="form-inline">
                                    <label class="mr-sm-2 form-label"
                                           for="inlineFormFilterBy">Filter by:</label>
                                    <input v-model="search" type="text"
                                           class="form-control search mb-2 mr-sm-2 mb-sm-0"
                                           id="inlineFormFilterBy"
                                           placeholder="Search ...">

                                    <label class="sr-only"
                                           for="inlineFormRole">Role</label>
                                    <select v-model="roles" id="inlineFormRole"
                                            class="custom-select mb-2 mr-sm-2 mb-sm-0" style="text-transform: capitalize;">
                                            <option value="0" selected="selected">All Roles</option>
                                            <option v-for="role in select_roles" :value="role"> {{ role }}</option>
                                    </select>
                                </form>
                            </div>
                            <table class="table mb-0 thead-border-top-0 table-nowrap">
                                <thead>
                                    <tr>
                                        <th>
                                            <a href="javascript:void(0)"
                                               class="sort"
                                               data-sort="js-lists-values-employee-name">Employee</a>
                                        </th>
                                        <th style="width: 37px;">Role</th>
                                        <th style="width: 24px;"
                                            class="pl-0"></th>
                                    </tr>
                                </thead>
                                <tbody class="list"
                                       id="staff">
                                    <tr class="selected" v-for="user in users.data">
                                        <td>
                                            <div class="media flex-nowrap align-items-center"
                                                 style="white-space: nowrap;">
                                                <div class="avatar avatar-sm mr-8pt">
                                                    <div class="avatar avatar-sm mr-8pt">
                                                        <span class="avatar-title rounded-circle">{{ user.fl }}</span>
                                                    </div>
                                                </div>
                                                <div class="media-body">
                                                    <div class="d-flex flex-column">
                                                        <p class="mb-0"><strong class="js-lists-values-employee-name">{{ user.full_name }}</strong></p>
                                                        <small class="js-lists-values-employee-email text-50">{{ user.id }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);"
                                               class="chip chip-outline-secondary">{{ user.role_name }}</a>
                                        </td>
                                        <td class="text-right pl-0">
                                            <a href="javascript:void(0);" @click="update(user.id, user.role_id)"
                                               class="text-50"><i class="material-icons">more_vert</i></a>
                                               <!-- <button @click="openDialog">Open Dialog</button> -->
                                        </td>
                                    </tr>
                                    
                                    <tr v-if="users.data.length == 0" class="selected">
                                        <td class="text-center">
                                            <h6 class="text-50">No Data Available</h6>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer p-8pt d-inline-block">

                            <ul class="pagination justify-content-start pagination-xsm m-0">
                                <li v-for="link in users.links" class="page-item" :class="link.url ? '' : 'disabled'">
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
import Breadcrumbs from "../Components/Breadcrumbs";
import {ref, watch} from "vue";
import debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import {useForm} from "@inertiajs/inertia-vue3";

let props = defineProps({
    users: Object,
    select_roles: Object,
    role_cnt: Object,
    filters: Object,
    select2: Object,
})

let search = ref(props.filters.search);
let roles = ref(props.filters.roles);
let form = useForm();

watch([search, roles], debounce(function (value){
    Inertia.get(
        '/roles',
        {search: value[0],roles: value[1]},
        {
            preserveScroll:true,
            preserveState: true,
            replace: true
        })
}, 500))


const options = props.select_roles;


const swalBtn = Swal.mixin({
    customClass: {
        container: 'modal-class',
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false,
})

function update(id, role){
    
    swalBtn.fire({
        title: 'Select a Role',
        input: 'select',
        inputOptions: options,
        inputPlaceholder: 'Select Role',
        showCancelButton: true,
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
          if (value == role) {
            return 'You cannot select the same option twice';
          }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.post('/roles/'+result.value+'/user/'+id,{
                preserveScroll:true,
                onSuccess:() => {
                    swalBtn.fire(
                        'Success!',
                        'Data has been saved.',
                        'success'
                    )
                }
            });
        }
    })
}


</script>

<style>
.modal-class .swal2-popup.swal2-modal.swal2-show {
    padding: 2% 3%;
}

.modal-class select.swal2-select {
    border-radius: 5px;
    background: #fbfbfb;
    text-transform: capitalize;
    font-size: 16px;
}

.modal-class select.swal2-select option {
    text-transform: capitalize;
}

</style>