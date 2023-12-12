export default {

    mounted() {
        
    },

    computed: {
        faviconsLoading(){
            var arrayToReturn = []
            for (let i=1; i<=23 ;i++){
                arrayToReturn.push(`/frame_${i}.ico`)
            }
            return arrayToReturn
        }
    },
    
    data() {
        return {
            handleOfLoadingFavicons: [],
            handleOfStartLoadingFavicon: null
        }
    },


    methods: {
        StartLoadingFavicon() {
            const favicon = document.getElementById("favicon");
            var interval = 200;
            var handle = 0;
            this.faviconsLoading.forEach( (elem, index) => {
                handle = setTimeout(() => {
                    favicon.href=`/storage/Favicon/${elem}`
                }, interval*index);
                this.handleOfLoadingFavicons.push(handle);
            })
            this.handleOfStartLoadingFavicon = setTimeout(() => {
                this.StartLoadingFavicon()
            }, this.faviconsLoading.length*interval);
        },

        StopLoadingFavicon() {
            this.handleOfLoadingFavicons.forEach( handle => {
                clearTimeout(handle);
            })
            clearTimeout(this.handleOfStartLoadingFavicon);
            const favicon = document.getElementById("favicon");
            favicon.href='/favicon.ico';
        }
    }
}