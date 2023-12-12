<template>
    <div class="w-75 mx-auto d-flex justify-content-center h-100">
        <vueTitle :title="$t('titles.Messages')"></vueTitle>
        <div class="d-flex flex-column justify-content-start h-100 messagewrap">
            <div class="d-flex" style="min-width: 500px;">
                <SearchingPersonField @keyup="StopCheckAndGetListOfMessages()" v-model="searchingUser"></SearchingPersonField>
            </div>
            <div class="listOfMessages" ref="messages">
                <TransitionGroup name="users-list">
                    <div v-for="message in messages" :key="message.conversation_id">
                        <div @click ="PushToTheUsersPage(message)" class="message pointer" >
                            <span class="avatar">
                                <img alt="Avatar" :src="message.receiverInfo.mini_image.mini_url">
                            </span>
                            <span class="d-inline-block ms-3">
                                <div>
                                    <span class="ms-2 me-2">
                                        {{ message.receiverInfo.name }}
                                    </span>
                                    <span>
                                        {{ message.receiverInfo.surname }}
                                    </span>
                                </div>
                                <div class="wrapMess" :style="{ color: message.whose_message === 'this is receivers message' && message.receiver_saw != 1 ? 'AntiqueWhite' : '' }">
                                    <div class="miniImage">
                                        <div class="d-flex flex-column justify-content-center h-100">
                                            <div>
                                                <img class="ms-2" v-if="message.whose_message === 'this is senders message'" alt="Avatar" :src="message.senderInfo.micro_image.micro_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-2 chat_message">
                                        <div v-html="message.chat_message" style="max-height: 42px; word-break: break-all;"/> 
                                    </div>
                                </div>
                            </span>
                        </div>
                        <div v-if="messages.indexOf(message) == this.obersverLine" v-intersection="intersectionGetListOfMessages" class="observer" ref="observer"></div>
                    </div>
                </TransitionGroup>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import SearchingPersonField from '@/components/UI/SearchingPersonField.vue';
