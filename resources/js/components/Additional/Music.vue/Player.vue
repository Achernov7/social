<template>
    <div>
        <div class="audioPlayer d-flex flex-column justify-content-center unselectable mt-1" v-if="musicState">
            <div class="d-flex w-100 h-100">
                <div class="d-flex flex-column justify-content-center me-2" style="margin-left: 11px;">
                    <font-awesome-icon @click="$emit('action', {type: 'previousSong'})" class="pointer" icon="fa-backward" size="lg"/>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <div class="wrapButton d-flex flex-column justify-content-center me-2 pointer" @click="playButton">
                        <div class="d-flex justify-content-center">
                            <font-awesome-icon v-if="!musicState.songIsPlaying" icon="fa-play" class="ms-1" size="lg"/>
                            <font-awesome-icon v-if="musicState.songIsPlaying" icon="fa-pause" size="lg"/>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <font-awesome-icon @click="$emit('action', {type: 'nextSong'})" class="pointer" icon="fa-forward" size="lg"/>
                </div>
                <div class="d-flex flex-column justify-content-start " style="width: 469px;">
                    <div style="height: 23px; text-align: center;">
                        <div class="ms-3 ticker" style="font-size: small; margin-top: 2px; box-sizing: border-box; white-space: nowrap; ">{{ musicState.nameOfSong }}</div>
                    </div>
                    <div class="ms-3 wrapLoadAndSlider pointer">
                        <div class="wrapSlider" @click.stop="(e)=>(setProgress(e))" @mousemove="(e)=>showMouseEnterTime(e)" @mouseleave="cursorSongLength = null">
                            <div class="slider"/>
                            <div class="load"/>
                        </div>
                        <div v-if="cursorSongLength != null" class="cursorSongLength">
                            <div class="triangleSlanting" style="position: absolute;"/>
                            {{ cursorSongLength }}
                        </div>
                    </div>
                </div>
                <div class="d-flex h-100" style="width: 36px;">
                    <div class="wrapVolumeBar">
                        <div class="volumeHeight">
                            <div style="height: 100%; width: 100%;" class="d-flex flex-column justify-content-end pointer" @mousedown="setShouldCalculateVolumeTotrue">
                                <div class="volumeBar"/>
                            </div>
                            <DialogForCalculating v-model:show="shouldCalculateVolume" @finish="topOfElementInY=null" @setProgress="(e)=>calculateVolume(e)"></DialogForCalculating>
                        </div>
                    </div>
                    <font-awesome-icon @click="musicState.volumeMuted = false" icon="fa-volume-xmark" class="mt-1 pointer" v-if="musicState.volumeMuted"/>
                    <font-awesome-icon @click="musicState.volumeMuted = true" icon="fa-volume-off" class="mt-1 pointer" v-if="!musicState.volumeMuted"/>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <div class="d-flex">
                        <div class="choosen">
                            <font-awesome-icon icon="fa-download" class="pointer" size="xl" @click="tryToAddMusic=true"/>
                            <Dialog :CommonBackgroundColor="'rgba(0, 0, 0, 0.3)'" v-model:show="tryToAddMusic">
                                <addMusic @stopAddingMusic="(e)=>stopAddingMusic(e)"/>
                            </Dialog>
                        </div>
                        <div :class="{'choosen': true, 'choosenColour': musicState.typeOfPlayingSong == 'infinite'}">
                            <font-awesome-icon icon="fa-retweet" class="pointer" @click="setInfiniteSong" size="xl"/>
                        </div>
                        <div :class="{'choosen': true, 'choosenColour':  musicState.typeOfPlayingSong == 'random'}">
                            <font-awesome-icon icon="fa-random" class="pointer"  @click="setRandomSongs" size="xl"/>
                        </div>
                        <div :class="{'choosen': true, 'choosenColour': musicState.typeOfPlayingSong == 'nextSongs'}">
                            <font-awesome-icon icon="fa-arrow-right" class="pointer"  @click="setNextSongs" size="xl"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import addMusic from '@/components/Additional/Music.vue/addMusic.vue'
