import axios from "axios"

export default {

    data () {
        return {
            musicState:{
                songUrl: '',
                songIsPlaying: false,
                indexOfPlayingSong: null,
                typeOfPlayingSong:'',
                nameOfSong: '',
                userVolume: 1,
                volumeMuted:false,
                progressOfPlaying: {},
                duration: null,
                promisePlay:undefined,
                audioEndedAndNeedNext:false,
                searchingMusic:''
            },
            idsAlreadyPlayedInRandom: []
        }
    },

    computed: {
        volume(){
            if (this.musicState.volumeMuted == false){
                return this.musicState.userVolume
            } else {
                return 0
            }
        },
        songIsPlaying(){
            return this.musicState.songIsPlaying
        }
    },

    watch: {
        songIsPlaying(newValue){
            if (newValue == false){
                this.stopPlayingSong()
            } else {
                this.playSong()
            }
        }
    },

    methods: {

        actionWithMusic(e){
            switch (e.type){
                case 'setProgress':
                    this.$refs.song.currentTime = e.value
                    break;
                case 'playSong':
                    this.playSong()
                    break;
                case 'nextSong':
                    
                    if (this.musicState.typeOfPlayingSong == 'random'){
                        this.getRandomSong()
                    } else {
                        this.getNextSongAndPlay()
                    }
                    break;
                case 'previousSong':
                    
                    if (this.musicState.typeOfPlayingSong == 'random'){
                        this.getRandomSong()
                    } else {
                        this.getPreviousSongAndPlay()
                    }
                    break;
            }
        },

        audioFileEnded(){
            
            if (this.musicState.typeOfPlayingSong == 'infinite'){
                this.idsAlreadyPlayedInRandom = []
                this.playSong()
            } else if(this.musicState.typeOfPlayingSong == 'random'){
                this.getRandomSong()
            } else if(this.musicState.typeOfPlayingSong == 'nextSongs'){
                this.idsAlreadyPlayedInRandom = []
                
                if ( this.$route.name == 'user.music'){
                    this.musicState.audioEndedAndNeedNext = true
                    setTimeout(() => {
                        this.musicState.audioEndedAndNeedNext = false
                    }, 200);
                } else {
                    this.getNextSongAndPlay()
                }
            } else {
                this.idsAlreadyPlayedInRandom = []
                this.musicState.songIsPlaying = false
                this.musicState.progressOfPlaying = {
                    percentage: 0,
                    loadPercent: 0
                }
            }
        },

        playSong(){

            if (this.musicState.songUrl == ''){
                this.getSong()
            } else {
                this.$nextTick(() => {
                    this.musicState.duration = this.$refs.song.duration
                    this.musicState.promisePlay = this.$refs.song.play()
                    this.musicState.promisePlay
                        .catch(error => {
                            if (!(error.message == 'The play() request was interrupted by a new load request. https://goo.gl/LdLk22')){
                                console.log(error)
                            }
                        })
                })
            }

        },

        updateProgressOfPlayingSong(e){
            if (e.target.duration > 0 && isNaN(this.musicState.duration)){
                this.musicState.duration = this.$refs.song.duration
            }
            if (e.target.duration > 0){
                this.musicState.progressOfPlaying = {
                    percentage: (e.target.currentTime / e.target.duration),
                    loadPercent: (e.target.buffered.end(e.target.buffered.length-1) / e.target.duration)
                }
            } else {
                this.musicState.progressOfPlaying = {
                    percentage: 0,
                    loadPercent: 0
                }
            }
        },
        
        getSong(){
            axios.get('/api/music/song')
                .then((response) => {
                    if (response.data.message == 'No music uploaded yet'){
                        console.log('No music uploaded yet')
                    } else {
                        this.startPlayingSongFromRequest(response.data.data)
                    }
                })
                .catch((error) => {
                    console.log(error)
                })
        },

        getRandomSong(){

            let params = {}
            if (this.musicState.searchingMusic.length > 0){
                params.search = this.musicState.searchingMusic
            }

            if (this.idsAlreadyPlayedInRandom.length > 0){
                params.idsAlreadyPlayedInRandom = this.idsAlreadyPlayedInRandom
            }
            this.idsAlreadyPlayedInRandom.push(this.musicState.indexOfPlayingSong)

            axios.post(`/api/music/random/${this.musicState.indexOfPlayingSong}`, params)
                .then((response) => {
                    
                    this.handlerOfPreviousAndNextResponse(response)
                })
                .catch((error) => {
                    console.log(error)
                })
        },

        getNextSongAndPlay(){
            this.idsAlreadyPlayedInRandom = []
            var axiosSting = `/api/music/next/${this.musicState.indexOfPlayingSong}`
            this.basisOfNextPreviousRandom(axiosSting)
        },

        getPreviousSongAndPlay(){
            this.idsAlreadyPlayedInRandom = []
            var axiosString = `/api/music/previous/${this.musicState.indexOfPlayingSong}`
            this.basisOfNextPreviousRandom(axiosString)
        },

        basisOfNextPreviousRandom(axiosString){
            if (this.musicState.searchingMusic.length > 0){
                var params = {
                    search: this.musicState.searchingMusic
                } 
            } else {
                var params = {}
            }
            axios.get(axiosString, {params})
                .then((response) => {
                    this.handlerOfPreviousAndNextResponse(response)
                })
                .catch((error) => {
                    console.log(error)
                })
        },

        handlerOfPreviousAndNextResponse(response){
            if (response.data.message && response.data.message == 'No music uploaded yet'){
                console.log('No music uploaded yet')
            } else if (response.data.message && response.data.message == 'You have 1 song in your library'){
                console.log('You have 1 song in your library')
            } else {
                this.startPlayingSongFromRequest(response.data.data)
            }
        },

        startPlayingSongFromRequest(song){
            this.musicState.duration = null
            this.musicState.songUrl = song.url  
            this.musicState.indexOfPlayingSong = song.id
            this.musicState.songIsPlaying = true
            this.$nextTick(() => {
                this.musicState.nameOfSong = song.name
                this.$refs.song.play()
                setTimeout(() => {
                    this.musicState.duration = this.$refs.song.duration
                }, 300);
            })
        },

        stopPlayingSong(){
            this.$refs.song.pause()
        },
    },  

}