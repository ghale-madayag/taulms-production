<template>
     <div class="posts-cards mb-24pt">
        <div class="card posts-card" v-for="record in recorded">
            <div class="posts-card__content d-flex align-items-center flex-wrap">
                <div class="avatar avatar-lg mr-3 avatar-4by3" style="width:10%;">
                    <div class="video-overlay">
                        <a v-if="record.video" href="javascript:void(0);" @click="playVideo('/storage/screenrecord/converted/'+record.video)"><i class="fa fa-play"></i></a>
                        <a v-else href="javascript:void(0);"><i class="fa fa-video-slash"></i></a>
                    </div>
                    <img :src="record.thumbnail ? '/storage/screenrecord/thumbnail/'+record.thumbnail : '/images/blank.webp'" alt="avatar" class="avatar-img rounded">
                </div>
                <div class="posts-card__title mr-5 flex d-flex flex-column" style="width:45%;">
                    <a class="card-title mr-3" :title="record.subject.title">{{ record.subject.title }}</a>
                    <small class="text-50">{{ record.section.name}}</small>
                </div>
                <div class="posts-card__meta" style="width:27%;">
                    <div class="text-50 text-uppercase">
                             {{  record.formatted_date }}
                    </div>
                </div>
                <div class="posts-card__meta" style="width:15%;text-align: right;">
                    <div class="text-100 posts-card__date">
                        <strong>{{ record.totalHrs }}</strong>
                    </div>
                </div>
                <div v-if="auth.permission.includes('delete all time records')" class="dropdown ml-auto" style="width:3%;text-align: right;">
                    <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                        <i class="material-icons">more_vert</i>
                    </a>
                   <div class="dropdown-menu dropdown-menu-right">
                       <a href="javascript:void(0)" @click="destroy(record.id)" class="dropdown-item text-danger">Delete</a>
                   </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

let props = defineProps({
    auth:Object,
    recorded: Object,
})

const swalBtn = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-link',
        popup: 'custom-popup-class',
    },
    buttonsStyling: false
})

const emit = defineEmits(['delete']);

function destroy(id) {
    emit('delete', id);
}


function playVideo(url){
    console.log(url)
    swalBtn.fire({
        html: `
            <video width="600" height="468" controls>
            <source src="`+url+`" type="video/mp4">
            Your browser does not support the video tag.
            </video>
        `,
        showCloseButton: true,
        showConfirmButton: false,
    })
}

</script>

<style>
  .video-overlay {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0,0,0,0.25);
  }

  .video-overlay i {
    font-size: 18px;
    color: white;
  }

.custom-popup-class {
    padding: 0 !important;
    background: transparent !important;
    width: auto;
}

video {
    margin-top: -65px;
}
</style>