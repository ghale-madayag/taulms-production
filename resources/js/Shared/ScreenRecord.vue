<template>
     <div class="card col-md-12 pt-3" style="position: absolute; bottom: 0; max-width: 100%; margin-bottom: 0; border-radius: 35px 35px 0px 0px; align-items: center; background: #f5f7fa;">
        <video id="myVideo" playsinline class="video-js vjs-default-skin" style="display:none;">
            <p class="vjs-no-js">
                To view this video please enable JavaScript, or consider upgrading to a
                web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">
                    supports HTML5 video.
                </a>
            </p>
        </video>
        <div class="card-subtitle mb-16pt mb-lg-0 text-accent pb-2 text-center" :class="{'hidden' : isStopRecording == false}">
            Please don't close or refresh your browser.
        </div>
        <div class="button-list">
            <button type="button" class="btn btn-accent btn-rounded" @click.prevent="startRecording()" :class="{'hidden' : isStartRecording}" id="btnStart"><i class="material-icons mr-1">radio_button_checked</i> Record Your Screen</button>
            <button type="button" class="btn btn-outline-accent btn-rounded" @click.prevent="stopRecording()" :class="{'hidden' : isStopRecording == false}" id="btnStop"><img src="/images/illustration/recording.gif" width="20" class="mr-1" :class="{'hidden' : isPause == false}"/> <i class="material-icons mr-1" :class="{'hidden' : isResume == false}">trip_origin</i> Stop</button>
            <button type="button" class="btn btn-secondary btn-rounded" @click.prevent="pauseRecording()" :class="{'hidden' : isPause == false}" id="btnPause"><i class="material-icons mr-1">pause</i> Pause</button>
            <button type="button" class="btn btn-secondary btn-rounded" @click.prevent="resumeRecording()" :class="{'hidden' : isResume == false}" id="btnResume"><i class="material-icons mr-1">play_arrow</i> Resume</button>
        </div>
     </div>
</template>

<script>
import 'video.js/dist/video-js.css'
import 'videojs-record/dist/css/videojs.record.css'
import videojs from 'video.js'
import 'webrtc-adapter'
import RecordRTC from 'recordrtc'
import Record from 'videojs-record/dist/videojs.record.js'
import FFmpegjsEngine from 'videojs-record/dist/plugins/videojs.record.ffmpegjs.js';
import WaveSurfer from 'wavesurfer.js';
import MicrophonePlugin from 'wavesurfer.js/dist/plugin/wavesurfer.microphone.js';
import videojs_wavesurfer_css from 'videojs-wavesurfer/dist/css/videojs.wavesurfer.css';
import Wavesurfer from 'videojs-wavesurfer/dist/videojs.wavesurfer.js';
import Swal from 'sweetalert2/dist/sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import {useForm} from "@inertiajs/inertia-vue3";
import axios from 'axios';

