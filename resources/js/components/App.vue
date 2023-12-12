<template >
    
<div class = "d-flex justify-content-end me-3 mb-2" style="height: 40px;">
    <div class="d-flex flex-column justify-content-center h-100" style="width: 80%;" v-if="this.$route.name != 'user.music' && this.musicState.songUrl != '' && token">
        <div class="d-flex justify-content-center w-100 marginLeftForPlayer">
            <MiniPlayer :musicState ="musicState" @action="(e)=>actionWithMusic(e)"/>
        </div>
    </div>
    <div class="d-flex flex-column justify-content-center h-100">
        <span>{{ $d(new Date(), "shortFormat") }}</span> 
    </div>
    <div class="d-flex flex-column justify-content-center h-100">
        <LanguageSwitcher class="mx-3 rounded"></LanguageSwitcher>
    </div>
    <div class="d-flex flex-column justify-content-center h-100">
        <MainMenuButton v-if="!token" :routerTo = "'user.login'" :isTopMenuElement="true"> {{ $t('main.Login') }} </MainMenuButton>
    </div>
    <div class="d-flex flex-column justify-content-center h-100">
        <MainMenuButton v-if="!token" :routerTo = "'user.registration'" :isTopMenuElement="true"> {{ $t('main.Registration') }} </MainMenuButton>
    </div>
    <div class="d-flex flex-column justify-content-center h-100">
        <MainMenuButton v-if="token" :isTopMenuElement="true" :isButton="true" :defineFunction="logout"> {{ $t('main.Logout') }}</MainMenuButton>
    </div>
</div>
<div class="row eiheight d-flex justify-content-center">
    <div class="col-1 leftbar align-self-center" v-if="token">
        <MainMenuButton :routerTo = "'get.news'" :fontawesome="'fa-solid fa-clipboard'"> {{ $t('main.News') }} </MainMenuButton>
        <MainMenuButton :routerTo = "'user.profile'" :fontawesome="'fa-solid fa-user'"> {{ $t('main.My_page') }} </MainMenuButton>
        <MainMenuButton :routerTo = "'user.friends'" :fontawesome="'fa-solid fa-user-group'"> {{ $t('main.Friends') }} </MainMenuButton>
        <MainMenuButton :routerTo = "'user.groups'" :fontawesome="'fa-solid fa-users'"> {{ $t('main.Groups') }} </MainMenuButton>
        <MainMenuButton :routerTo = "'user.music'" :fontawesome="'fa-solid fa-music'"> {{ $t('main.My_music') }} </MainMenuButton>
        <MainMenuButton :routerTo = "'user.gallery'" :fontawesome="'fa-solid fa-image'"> {{ $t('main.Gallery') }} </MainMenuButton>
        <MainMenuButton :routerTo = "'user.messages'" :fontawesome="fontawesomeForMessages()" :numberWithIcon="this.NumberOfUnreadConversations"> {{ $t('main.Messages') }} </MainMenuButton>
    </div>
    <div class="col-10">
        <div class="d-flex flex-column justify-content-center h-100 w-100 mb-4">
            <div class="rightbar">
                <router-view @ReduceNumberOfMessages="ReduceNumberOfUnreadConversations()" @increaseNumberOfMessages="getNumberOfUnreadConversations" :MessageToPush="MessageToPush" :key="$route.fullPath" class="px-4" v-slot="{ Component, route }">
                    <transition name="fade" mode="out-in" appear>
                        <div class="h-100" :key="route.name">  
                            <component :is="Component" v-model:musicStateProp="musicState" @action="(e)=>actionWithMusic(e)"/>
                        </div>
                    </transition>
                </router-view>
                <audio preload="none" @timeupdate="(e)=>(updateProgressOfPlayingSong(e))" :volume="volume" @ended="audioFileEnded" ref="song" :src="musicState.songUrl"/>
            </div>
        </div>
    </div>
</div>
<div v-if="this.display" class='messagesToDisplay d-flex flex-column justify-content-end'>
    <TransitionGroup name="messages-list" >
    <div class="message" v-for="message in messagesToDisplay" :key="message.id" @click="goToTheUserPage(message)">
        <div class="userInfo">
            <div>
                <img :src="message.senderInfo.mini_image.mini_url" alt="Avatar">
            </div>
            <div class="Username">
                {{ message.senderInfo.name }}
            </div>
        </div>
        <div class="messageText">
            {{message.chat_message}}
        </div>
    </div>
    </TransitionGroup>
