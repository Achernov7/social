<template>
    <div class="d-flex" style="background-color: rgba(221,152,146,1); padding: 5px 0px; border-radius: 8px; width: 400px;">
        <div class="d-flex flex-column justify-content-center me-2" style="margin-left: 11px;">
            <font-awesome-icon @click="$emit('action', {type: 'previousSong'})" class="pointer" icon="fa-backward" size="lg"/>
        </div>
        <div class="d-flex flex-column justify-content-center">
            <div class="wrapButton d-flex flex-column justify-content-center me-2 pointer" @click="musicState.songIsPlaying = !musicState.songIsPlaying">
                <div class="d-flex justify-content-center w-100">
                    <font-awesome-icon v-if="!musicState.songIsPlaying" icon="fa-play" class="ms-1" size="lg"/>
                    <font-awesome-icon v-if="musicState.songIsPlaying" icon="fa-pause" style="margin-left: 1px;" size="lg"/>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column justify-content-center">
            <font-awesome-icon @click="$emit('action', {type: 'nextSong'})" class="pointer" icon="fa-forward" size="lg"/>
        </div>
        <div class="d-flex flex-column h-100 justify-content-center unselectable ms-4">
            <div class="text">
                {{ musicState.nameOfSong }}
            </div>
        </div>
    </div>
</template>
<script>
export default {

    props: ['musicState'],
    
}
</script>
<style scoped>

    .text{
        width: 250px;
        text-align: center;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        margin: 0px 10px;
        animation: marquee 5s infinite linear;
    }
    .wrapButton{
        border-radius: 50%;
        width: 27px;
        height: 27px;
        background-color: rgba(237,198,156,1);
    }

    @keyframes marquee{
        0% {
            transform: translateX(-7%)
        }

        50% {
            transform: translateX(7%)
        }

        100% {
            transform: translateX(-7%)
        }
    }
    
</style>