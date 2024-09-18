<template>
    <div class="alert alert-accent" role="alert" v-if="msg">
        <div class="d-flex flex-wrap align-items-start">
            <div class="mr-8pt">
                <i class="material-icons">error</i>
            </div>
            <div class="flex" style="min-width: 180px">
                <small class="text-accent-100">
                    <strong>Error!</strong> {{ msg }}
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

