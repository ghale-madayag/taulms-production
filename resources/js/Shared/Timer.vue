<template>
    <div>
        <div>
           <span>0{{timer.hours}}</span>:<span>{{timer.minutes}}</span>:<span>{{timer.seconds}}</span>
        </div>
    </div>
</template>


<script setup>
import {watchEffect, onMounted, onUnmounted} from "vue";
import { useTimer } from 'vue-timer-hook';
import {useForm} from "@inertiajs/inertia-vue3";

let props = defineProps({
    time_value:Number,
})

const time = new Date();
time.setSeconds(time.getSeconds() + props.time_value); // 10 minutes timer
const timer = useTimer(time);


let form = useForm({
    hrs: timer.hours,
    min: timer.minutes,
    sec: timer.seconds,
});

onMounted(() => {
    watchEffect(async () => {
        if(timer.isExpired.value) {
            console.warn('IsExpired')
        }
    })
})

</script>
