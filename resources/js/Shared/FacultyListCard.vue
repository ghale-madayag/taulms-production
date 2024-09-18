<template>
    <div class="posts-cards mb-24pt">
        <div class="card posts-card" v-for="faculty in data">
            <div class="posts-card__content d-flex align-items-center flex-wrap" >
<!--                <div class="avatar avatar-lg mr-3">-->
<!--                    <a href="">-->

<!--                        <img src="/images/people/50/guy-2.jpg" alt="avatar" class="avatar-img rounded">-->
<!--                    </a>-->
<!--                </div>-->
                <div class="avatar avatar-lg mr-3">
                    <span class="avatar-title rounded-circle">{{ initials(faculty.fname ) }}{{ initials(faculty.lname )}}</span>
                </div>
                <div class="posts-card__title flex d-flex flex-column mr-auto">
                    <a class="card-title">{{ faculty.lname }}, {{ faculty.fname }} {{ faculty.initial }} {{ faculty.extname }}</a>
                    <small class="text-50">{{ faculty.id }}</small>
                </div>
                <div class="d-flex align-items-center flex-column flex-sm-row posts-card__meta">
                    <div class="text-50 text-uppercase posts-card__tag d-flex align-items-center" style="width: 50vh;">
                        <i class="material-icons text-muted-light mr-1" v-if="faculty.schedule.length">today</i> {{ faculty.schedule.length}} Schedule
                    </div>
                </div>
                <div class="dropdown ml-auto">
                    <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                        <i class="material-icons">more_vert</i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <Link :href="'/faculty/'+faculty.id" class="dropdown-item">View</Link>
                        <Link :href="'/faculty/'+faculty.id+'/edit/'" class="dropdown-item">Edit</Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
let props = defineProps({
    data: Object,
})

function initials(data){
    const name = data;
    return `${name[0].charAt(0)}`;
}

</script>

