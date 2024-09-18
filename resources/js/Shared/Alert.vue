<template>
    <div class="alert alert-primary" role="alert" v-if="msg">
        <div class="d-flex flex-wrap align-items-start">
            <div class="mr-8pt">
                <i class="material-icons">done</i>
            </div>
            <div class="flex" style="min-width: 180px">
                <small class="text-black-100">
                    <strong>Success!</strong> {{ msg }}
                </small>
            </div>
        </div>
    </div>
</template>

<script setup>
let props = defineProps({
    msg: Boolean,
})
</script>