</div>

</template>
<script>
import LanguageSwitcher from '@/components/UI/LanguageSwitcher.vue';
import musicListenerMixin from '@/Mixins/musicListenerMixin'
import MiniPlayer from '@/components/Additional/Music.vue/MiniPlayer.vue';
import ChooseMarginKLeftForMiniPlayer from '@/Mixins/ChooseMarginKLeftForMiniPlayer'

export default {

    name: 'App',

    mounted() {
        this.getToken();
        this.detectbrowser();
        if (!this.NumberOfUnreadConversationsWasLoad) {
            this.getNumberOfUnreadConversationsWithAudio();
        }
    },
    
    updated() {
        this.getToken();
        if (!this.NumberOfUnreadConversationsWasLoad) {
            this.getNumberOfUnreadConversationsWithAudio();
        }
    },

    components: { 
        LanguageSwitcher,
        MiniPlayer
    },

    mixins: [musicListenerMixin, ChooseMarginKLeftForMiniPlayer],

    data() {
        return {
            token: null,
            NumberOfUnreadConversations: 0,
            NumberOfUnreadConversationsWasLoad: false,
            MessageSound: null,
            browser:'',
            messagesToDisplay: [],
            MessageToPush:'',
            display:false,
            HandleOfaxiosGetNumberOfUnreadConversationsWithAudio: null,
        }
    },

    methods: {
        
        getToken(){
            this.token = localStorage.getItem('x_xsrf_token');
        },

        logout() {
            axios.post('/logout')
            .then(res => {
                    window.Echo.leave(`chat_${res.data.UserId}`)
                    localStorage.removeItem('x_xsrf_token');
                    this.NumberOfUnreadConversationsWasLoad = false
                    this.$router.push( this.$Tr.i18nroute({ name: 'user.login'}));
                })
        },

        fontawesomeForMessages(){
            if (this.NumberOfUnreadConversations > 0) {
                return 'fa-solid fa-envelope'
            } else {
                return 'fa-solid fa-message'
            }
        },

        getNumberOfUnreadConversationsWithAudio(){
            if (!this.NumberOfUnreadConversationsWasLoad && this.$route.name !== '/') {
                
                if (localStorage.getItem('x_xsrf_token')) {
                    clearTimeout(this.HandleOfaxiosGetNumberOfUnreadConversationsWithAudio)
                    this.HandleOfaxiosGetNumberOfUnreadConversationsWithAudio = setTimeout(() => {
                        axios.get('/api/messages/getNumberOfUnreadConversationsPlusDeafultAudioPlusId')
                        .then(res => {
                            this.NumberOfUnreadConversationsWasLoad = true
                            
                            this.NumberOfUnreadConversations = res.data.numberOfUnreadConversations
                            this.MessageSound = new Audio(res.data.messageSound)
                            if (this.browser !== 'Chrome'){
                                this.MessageSound.volume=0.2
                            }
                            window.Echo.private(`chat_${res.data.UserId}`).listen('.chat', (res) => {

                                if (this.$route.name == 'user.messageTo' && res.message.senderInfo.id == this.$route.params.id) {
                                    this.MessageToPush = res.message
                                    axios.post(`/api/messages/userRead/${res.message.conversation_id}`)
                                } else if (this.$route.name == 'user.messages'){
                                    this.MessageToPush = res.message
                                    setTimeout(() => {
                                        this.MessageSound.play()
                                    }, 1200);
                                } else {
                                    this.getNumberOfUnreadConversations()
                                    this.MessageSound.play()
                                    res.message.timeToDisplay = 5000
                                    res.message.shouldDisplay = true
                                    
                                    this.display=true
        
                                    if (this.messagesToDisplay.length > 0 && this.messagesToDisplay.length < 5) {
                                        this.messagesToDisplay.push(res.message) 
                                    } else if (this.messagesToDisplay.length > 0 && this.messagesToDisplay.length == 5) {
                                        this.messagesToDisplay.splice(0,1)
                                        this.messagesToDisplay.push(res.message) 
                                    } else {
                                        this.$nextTick(() => {
                                            this.messagesToDisplay.push(res.message)
                                            this.loopForDisplay(res.message)
                                        })
                                    }
                                }
                            })
                        })
                        .catch(err => {
                            console.log(err)
                        })
                        
                    }, 250);
                }
            }
        },

        loopForDisplay(){
            var elementsToDelete = []
            this.messagesToDisplay.forEach((element)  => {
                
                if (element.timeToDisplay > 0) {
                element.timeToDisplay = element.timeToDisplay - 1000
                } else {
                    elementsToDelete.push(element)
                }
            })

            elementsToDelete.forEach(element => {
                this.messagesToDisplay.splice(this.messagesToDisplay.indexOf(element),1)
            });

            if (this.messagesToDisplay.length > 0) {
                setTimeout(() => {
                    this.loopForDisplay()
                }, 1000)
            } else {
                setTimeout(() => {
                    this.display=false
                }, 1000);
            }
        },

        getNumberOfUnreadConversations(){
            axios.get('/api/messages/getNumberOfUnreadConversations')
                .then(res => {
                    this.NumberOfUnreadConversations = res.data
                })
                .catch(err => {
                    console.log(err)
                })
        },

        detectbrowser(){
            if((navigator.userAgent.indexOf("Opera") || navigator.userAgent.indexOf('OPR')) != -1 ) {
                this.browser = 'Opera';
            }
            else if(navigator.userAgent.indexOf("Edg") != -1 ){
                this.browser = 'Edge';
            }
            else if(navigator.userAgent.indexOf("Chrome") != -1 ){
                this.browser = 'Chrome';
            }
            else if(navigator.userAgent.indexOf("Safari") != -1){
                this.browser = 'Safari';
            }
            else if(navigator.userAgent.indexOf("Firefox") != -1 ) {
                this.browser='Firefox';
            }
            else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) {
                this.browser = 'IE';
            }  
            else {
                this.browser = 'unknown';
            }
        },


        ReduceNumberOfUnreadConversations(){
            if (this.NumberOfUnreadConversations>0){
                this.NumberOfUnreadConversations = this.NumberOfUnreadConversations - 1
            }
        },

        goToTheUserPage(message){
            var messagesToDelete = []
            this.messagesToDisplay.forEach(element => {
                if (element.senderInfo.id == message.senderInfo.id) {
                    messagesToDelete.push(element)
                }
            })
            messagesToDelete.forEach(element => {
                this.messagesToDisplay.splice(this.messagesToDisplay.indexOf(element),1)
            });
            
            this.NumberOfUnreadConversations --
            this.$router.push( this.$Tr.i18nroute({ name: 'user.messageTo', params: { id: message.senderInfo.id }}))
        }

    },

}
</script>

