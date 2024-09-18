<template>
    <div class="pt-32pt container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
        <div class="flex d-flex flex-column flex-sm-row align-items-center mb-24pt mb-md-0">
            <div class="mb-24pt mb-sm-0 mr-sm-24pt">
                <div class="alert alert-primary" role="alert" v-if="$page.url=='/?verified=1'">
                    <div class="d-flex flex-wrap align-items-start">
                        <div class="mr-8pt">
                            <i class="material-icons">done</i>
                        </div>
                        <div class="flex" style="min-width: 180px">
                            <small class="text-black-100">
                                <strong>Success!</strong> You're email was successfully verified.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="media align-items-center">
                    <a href="" class="media-left mr-16pt">
                        <div class="avatar avatar-lg">
                            <span class="avatar-title rounded-circle">{{ initials(profile.fname) }}{{ initials(profile.lname) }}</span>
                        </div>
                    </a>
                    <div class="media-body">
                        <div class="custom-file">
                            <h2 class="mb-0">{{ profile.fname }} {{ profile.initial }} {{ profile.lname }}  </h2>
                            <small class="js-lists-values-email text-50">{{ profile.id }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" role="tablist">
            <Link :href="'/'+url+'/'+profile.id+'/edit'" class="btn btn-outline-secondary" v-if="$page.component != 'ProfileEdit' && $page.component != 'ProfileEdit'">Edit Profile</Link>
            <div class="col-auto">
                <Link class="btn btn-outline-secondary" v-if="profile.can && profile.role==3">View All</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
let props = defineProps({
    profile:Object,
    url: String,
})

function initials(data){
    const name = data;
    return `${name[0].charAt(0)}`;
}
</script>