export default {

    props: ['musicStateProp'],

    computed: {
        musicState: {
            get() {
                return this.musicStateProp
            },
            set(value) {
                this.$emit('update:musicStateProp', value)
            }
        },
        sliderWidthPx(){
            return `${this.sliderWidth}px`
        },

        cursorSongLengthPositionPx(){
            return `${this.cursorSongLengthPosition}px`
        },

        volumeHeightPx(){
            return `${this.volumeHeight}px`
        }
    },

    mounted() {
        if (this.musicState.userVolume != 1){
            this.volumeInBar = `${this.musicState.userVolume*100}%`
        }
    },


    watch: {
        'musicState.progressOfPlaying': function(e){
                this.progressPercent = `${e.percentage*100}%`
                this.loadPercent = `${e.loadPercent*99}%`  
        },
    },

    data() {
        return {
            progressPercent:0,
            loadPercent:0,
            sliderWidth: 444,
            cursorSongLength: null,
            cursorSongLengthPosition: 0,
            volumeInBar: '100%',
            shouldCalculateVolume:false,
            topOfElementInY:null,
            volumeHeight: 43.5,
            tryToAddMusic:false,
        }
    },

    components:{
        addMusic
    },

    methods: {

        setProgress(e){
            if (!this.musicState.duration && this.musicState.duration){
                this.musicState.songIsPlaying = true
                this.$emit('action', {type: 'setProgress', value: (e.offsetX/(this.sliderWidth))*this.musicState.duration})
            } else if (this.musicState.duration){
                this.$emit('action', {type: 'setProgress', value: (e.offsetX/(this.sliderWidth))*this.musicState.duration})
            }
        },

        playButton(){
            if (this.musicState.songIsPlaying){
                this.musicState.songIsPlaying = false
            } else {
                this.musicState.songIsPlaying = true
            }
        },

        showMouseEnterTime(e){
            if (this.musicState.nameOfSong.length > 0 && this.musicState.duration){
                let numberInSeconds = (e.offsetX/(this.sliderWidth))*this.musicState.duration
                let minutes = Math.trunc(numberInSeconds/60)
                let seconds = Math.trunc((numberInSeconds/60 - minutes)*60)
                if (minutes<9 && minutes!=0){
                    minutes = `0${minutes}`
                }
                if (seconds<9){
                    seconds = `0${seconds}`
                }
                this.cursorSongLength = `${minutes}:${seconds}`
                this.cursorSongLengthPosition = e.offsetX - 42
            }
        },

        setShouldCalculateVolumeTotrue(e){
            this.musicState.userVolume = 1-e.offsetY / this.volumeHeight
            this.volumeInBar = `${(1-e.offsetY / this.volumeHeight)*100}%`

            this.shouldCalculateVolume = true
            this.topOfElementInY = e.clientY - e.offsetY
        },


        calculateVolume(e){
            if (e.offsetY > this.volumeHeight + this.topOfElementInY){
                this.musicState.userVolume = 0
                this.volumeInBar = `0%`
            } else if (e.offsetY < this.topOfElementInY){
                this.musicState.userVolume = 100
                this.volumeInBar = `100%`
            } else {
                this.musicState.userVolume = 1-((e.offsetY-this.topOfElementInY) / this.volumeHeight)
                this.volumeInBar = `${(1-(e.offsetY-this.topOfElementInY) / this.volumeHeight)*100}%`
            }
        },

        setInfiniteSong(){  
            if (this.musicState.typeOfPlayingSong == 'infinite'){
                this.musicState.typeOfPlayingSong = ''
            } else {
                this.musicState.typeOfPlayingSong = 'infinite'
            }
        },

        setRandomSongs(){
            if (this.musicState.typeOfPlayingSong == 'random'){
                this.musicState.typeOfPlayingSong = ''
            } else {
                this.musicState.typeOfPlayingSong = 'random'
            }
        },

        setNextSongs(){
            if (this.musicState.typeOfPlayingSong == 'nextSongs'){
                this.musicState.typeOfPlayingSong = ''
            } else {
                this.musicState.typeOfPlayingSong = 'nextSongs'
            }
        },

        stopAddingMusic(e){
            this.tryToAddMusic=false
            if (e.data){
                this.$emit('action', e.data)
            }
            
        }


    },
}
</script>
<style scoped>
        .volumeHeight{
        height: v-bind(volumeHeightPx);
        margin-top: 2px;
    }

    .choosen{
        padding: 2px 4px;
        border-radius: 4px;
    }

    .choosenColour{
        background-color: rgba(221,152,146,1);
    }

    .wrapVolumeBar{
        width: 8px;
        background-color: rgb(156, 119, 119);
        margin:4px 4px 4px 7px;
        height: 86%;
        border-radius: 4px;
    }

    .volumeBar{
        width: 2px;
        background-color: rgb(211, 198, 198);
        height: v-bind(volumeInBar);
        margin: 0px 3px;
        border-radius: 1px;
    }

    .triangleSlanting{
        position: relative;
        width: 0;
        height: 0;
        border-left: 3px solid transparent;
        border-right: 3px solid transparent;
        border-top: 3px solid rgba(221,152,146,1);
        transform: rotate(350deg);
        top: 13.5px;
        margin-left: 26.8px;
    }

    .cursorSongLength{
        background-color: rgba(221,152,146,1);
        width: 34px;
        height: 12px;
        padding: 0px 0px 0px 4px;
        border-radius: 4px;
        font-size: 0.75em;
        text-align: center;
        line-height: 1.1;
        margin-top: 4px;
        margin-left: v-bind(cursorSongLengthPositionPx);
    }

    .ticker{
        animation: marquee 5s infinite linear;
        box-sizing: border-box;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @keyframes marquee{
        0% {
            transform: translateX(-3%)
        }

        50% {
            transform: translateX(3%)
        }

        100% {
            transform: translateX(-3%)
        }
    }


    .wrapLoadAndSlider{
        width: 96%;
        height: 10px;
        border-radius: 5px;
        background-color: rgb(156, 119, 119);
        position: relative;
    }

    
    .wrapSlider{
        width:v-bind(sliderWidthPx);
        height: 100%;
        padding: 4px 0px;
        margin-left: 3px;
    }

    .slider{
        width: v-bind(progressPercent);
        height: 2px;
        background-color: rgb(249, 246, 246);
        border-radius: 1px;
        z-index: 2;
        position: relative;
    }

    .load{
        width: v-bind(loadPercent);
        height: 6px;
        background-color: rgb(140, 97, 97);
        margin: 0px 2px;
        top: 2px;
        left: 0px;
        border-radius: 3px;
        z-index: 1;
        position: absolute;
    }

    .wrapButton{
        border-radius: 50%;
        width: 30px;
        height: 30px;
        background-color: rgba(237,198,156,1);
    }

    .audioPlayer{
        background-color: rgb(194, 138, 138);
        border-radius: 8px;
        height: 55px;
        width: 98%;
        margin: 0px 10px 0px 10px;
    }
</style>