<style scoped>

    .messages-list-item{
        display: inline-block;
        margin-right: 10px;
    }

    .messages-list-enter-active, .messages-list-leave-active {
        transition: all 1s ease;
    }

    .messages-list-enter-from, .messages-list-leave-to {
        opacity: 0;
        transform: translateX(30px);
    }

    .Username{
        text-align: center;
    }

    .messageText{
        display: table-cell;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 1px;
    }

    .userInfo{
        display: table-cell;
        width: 30%;
    }

    img{
        border-radius: 20%;
    }

    .message{
        background-color: rgb(186, 135, 162);
        border-radius: 8px;
        width: 100%;
        margin-bottom: 10px;
        height: 70px;
        display: table;
        padding-left: 4px;
        padding-top: 5px;
        cursor: pointer;
    }

    .messagesToDisplay{
        padding: 8px;
        position: fixed;
        width: 250px;
        height: 500px;
        min-height: 500px;
        bottom: 100px;
        right: 80px;
    }

    .rightbar {
        min-height: 720px;
        height: 841px;
    }

    .eiheight{
        height:90%;
    }
    .leftbar {
        padding-right:0;
        min-width: 170px;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.5s ease-out;
    }
    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }

    .marginLeftForPlayer{
        margin-left: v-bind(marginLeftForPlayer);
        transition: 1s;
    }
</style>

<style>
    
    .roll::-webkit-scrollbar {
        width: 12px;
        background-color:  #cda7a7;
        border-radius: 10px;
    }

    .roll::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        border-radius: 10px;
    }

    .roll::-webkit-scrollbar-thumb {
        border-radius: 14px;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
    }

    .unselectable {
        user-select: none;                                       
    }

    .pointer {
        cursor: pointer;
    }
</style>