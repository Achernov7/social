<template>
    <div v-if="loadPercent == 0">
        <div class="mb-2" style="border-bottom: 2px solid #1c1b1b; font-size: 1.05em;">
            {{$t('additional.Select_an_audio_recording_on_your_computer')}}
        </div>
        <div>
            {{ $t('additional.File_restrictions') }}:
        </div>
        <div >
            <ul>
                <li>{{$t('additional.The_audio_file_must_not_exceed')}} 20 {{$t('additional.Megabytes')}} {{$t('additional.And_must_be_in_MP3_format')}}</li>
                <li>{{$t('additional.The_length_of_the_file_should_not_exceed')}} 1 {{$t('additional.Hour')}}</li>
            </ul>
        </div>
        <div class="d-flex justify-content-center">
            <button class="buttonInputAudio" @click="this.$refs.audioInput.click()" type="">{{ $t('additional.Choose_file') }}</button>
            <input ref="audioInput" type="file" accept=".mp3" style="visibility: hidden; position: absolute;" @change="loadFile">
        </div>
    </div>
    <div v-if="loadPercent != 0" class="d-flex flex-column justify-content-center mt-3" style="height: 80px;">
        <div class="d-flex justify-content-center h-100">
            <div class="d-flex flex-column justify-content-center" style="font-weight: bold;">{{ $t('additional.Loading') }}</div>
            <div class="ms-3 d-flex justify-content-start" style="width: 300px;">
                <div class="loading"/>
            </div>
        </div>
    </div>
</template>
<script>
export default {

    data() {
        return {
            loadPercent:0
        }
    },

    emits: ['stopAddingMusic'],
 
    methods: {
        loadFile() {
            if (this.$refs.audioInput.files[0]) {
                let data = new FormData();
                data.append('music', this.$refs.audioInput.files[0])
                axios({
                    method: 'post',
                    url: '/api/music/store',
                    data: data,
                    onUploadProgress: (progressEvent) => {
                        this.loadPercent = `${Math.round(progressEvent.progress* 1000)/10}%`
                    }
                })
                    .then(res => {
                        this.loadPercent = 0
                        this.$emit('stopAddingMusic', {data:{type: 'addMusic', value: res.data.data}})
                    })
                    .catch(err=>{
                        this.loadPercent = 0
                        this.$emit('stopAddingMusic', true)
                        console.log(err);
                    })
            }
        }
    }
}
</script>
<style scoped>

    .loading{
        height: 15px;
        width: v-bind(loadPercent);
        margin: auto 0;
        border-radius: 5px;
        background-color: rgb(174, 125, 121);
    }

    .buttonInputAudio{
        background-color: rgba(221,152,146,1);
        border-radius: 8px;
        box-shadow: 0px -2px 6px 0px rgb(255, 255, 255);
    }
    ul {
        padding: 0 0 0 6%;
    }
</style>