import StartStopLoadingFavicon from '@/Mixins/StartStopLoadingFavicon';
export default {

    mixins: [StartStopLoadingFavicon],

    props: [
        'MessageToPush'
    ],

    watch: {
        MessageToPush(){
            this.MessageToPush.whose_message = 'this is receivers message'
            var wasUnreaden = false
            this.messages.map(element => {
                if (element.conversation_id == this.MessageToPush.conversation_id && element.whose_message == 'this is receivers message' && element.receiver_saw != 1){
                    wasUnreaden = true
                }
            })
            if (!wasUnreaden){
                this.$emit('increaseNumberOfMessages', true)
            }
        },
    },

    mounted() {
        this.GetListOfMessages()
        this.controller = new AbortController
    },

    components: {
        SearchingPersonField
    },

    data() {
        return {
            messages: [],
            shouldLoadMore: true,
            limit: 10,
            DialogIds: [],
            obersverLine: 0,
            searchingUser: '',
            axiosAlreadyHandled: false,
            lastSearchOfList: '',
            checkingIsTurnedOff: true,
            tryToStartGetListOfMessagesIsTurnOn:false,
            stopCheckingWasSentToDataBase:false,
            stopCheckingWasSentFromFront:false,
            countOfCallingCheck: 0,
            handleOftryToStartGetListOfMessages: null,
            controller: null,
            timerOftryToStartGetListOfMessages: 0
        }
    },

    methods: {

        StopCheckAndGetListOfMessages(){
            if (this.countOfCallingCheck != 0){
                if (!this.stopCheckingWasSentFromFront && !this.checkingIsTurnedOff && !this.tryToStartGetListOfMessagesIsTurnOn){
                    this.stopCheckingWasSentFromFront = true
                    this.stopChecking()
                    this.tryToStartGetListOfMessagesIsTurnOn = true
                    this.tryToStartGetListOfMessages()
                }
            } else {
                setTimeout(() => {
                    this.StopCheckAndGetListOfMessages()
                }, 300);
            }
        },

        tryToStartGetListOfMessages(){
            
            if (this.stopCheckingWasSentToDataBase && this.checkingIsTurnedOff){
                this.GetListOfMessages()
            } else {
                
                if (this.timerOftryToStartGetListOfMessages < 4){
                    this.timerOftryToStartGetListOfMessages++
                    this.handleOftryToStartGetListOfMessages = setTimeout(() => {
                        this.tryToStartGetListOfMessages()
                    }, 300);
                } else {
                    this.timerOftryToStartGetListOfMessages = 0
                    this.tryToStartGetListOfMessagesIsTurnOn = false
                    this.stopCheckingWasSentToDataBase = false
                    this.stopCheckingWasSentFromFront = false
                    
                    clearTimeout(this.handleOftryToStartGetListOfMessages)
                    this.StopCheckAndGetListOfMessages()
                }
            }
        },

        intersectionGetListOfMessages(){
            var thisIsIntersiction = true
            this.GetListOfMessages(thisIsIntersiction)
        },

        List(thisIsIntersiction){

            this.StartLoadingFavicon()

            axios.get('/api/messages/', { params:{ limit: this.limit, DialogIds: this.DialogIds, searchingUser: this.searchingUser},
                signal: this.controller.signal})
                .then(response => {
                    
                    this.StopLoadingFavicon()
                    
                    if (response.data.SearchingUser === null){
                        response.data.SearchingUser = ''
                    }

                    let searchingUserWithoutBr = this.searchingUser.replace('<br>', '')
                    if (response.data.SearchingUser !== searchingUserWithoutBr){
                        this.GetListOfMessages()
                    } else {
                        this.lastSearchOfList = response.data.SearchingUser

                        if (response.data.data.length < this.limit){
                            this.shouldLoadMore = false
                        }
                        response.data.data.forEach(element => {
                            this.DialogIds.push(element.conversation_id)
                        })

                        this.messages = [...this.messages, ...response.data.data]
                        this.page = this.page + 1
                        this.obersverLine = this.messages.length-2

                        this.checkingIsTurnedOff = false
                        this.stopCheckingWasSentToDataBase = false
                        
                        if (!thisIsIntersiction == true){
                            setTimeout(() => {
                                this.countOfCallingCheck++
                            }, 300);
                            this.checkListOfMessages()
                        }

                    }
                        
                })
                .catch((error)=>{
                    console.log(error);
                })          
        },

        GetListOfMessages(thisIsIntersiction) {
            let searchingUserWithoutBr = this.searchingUser.replace('<br>', '')
            if (searchingUserWithoutBr !== this.lastSearchOfList){
                this.DialogIds = []
                this.messages = []
                this.shouldLoadMore = true
            }
            
            if (!thisIsIntersiction){     
                clearTimeout(this.axiosAlreadyHandled)
                this.axiosAlreadyHandled = setTimeout(() => {
                    this.List(thisIsIntersiction)
                }, 400)
            } else {
                if (this.shouldLoadMore){
                    if (this.tryToStartGetListOfMessagesIsTurnOn == false){
                        this.List(thisIsIntersiction)
                    }
                } 
            }
        },

        checkListOfMessages() {
            var latestTimestampForAxios = this.latestTimestamp()
            this.tryToStartGetListOfMessagesIsTurnOn = false
            this.stopCheckingWasSentFromFront = false

                axios.get('/api/messages/check', { params:{ limit: this.limit, latestTimestamp: latestTimestampForAxios, searchingUser: this.lastSearchOfList},
                    signal: this.controller.signal})
                    .then(response => {
                        
                        if (typeof response === 'undefined') {
                            setTimeout(() => {
                                this.checkListOfMessages()
                            })
                        } else {
                            if (typeof response.data.stop_check_messages === 'undefined'){
                                setTimeout(() => {
                                    this.checkListOfMessages()
                                });
                            } else {
                                this.checkingIsTurnedOff = true
                            }

                            if (response.data.data ){
                                response.data.data.forEach(element => {
                                    
                                    var alredypushed = false
                                    if (this.messages.length === 0){
                                        this.messages.unshift(element)
                                    } else {
                                        this.messages.forEach((message, idx, array) => {
                                            if (idx === array.length - 1){
                                                if (message.conversation_id === element.conversation_id){
                                                    this.addToTheList(element)
                                                } else {
                                                    if (!alredypushed){
                                                        this.messages.unshift(element)
                                                        this.DialogIds.push(element.conversation_id)
                                                    }
                                                }
                                            } else if (message.conversation_id === element.conversation_id){
                                                this.addToTheList(element)
                                                alredypushed = true
                                            }
                                        });
                                    }
                                });
                            }
                        }

                    })
                    .catch(err=>{
                        if (err.message !== 'canceled'){
                            console.log(err);
                        }
                    })

        },

        latestTimestamp(){
            var timestamp = 0
            this.messages.forEach(element => {
                if (element.timestamp > timestamp){
                    timestamp = element.timestamp
                }
            })

            return timestamp
        },

        addToTheList(message) {
            this.messages.splice(this.messages.findIndex(x => x.conversation_id == message.conversation_id), 1)
            this.messages.unshift(message)
        },

        stopChecking(){
            axios.post('/api/messages/stopCheck')
                .then(res=>{
                    if (res.data.message == 'stop_check_chat_messages'){
                        this.stopCheckingWasSentToDataBase = true
                    }
                })
        },

        PushToTheUsersPage(message){
            if (message.whose_message === 'this is receivers message' && message.receiver_saw != 1){
                this.$emit('ReduceNumberOfMessages', true)
            }
            this.$router.push( this.$Tr.i18nroute({ name: 'user.messageTo', params: { id: message.receiverInfo.id }}))
        },
        
    },

    beforeRouteLeave (to, from, next) {
        this.StopLoadingFavicon();
        this.stopChecking()
        this.controller.abort()
        next()
    },

}
</script>
<style scoped>

    .users-list-item {
        display: inline-block;
        margin-right: 10px;
    }

    .users-list-enter-active {
        transition: all 1s ease;
    }

    .users-list-enter-from {
        opacity: 0 !important;
        transform: translateX(100px);
    }

    .searchingBar{
        background-color:rgb(210, 162, 162);
        border-radius: 7px;
        width: 100%;
        display: inline-block;
        text-align: center;
        overflow: hidden;
        height: 23px;
    }

    .miniImage{
        display: table-cell;
    }
    .wrapMess{
        padding: 0 2px;
        overflow: hidden;
        display: table;
        height: 1px;
    }
    .chat_message{
        padding-left: 4px;
        display: table-cell;
        word-wrap: break-word;
        vertical-align: middle;
        box-sizing: border-box;
    }


    .avatar {
        display: table-cell;
        vertical-align: middle; 
        width: 5%;
    }

    .message img {
        border-radius: 50%;
    }

    .message {
        background-color: rgb(210, 162, 162);
        border-radius: 8px;
        padding: 5px 10px;
        margin: 7px 7px;
        display: table;
        width: 98%;
    }
    .messagewrap {
        width: 60%;
    }

    .listOfMessages{
        background-color: rgba(221,152,146,1);
        height: 810px;
        border-radius: 8px;
        overflow: auto;
        overflow-x: hidden;
        min-width: 500px;
    }
    .listOfMessages::-webkit-scrollbar {
        display: none;
    }
</style>