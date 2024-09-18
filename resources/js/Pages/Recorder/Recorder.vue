<template>
    <Head>
        <title>Time Records</title>
    </Head>
    <Breadcrumbs pageTitle='Time Records'/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <div class="row card-group-row mb-lg-8pt">
                <div class="col-lg-4 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-12">
                                <div class="card-body">
                                    <h6 class="text-50">Total Hours</h6>

                                    <div class="h2 mb-0">{{ totalDay }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Today</strong>
                                        <small class="text-50"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-12">
                                <div class="card-body">
                                    <h6 class="text-50">Total Hours</h6>

                                    <div class="h2 mb-0">{{ totalWeek }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>This Week</strong>
                                        <small class="text-50"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-12">
                                <div class="card-body">
                                    <h6 class="text-50">Total Hours</h6>

                                    <div class="h2 mb-0">{{ totalMonth }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>This Month</strong>
                                        <small class="text-50"></small>
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
                        <div class="page-separator__text">List</div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-24pt" style="white-space: nowrap;">
                        <small class="flex text-muted text-headings text-uppercase mr-3 mb-2 mb-sm-0"></small>
                        <div class="w-auto ml-sm-auto table flex align-items-center mb-2 mb-sm-0">
                            <div class="form-inline">
                                <!-- <label class="mr-sm-2 form-label" for="inlineFormFilterBy">Filter by:</label>
                                <input v-model="search"
                                       type="text"
                                       class="flex w-auto form-control search mb-2 mr-sm-2 mb-sm-0"
                                       id="inlineFormFilterBy"
                                       placeholder="Search ..."> -->
                            </div>
                        </div>
                    </div>
                    <!--Card-->
                    <!-- <div class="alert alert-warning mb-0" role="alert" v-if="!announcement.data.length">
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
                    </div> -->
                    <RecorderdList @delete="destroy" :recorded="recorded.data" :auth="auth"/>
                    <ListPagination :links="recorded.links"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from "../Components/Breadcrumbs";
import RecorderdList from "../../Shared/RecordedList";
import ListPagination from "../../Shared/ListPagination.vue";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import {useForm} from "@inertiajs/inertia-vue3";

let props = defineProps({
    auth: Object,
    recorded: Object,
    totalDay: String,
    totalWeek: String,
    totalMonth: String,
})

let form = useForm();

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

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
            form.delete('/recorder/destroy/'+id,{
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
</script>
