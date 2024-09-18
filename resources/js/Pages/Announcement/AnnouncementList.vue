<template>
    <Head>
        <title>Announcement</title>
    </Head>
    <Breadcrumbs pageTitle='Announcement' :auth="auth.permission"/>
    <div class="container page__container">
        <div class="page-section">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>
            <div class="row card-group-row mb-lg-8pt">
                <div class="col-lg-4 card-group-row__col">
                    <div class="card card-group-row__card d-flex flex-column">
                        <div class="row no-gutters flex">
                            <div class="col-6">
                                <div class="card-body">
                                    <h6 class="text-50">Overview</h6>

                                    <div class="h2 mb-0">{{ total }}</div>
                                    <div class="d-flex flex-column">
                                        <strong>Total</strong>
                                        <small class="text-50">This Semester</small>
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
                                <label class="mr-sm-2 form-label" for="inlineFormFilterBy">Filter by:</label>
                                <input v-model="search"
                                       type="text"
                                       class="flex w-auto form-control search mb-2 mr-sm-2 mb-sm-0"
                                       id="inlineFormFilterBy"
                                       placeholder="Search ...">
                            </div>
                        </div>
                    </div>
                    <!--Card-->
                    <div class="alert alert-warning mb-0" role="alert" v-if="!announcement.data.length">
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
                    </div>
                    <ListCard @delete="destroy" :data="announcement.data" :auth="auth.permission"/>
                    <ListPagination :links="announcement.links"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumbs from "../Components/Breadcrumbs";
import ListCard from '../../Shared/AnnouncementCard.vue';
import {ref, watch} from "vue";
import debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import {useForm} from "@inertiajs/inertia-vue3";
import ListPagination from "../../Shared/ListPagination";

let form = useForm();

let props = defineProps({
    auth: Object,
    announcement:Object,
    total: Number,
    filters: Object,
})

let search = ref(props.filters.search);

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link'
    },
    buttonsStyling: false
})

watch(search, debounce(function (value){
    Inertia.get(
        '/announcement',
        {search: value},
        {
            preserveState: true,
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
            form.delete('/announcement/list/'+id,{
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

