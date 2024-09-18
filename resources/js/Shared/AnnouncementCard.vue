<template>
    <div class="posts-cards mb-24pt">
        <div class="card posts-card" v-for="announcement in data">
            <div class="posts-card__content d-flex align-items-center flex-wrap" >
                <!--                <div class="avatar avatar-lg mr-3">-->
                <!--                    <a href="">-->

                <!--                        <img src="/images/people/50/guy-2.jpg" alt="avatar" class="avatar-img rounded">-->
                <!--                    </a>-->
                <!--                </div>-->
                <div class="avatar avatar-lg mr-3">
                    <div class="d-flex flex-column mr-16pt">
                        <small class="text-uppercase text-50">{{ announcement.month }}</small>
                        <strong class="border-bottom-2 border-bottom-accent">{{ announcement.day }}</strong>
                    </div>
                </div>

                <div class="posts-card__title flex d-flex flex-column mr-auto">
                    <a class="card-title">{{ announcement.title }}</a>
                    <small class="text-50" v-html="announcement.full_text.replace(/<br>/g,'').substring(0,40)+'...'"></small>
                </div>
                <div class="d-flex align-items-center flex-column flex-sm-row posts-card__meta">
<!--                    <div class="text-50 text-uppercase posts-card__tag d-flex align-items-center" style="width: 50vh;">-->
<!--                        <i class="material-icons text-muted-light mr-1">library_books</i> {{ student.registration.cnt}} Subject-->
<!--                    </div>-->
<!--                    <div class="text-50 text-uppercase posts-card__tag d-flex align-items-center" style="width: 50vh;">-->
<!--                        <i class="material-icons text-muted-light mr-1" v-if="student.registration.validation_date != ''">event_available</i> {{ student.registration.validation_date}}-->
<!--                    </div>-->
                </div>
                <div class="dropdown ml-auto">
                    <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                        <i class="material-icons">more_vert</i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <Link v-if="auth.includes('edit announcement')" :href="'/announcement/'+announcement.slug+'/edit'" class="dropdown-item">Edit</Link>
                        <Link v-if="auth.includes('delete announcement')" href="#" class="dropdown-item text-danger" @click="destroy(announcement.id)">
                            Delete
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

const emit = defineEmits(['delete']);

let props = defineProps({
    auth: Object,
    data: Object,
})

function initials(data){
    const name = data;
    return `${name[0].charAt(0)}`;
}

function destroy(id) {
    emit('delete', id);
}

</script>

