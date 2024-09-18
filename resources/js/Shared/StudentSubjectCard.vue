<template>
    <div class="posts-cards mb-24pt">
        <div class="card posts-card" v-for="student in data">
            <div class="posts-card__content d-flex align-items-center flex-wrap" >
                <!--                <div class="avatar avatar-lg mr-3">-->
                <!--                    <a href="">-->

                <!--                        <img src="/images/people/50/guy-2.jpg" alt="avatar" class="avatar-img rounded">-->
                <!--                    </a>-->
                <!--                </div>-->
                <div class="avatar avatar-lg mr-3">
                    <span class="avatar-title rounded-circle">{{ initials(student.fname ) }}{{ initials(student.lname )}}</span>
                </div>
                <div class="posts-card__title flex d-flex flex-column mr-auto">
                    <a class="card-title">{{ student.lname }}, {{ student.fname}} {{ student.initial }}</a>
                    <small class="text-50">{{ student.id }}</small>
                </div>
                <div class="d-flex align-items-center flex-column flex-sm-row posts-card__meta">
<!--                    <div class="text-50 text-uppercase posts-card__tag d-flex align-items-center" style="width: 50vh;">-->
<!--                        <i class="material-icons text-muted-light mr-1">library_books</i> {{ student.registration.cnt}} Subject-->
<!--                    </div>-->
<!--                    <div class="text-50 text-uppercase posts-card__tag d-flex align-items-center" style="width: 50vh;">-->
<!--                        <i class="material-icons text-muted-light mr-1" v-if="student.registration.validation_date != ''">event_available</i> {{ student.registration.validation_date}}-->
<!--                    </div>-->
                </div>
<!--                <div class="dropdown ml-auto">-->
<!--                    <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">-->
<!--                        <i class="material-icons">more_vert</i>-->
<!--                    </a>-->
<!--                    <div class="dropdown-menu dropdown-menu-right">-->
<!--                        <Link :href="'/student/'+student.id" class="dropdown-item">View</Link>-->
<!--                        <Link :href="'/student/'+student.id+'/edit/'" class="dropdown-item">Edit</Link>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, watch} from "vue";
import debounce from "lodash/debounce";
import {Inertia} from "@inertiajs/inertia";

let props = defineProps({
    data: Object,
})


function initials(data){
    const name = data;
    return `${name[0].charAt(0)}`;
}

</script>

