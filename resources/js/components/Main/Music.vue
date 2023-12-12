<template>
    <div class="d-flex justify-content-center h-100">
        <div class="wrapMusic">
            <div class="d-flex justify-content-center">
                <Player @action="(e)=>emitActionToTop(e)" v-model:musicStateProp="musicState"/>
            </div>
            <div style="width: 97%; margin: 0 auto">
                <SearchingPersonField class="mt-2" v-model="searchingMusic"></SearchingPersonField>
            </div>
            <div class="wrapSongs">
                <div v-for = "song in songs" :key="song.id">
                    <div class="song">
                        <div>
                            <img :src="song.image.mini_url" @click="playSong(song)" class="pointer">
                        </div>
                        <div class="d-flex flex-column justify-content-center h-100 ms-4 w-100">
                            <div class="d-flex justify-content-between w-100 h-100">
                                <div class="ms-4 d-flex flex-column justify-content-center h-100">
                                    {{ song.name }}
                                </div>
                                <div class="d-flex h-100">                                
                                    <div class="d-flex flex-column justify-content-center h-100 me-4">
                                        <Equalizer class="mb-1" v-if="musicState.indexOfPlayingSong == song.id && musicState.songIsPlaying"></Equalizer>
                                        <EqualizerStop v-if="musicState.indexOfPlayingSong == song.id && !musicState.songIsPlaying" class="mb-1"></EqualizerStop>
                                        <font-awesome-icon v-if="!(musicState.indexOfPlayingSong == song.id)" icon="fa-play" class="pointer me-3" size="xl" @click="playSong(song)"/>
                                    </div>
                                    <div class="me-3 d-flex flex-column justify-content-center h-100">
                                        <font-awesome-icon v-if="song.liked == false" icon="fa-plus" class="pointer me-3" size="xl" @click="likeSong(song)"/>
                                        <font-awesome-icon v-else icon="fa-xmark" class="pointer me-3" size="xl" @click="unlikeSong(song)"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="songs.indexOf(song) == songs.length -3 " v-intersection="getSongs" class="observer" ref="observer"></div> 
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import SearchingPersonField from '@/components/UI/SearchingPersonField.vue';
import Player from '@/components/Additional/Music.vue/Player.vue';
import Equalizer from '@/components/UI/Equalizer.vue';
import EqualizerStop from '@/components/UI/EqualizerStop.vue';
export default {

    props: ['musicStateProp'],

    mounted() {
        this.getSongs()
    },

    data() {
        return {
            songs: [],
            limit:15,
            searchingMusic: '',
            stopFetch: false,
            IdsOfAlreadyExistedSongs: [],
            likedUsersIsOver: false,
            handleOfGetSongs:null,
        }
    },

    watch: {
        searchingMusic() {
            this.stopFetch = false
            this.songs = []
            this.IdsOfAlreadyExistedSongs = []
            this.likedUsersIsOver = false
            this.getSongs()
            this.musicState.searchingMusic = this.searchingMusic
        },
        'musicState.audioEndedAndNeedNext': function(){
            if (this.musicState.audioEndedAndNeedNext){
                this.emitActionToTop({type: 'nextSong'})
            }
        }

    },

    computed: {
        musicState: {
            get() {
                return this.musicStateProp
            },
            set(value) {
                this.$emit('update:musicStateProp', value)
            }
        },    
    },

    methods: {
        async emitActionToTop(e){
            
            if (e.type == 'nextSong' && this.musicState.typeOfPlayingSong != 'random') {
                
                var lastSong = this.getLastSong()

                if (lastSong != null){
                    
                    if (this.songs.indexOf(lastSong) != this.songs.length - 1){
                        
                        var neededSong = this.songs[this.songs.indexOf(lastSong) + 1]
                        this.playSong(neededSong)
    
                    } else if (this.stopFetch != true){
    
                        if (this.handleOfGetSongs == null){
                            await this.getSongs()
                            this.emitActionToTop(e)
                        }

                    } else {
                        this.musicState.songIsPlaying = false
                    }

                } else {
                    this.$emit('action', e)
                }

            } else if (e.type == 'previousSong' && this.musicState.typeOfPlayingSong != 'random') {
                
                var lastSong = this.getLastSong()

                if (lastSong != null){
                    
                    if (this.songs.indexOf(lastSong) != 0){
                        var neededSong = this.songs[this.songs.indexOf(lastSong) - 1]
                        this.playSong(neededSong)
                    }
                    
                } else {
                    this.$emit('action', e)
                }

            } else if (e.type == 'addMusic') {

                this.songs.unshift(e.value)

            } else {

                this.$emit('action', e)
                
            }
        },

        getSongs(){

            return new Promise((resolve) => {     

                if (this.handleOfGetSongs){
                    clearTimeout(this.handleOfGetSongs)
                }

                this.handleOfGetSongs = setTimeout(() => {
                    
                    if (!this.stopFetch) {
        
                        let params = { limit: this.limit }
            
                        if (this.IdsOfAlreadyExistedSongs.length > 0 ){
                            params.IdsOfAlreadyExistedSongs = this.songs.map(song => song.id)
                        }
            
                        if (this.likedUsersIsOver){
                            params.likedUsersIsOver = true
                        }
                        
                        if (this.searchingMusic != ''){
                            params.searchingMusic = this.searchingMusic
                        }
                        
                        axios.post('/api/music/index',  params )
                            .then(res => {
                                
                                this.IdsOfAlreadyExistedSongs = [...this.IdsOfAlreadyExistedSongs, ...res.data.data.map(song => song.id)]
                                this.songs = [...this.songs, ...res.data.data]

                                if (this.musicState.songUrl.length == 0){
                                    this.musicState.songUrl = res.data.data[0].url
                                    this.musicState.nameOfSong = res.data.data[0].name
                                    this.musicState.indexOfPlayingSong = res.data.data[0].id
                                }
                                
                                if (res.data.likedUsersIsOver){
                                    this.likedUsersIsOver = true
                                }
    
                                if (res.data.data.length < this.limit){
                                    this.stopFetch = true
                                }

                                this.handleOfGetSongs = null

                                resolve()
                            })
                            .catch(err => {
                                console.log(err)
                                resolve()
                            })
                            
                    }
                    
    
                }, 500)

            })

        },

        playSong(song){
            this.musicState.songUrl = song.url
            this.musicState.nameOfSong = song.name
            this.musicState.indexOfPlayingSong = song.id
            if (this.musicState.songIsPlaying == false){
                this.musicState.songIsPlaying = true
            } else {
                this.$emit('action', {type: 'playSong'})
            }
            
        },

        likeSong(song){
            axios.post(`/api/music/like/${song.id}`)
                .then(res => {
                    if (res.data.message == 'Liked successfully'){
                        this.songs.every((songInArray) => {
                            if (songInArray == song){
                                songInArray.liked = true
                                return false
                            }
                            return true
                        })
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },

        unlikeSong(song){
            axios.post(`/api/music/unlike/${song.id}`)
                .then(res => {
                    if (res.data.message == 'Unliked successfully and music was deleted'){
                        this.songs.splice(this.songs.indexOf(song), 1)
                    }
                    if (res.data.message == 'Unliked successfully'){
                        this.songs.every((songInArray) => {
                            if (songInArray == song){
                                songInArray.liked = false
                                return false
                            }
                            return true
                        })
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },

        getLastSong(){
            let lastSong = null
            this.songs.every((song) => {
                if (song.id == this.musicState.indexOfPlayingSong){
                    lastSong = song
                    return false
                }
                return true
            })

            return lastSong
        }
    },
    
    components: {
        Player,
        Equalizer,
        EqualizerStop,
        SearchingPersonField
    },

    
}
</script>
<style scoped>

    img{
        width: 50px;
        border-radius: 8px;
    }

    .song{
        display: flex;
        margin: 5px 7px;
        height: 60px;
        background-color:rgb(210, 162, 162);
        border-radius: 8px;
        padding: 5px;
    }

    .song:hover{
        background-color: rgb(156, 119, 119);
    }

    .wrapSongs{
        height: 730px;
        margin-left: 10px;
        width: 98%;
        background-color: #bc7e7e;
        border-radius: 8px;
        overflow: auto;
    }

    .wrapSongs::-webkit-scrollbar {
        display: none;
    }

    .wrapMusic{
        max-width: 1100px;
        width: 55%;
        padding: 6px 6px;
        background-color: rgba(221,152,146,1);
        border-radius: 8px;
        height: 100%;
        min-width: 740px;
    }

</style>