WaveSurfer.microphone = MicrophonePlugin;
export default {
    props:[
        'videoId'
    ],
    data() {
        return {
            videoIdVal: '',
            player: '',
            swalBtn:'',
            subject_id: '',
            section_id:'',
            loading_btn:'',
            retake: 0,
            controls: true,
            isSaveDisabled: true,
            isStartRecording: false,
            isStopRecording: false,
            isResume: false,
            isPause: false,
            isRetakeDisabled: true,
            submitText: 'Submit',
            options: {
                controls: true,
                bigPlayButton: false,
                controlBar: {
                    deviceButton: false,
                    recordToggle: false,
                    pipToggle: false
                },
                width: 100,
                height: 100,
                fluid: true,
                plugins: {
                    wavesurfer: {
                            backend: 'WebAudio',
                            waveColor: '#36393b',
                            progressColor: 'black',
                            debug: true,
                            cursorWidth: 1,
                            displayMilliseconds: true,
                            hideScrollbar: true,
                            plugins: [
                                // enable microphone plugin
                                WaveSurfer.microphone.create({
                                    bufferSize: 4096,
                                    numberOfInputChannels: 1,
                                    numberOfOutputChannels: 1,
                                    constraints: {
                                        video: false,
                                        audio: true
                                    }
                                })
                            ]
                        },
                    record: {
                        audio: true,
                        screen: true,
                        maxLength: 7200,
                        debug: true,
                        timeSlice: 10000
                    }
                }
            }
        }
    },
    mounted() {

        if(this.loading_btn){
            this.loading_btn = 'btn btn-primary mr-2 is-loading';
        }else{
            this.loading_btn = 'btn btn-primary mr-2';
        }
        this.swalBtn = Swal.mixin({
            customClass: {
                confirmButton: this.loading_btn,
                cancelButton: 'btn btn-link',
                container: 'modal-class-recorder',
            },
            buttonsStyling: false
        })

        this.player = videojs('myVideo', this.options, () => {
            var msg = 'Using video.js ' + videojs.VERSION +
                ' with videojs-record ' + videojs.getPluginVersion('record') +
                ' and recordrtc ' + RecordRTC.version;
            videojs.log(msg);
        });
     
        this.player.on('deviceReady', () => {
            this.player.record().start();
        });
        this.player.on('deviceError', () => {
            console.log('device error:', this.player.deviceErrorCode);
        });
        this.player.on('error', (element, error) => {
            console.error(error);
        });

        this.player.on('chromeMediaSourceIdLost', () => {
            console.log('continue still awake!');
        });

        this.player.on('startRecord', () => {
            this.isStartRecording = true;
            this.isStopRecording = true;
            this.isPause = true;
            this.start();
           
        });
        // user completed recording and stream is available
        this.player.on('finishRecord', () => {
            this.isSaveDisabled = false;
            if(this.retake == 0) {
                this.isRetakeDisabled = false;
            }
    
        });

        this.player.on('ended', function(){ 
            var a = window.playerEvents.playerEventEnded(); 
            console.log('The user has stopped sharing in Google Chrome.');
        });

        this.player.on('timestamp', () => {
           
            if(this.videoIdVal != ""){
                this.recordedTime(this.videoIdVal);
            }
        });
    },

    methods: {
        showSwal() {
            
           this.swalBtn.fire({
                title: 'Are you sure?',
                text: "Do you want to save the recorded file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitVideo();
                }
            })
        },
        startRecording() {
            axios.get('/recorder/getSubject')
            .then(response => {
                let options = response.data.subject;
                let selectOptions = options.map(option => `<option value="${option.section.id}" data-subject="${option.id}">${option.section.name+' - '+option.title}</option>`).join('');
                
                let content = `
                <select id="select" class="form-control custom-select mb-4">
                    ${selectOptions}
                </select>
                `;
                this.swalBtn.fire({
                    title: 'Please select your class',
                    html: content,
                    showCancelButton: true,
                    confirmButtonText: 'Start',
                    preConfirm: () => {
                        return document.getElementById("select").value;
                    }
                }).then((result) => {
                    
                    if (result.isConfirmed) {
                        this.player.record().getDevice();
                      
                        // this.start(result.value);
                        let str = result.value;
                        let res = str.split("-"); 

                        this.subject_id = res[0];
                        this.section_id = res[1];

                    }
                });

            })
            .catch(error => {
                console.error(error);
            });

        },
        pauseRecording() {
            this.player.record().pause();
            this.isResume = true;
            this.isPause = false;
        },
        resumeRecording() {
            this.player.record().resume();
            this.isPause = true;
            this.isResume = false;
        },
        stopRecording(){
            this.player.record().stopDevice();
            this.isPause = false;
            this.isResume = false;
            this.isStopRecording = false;
            this.isStartRecording = false;
            this.showSwal();
        },
        start(){
        
            let formData = useForm({
                subject_id: this.subject_id,
                section_id: this.section_id,
                startTime: this.player.currentTimestamp,
            });

            formData.post('/recorder/start',{
                preserveScroll:true,
                onSuccess:() => {
                    this.videoIdVal = this.videoId;
                }
            })
        },

        recordedTime(id){
            let formData = useForm({
                id: id,
                end: this.player.currentTimestamp,
            });

            formData.post('/recorder/'+id+'/update',{
                preserveScroll:true,
                onSuccess:() => {
                        console.log('updated')
                    }
                })
        },
        submitVideo() {
            this.isSaveDisabled = true;
            this.isRetakeDisabled = true;
            var data = this.player.recordedData;
            let formData = useForm({
                id: this.videoIdVal,
                name: data.name,
                video: data,
                end: this.player.currentTimestamp
            });


            this.player.record().saveAs({'video': data.name});
            
            formData.post('/recorder/'+this.videoIdVal+'/data',{
                preserveScroll:true,
                onStart:() => {
                    let timerInterval
                    this.swalBtn.fire({
                        title: 'Please wait!',
                        html: 'Don\'t close or refresh your browser while uploading the file',
                        timer: 3600000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        backdrop: 'static',
                        didOpen: () => {
                            this.swalBtn.showLoading()
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {
                        if (result.dismiss === this.swalBtn.DismissReason.timer) {
                            console.log('I was closed by the timer')
                        }
                    })
                },
                onFinish:() => {
                    this.loading_btn = false;
                },
                onSuccess:() => {
                    this.videoIdVal = "";
                    this.swalBtn.fire(
                        'Saved!',
                        'Your data has been successfully saved.',
                        'success'
                    )
                },
                onError:() => {
                    this.swalBtn.fire(
                        'Saved!',
                        'There was an error uploading your video. Please try again later.',
                        'error'
                    )
                }
            })
           
        },
        retakeVideo() {
            this.isSaveDisabled = true;
            this.isRetakeDisabled = true;
            this.retake += 1;
            this.player.record().start();
        }
    },
    beforeDestroy() {
        if (this.player) {
            this.player.dispose();
        }
    }
}
</script>

<style>
.hidden{
    display: none;
    visibility: hidden;
}

.modal-class-recorder .swal2-popup.swal2-modal.swal2-show {
    padding: 2% 3%;
}

.modal-class-recorder select#select {
    font-size: 16px;
}
</style>