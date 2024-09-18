<template>
    <div class="col-md-6 col-lg-4 col-xl-3 card-group-row__col" v-for="data in subjects">
        <div class="card card-sm card--elevated p-relative o-hidden overlay js-overlay card-group-row__card"
             data-partial-height="44">
            <div v-if="comp=='Subject/SubjectList'">
                <Link v-if="data.thumbnail" :href="'/subject/'+data.slug+'/'+data.section.id" class="js-image" data-position="center" data-height="auto" :style="'display: block; position: relative; overflow: hidden; background-image: url(/storage/'+data.thumbnail+'); background-size: cover; background-position: center center; height: 168px;'">
                    <img :src="'/storage/'+data.thumbnail" style="visibility: hidden;">
                </Link>
                <Link v-if="!data.thumbnail" :href="'/subject/'+data.slug+'/'+data.section.id" class="js-image" data-position="center" data-height="auto" style="display: block; position: relative; overflow: hidden; background-image: url('/images/blank.webp'); background-size: cover; background-position: center center; height: 168px;">
                    <img src="/images/course/background-3.png" style="visibility: hidden;">
                </Link>
            </div>
            <div v-else>
                <a v-if="data.thumbnail" href="javascript:void(0);" class="js-image" data-position="center" data-height="auto" :style="'display: block; position: relative; overflow: hidden; background-image: url(/storage/'+data.thumbnail+'); background-size: cover; background-position: center center; height: 168px;'">
                    <img :src="'/storage/'+data.thumbnail" style="visibility: hidden;">
                </a>
                <a v-if="!data.thumbnail" href="javascript:void(0);" class="js-image" data-position="center" data-height="auto" style="display: block; position: relative; overflow: hidden; background-image: url('/images/blank.webp'); background-size: cover; background-position: center center; height: 168px;">
                    <img src="/images/course/background-3.png" style="visibility: hidden;">
                </a>
            </div>
            <div class="card-body flex">
                <div class="d-flex">
                    <div class="flex">
                        <Link v-if="comp=='Subject/SubjectList'" class="card-title text-black-70 mb-1"
                           :href="'/subject/'+data.slug+'/'+data.section.id">{{ data.title }}</Link>
                        <a v-else class="card-title text-black-70 mb-1"
                        href="javascript:void(0);">{{ data.title }}</a>
                        <small class="text-50 font-weight-bold mb-4pt">{{ data.code }}</small>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row justify-content-between">
                    <div class="col-auto d-flex align-items-right mb-1" >
                        <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                        <p class="flex text-50 lh-1 mb-0"><small>{{ data.sched_date }}</small></p>
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <span class="material-icons icon-16pt text-50 mr-4pt">subject</span>
                        <p class="flex text-50 lh-1 mb-0"><small>{{ data.section.name }}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    let props = defineProps({
        comp: String,
        subjects:Object,
    })
</script>

