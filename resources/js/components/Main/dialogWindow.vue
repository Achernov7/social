<template>
    <div class="w-75 mx-auto d-flex justify-content-center h-100 minwidth">
        <vueTitle v-if="InformationAboutReceiver" :title="InformationAboutReceiver.name"></vueTitle>
            <div class="me-4 mt-4" v-if="InformationAboutReceiver" style="width: 240px;">
                <div v-if="lastOnline" style="text-align: center;" class="mb-2">
                    <span v-if="lastOnline !='Online'">
                        <span>
                            {{ $t('additional.Was_online') }}:
                        </span>
                        <span class="ms-1">
                            {{ lastOnline }}
                        </span>
                    </span>
                    <span v-else>
                        {{ $t('additional.Online') }}
                    </span>
                </div>
                <img :src="InformationAboutReceiver.image.preview_url" alt="Avatar"  @click ="this.$router.push( this.$Tr.i18nroute({ name: 'user.profileOf', params: { id: InformationAboutReceiver.id }}))">
                    <div class="mt-3 usersNameSurname">
                        <span class="me-1">
                            {{InformationAboutReceiver.name}}
                        </span> 
                        <span class="ms-2">
                            {{InformationAboutReceiver.surname}}
                        </span>
                    </div>
                    <div class="d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-center" @click="this.$router.push( this.$Tr.i18nroute({name: 'user.messages'}))">
                            <div class="back">
                                <font-awesome-icon icon='fa-reply' size="xl" class="me-1"/> 
                                {{$t('additional.Back')}}
                            </div>
                        </div>
                        <div @click="PushToTheBottom()" class="d-flex justify-content-center">
                            <div :class="{'back': true, 'InViewPort':changeBackColourOfButton}">
                                <font-awesome-icon icon='fa-down-long' size="xl" class="me-1"/> 
                            </div>
                        </div>
                    </div>
            </div>
            <div class="d-flex flex-column justify-content-start h-100 w-50">
                <div class="mb-2 backOfMessages">
                    <div class="d-table-cell verticalToBottom">
                        <div class="forHeight" @scroll="checkAndChangeVariablechangeBackColourOfButton()" ref="messagesback">
                            <div class="mb-3" v-for="message in Messages" :key="message.id">
                                    <div class="d-flex justify-content-between" v-if="message.whose_message == 'this is receivers message'">
                                        <span class="ms-3">
                                            {{InformationAboutReceiver.name}}
                                        </span>
                                        <span class ='time me-3'>
                                            {{message.time}}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between" v-if="message.whose_message == 'this is senders message'">
                                        <span class ='time ms-3'>
                                            {{message.time}}
                                        </span>
                                        <span class="me-3">
                                            {{ $t('additional.You') }}
                                        </span>
                                    </div>
                                    <div v-if="Messages.indexOf(message) == Messages.length - 1" class="lastElem" ref="lastElement"></div>
                                    <div :class="{
                                        'Messages':true,
                                        'sendersMessage': message.whose_message == 'this is senders message',
                                        'receiversMessage': message.whose_message == 'this is receivers message'
                                    }">
                                        <div :class="{
                                            'leftMessage': message.whose_message == 'this is receivers message',
                                            'rightMessage': message.whose_message == 'this is senders message'
                                        }">
                                            <span v-html="message.chat_message"></span>
                                        </div>
                                    </div>
                                    <div v-if="Messages.indexOf(message) == this.obersverLine" v-intersection="GetMessages" class="observer" ref="observer"></div> 
                            </div>
                        </div>
                    </div>
                </div>
                <span class="w-100">
                    <ChatMessage :defineAxiosString="`/api/messages/saveMessage/${this.$route.params.id}`" @messageWasSent="(e)=>AddMessage(e)"></ChatMessage>
                </span>
            </div>
            <div>
            </div>
            <div class="d-flex flex-column justify-content-end h-100 ms-4" v-if="InformationAboutSender">
                <div class="mb-5">
                    <img :src="InformationAboutSender.image.preview_url" alt="Avatar"  @click ="this.$router.push( this.$Tr.i18nroute({ name: 'user.profileOf', params: { id: InformationAboutSender.id }}))">
                    <div class="mt-3 usersNameSurname">
                        <span>
                            {{ $t('additional.You') }}
                        </span>
                    </div>
                </div>
            </div>
    </div>
</template>
<script>

import axios from 'axios';
import ChatMessage from '@/components/UI/ChatMessage.vue';
export default {

    props: ['MessageToPush'],

    components: {
        ChatMessage
    },

    watch: {
        MessageToPush(){
            
            this.MessageToPush.whose_message = 'this is receivers message'
            
            if (this.diffInhours != null){
                this.Messages.push(this.recalculateHoursForSingItem(this.MessageToPush))
            } else {
                this.Messages.push(this.MessageToPush)
            }

            if (this.isElementXPercentInViewport(this.$refs.lastElement[0] , 70)){
                this.PushToTheBottom()
            } else {
                this.changeBackColourOfButton = true
            }
        }
    },

    mounted() {
        this.GetMessages()
    },


    data() {
        return {
            page: 0,
            limit: 10,
            InformationAboutSender: null,
            InformationAboutReceiver:null,
            Messages:[],
            obersverLine:1,
            stopFetch: false,
            IdsOfMessages: [],
            changeBackColourOfButton: false,
            lastOnline: null,
            handlerFor1MinuteAgo:null,
            diffInhours: null
        }
    },

    methods: {
        GetMessages(){
            if (!this.stopFetch){
                let params = {page: this.page, limit: this.limit, IdsOfMessages: this.IdsOfMessages}
                
                if (this.page == 0){
                    params.date = new Date().toISOString()
                }

                axios.get(`/api/messages/getMessagesWithUser/${this.$route.params.id}`, { params:params})
                    .then(res=>{

                        if (res.data.messages == 'reciever_id is not valid'){
                            this.$router.push(this.$Tr.i18nroute({ name: 'user.messages'}))
                        }

                        if (res.data.diffInhours){
                            if (res.data.diffInhours > -24 && res.data.diffInhours < 24){
                                this.diffInhours = res.data.diffInhours
                            }
                        }

                        if (res.data.messages == 'sender_id is not valid'){
                            this.$router.push(this.$Tr.i18nroute({ name: 'user.messages'}))
                        }
                        
                        if (res.data.lastReceiverActivity){
                            if (res.data.lastReceiverActivity == 'Online'){
                                this.handlerFor1MinuteAgo = setTimeout(() => {
                                    this.lastOnline = 'more than 1 minute ago'
                                }, 60000);
                            }
                            this.lastOnline = res.data.lastReceiverActivity
                        }
                        
                        if (res.data.Receiver){
                            
                            this.InformationAboutReceiver = res.data.Receiver
                                window.Echo.channel(`user_is_online_${res.data.Receiver.id}`)
                                    .listen('.user_is_online', res => {
                                        if (res.message == 'User is online'){                                            
                                            this.lastOnline = 'Online'
                                            clearTimeout(this.handlerFor1MinuteAgo)
                                            this.handlerFor1MinuteAgo = setTimeout(() => {
                                                this.lastOnline = 'more than 1 minute ago'
                                            }, 60000);
                                        }
                                    }); 
                        }
                        if (res.data.Sender){
                            this.InformationAboutSender = res.data.Sender
                        }

                        if ( this.diffInhours != null){
                            res.data.data = this.recalculateHoursForArray(res.data.data)
                        }

                        this.Messages = [...res.data.data, ...this.Messages]
                        res.data.data.forEach(element => {
                            this.IdsOfMessages.push(element.id)
                        })
                        if (this.page == 0 || this.page == 1){
                             
                            this.$nextTick(() => {   
                                this.$refs.messagesback.scrollTo({
                                    top:  this.$refs.messagesback.scrollHeight,
                                });
                                
                            })
                        } else {
                            this.$nextTick(() => {   
                                this.$refs.messagesback.scrollTo({
                                    top: 900,
                                });
                                
                            })
                        }
                        
                        if (res.data.data.length < this.limit){
                            this.stopFetch = true
                        }
                        this.page = this.page + 1

                    })
                    .catch(err=>{
                        console.log(err);
                    })  
            }
        },

        AddMessage(e){
            if (this.diffInhours != null){
                e.data = this.recalculateHoursForSingItem(e.data) 
            }
            this.Messages.push({chat_message: e.data.message, whose_message: 'this is senders message', time: e.data.time})

            this.$nextTick(() => {
                this.$refs.messagesback.scrollTo({
                    top: this.$refs.messagesback.scrollHeight,
                    behavior: 'smooth'
                });
            })
        },

        PushToTheBottom(){
            this.changeBackColourOfButton = false
            this.$nextTick(() => {
                this.$refs.messagesback.scrollTo({
                    top: this.$refs.messagesback.scrollHeight,
                    behavior: 'smooth'
                });
            });
        },

        isElementXPercentInViewport(el, percentVisible){
            let rect = el.getBoundingClientRect(),
            windowHeight = (window.innerHeight || document.documentElement.clientHeight);
            return !(
                Math.floor(100 - (((rect.top >= 0 ? 0 : rect.top) / +-rect.height) * 100)) < percentVisible ||
                Math.floor(100 - ((rect.bottom - windowHeight) / rect.height) * 100) < percentVisible
            )

        },

        checkAndChangeVariablechangeBackColourOfButton(){
            if (this.changeBackColourOfButton){
                if (this.isElementXPercentInViewport(this.$refs.lastElement[0] , 70)){
                    this.changeBackColourOfButton = false
                }
            }
        },

        recalculateHoursForArray(data){
            let newData = data.map(element => {
                element = this.recalculateHoursForSingItem(element)
                return element
            });
            return newData
        },

        recalculateHoursForSingItem(element){
            if (!element.time.includes("ago")){
                    let hours = element.time.split(":")[0]
                    let minutes = element.time.split(":")[1]

                    let newHour = parseInt(hours)+this.diffInhours
                    if (newHour >= 24){
                        newHour = newHour - 24
                    }
                    if (newHour < 10){
                        newHour = `0${newHour}`
                    }
                    if (newHour < 0){
                        newHour = 24 - newHour
                    }
                    element.time = `${newHour}:${minutes}`
                }
                
            return element
        }
        
    },

    beforeRouteLeave (to, from, next) {
        window.Echo.leave(`user_is_online_${this.InformationAboutReceiver.id}`)
        next()
    },

}
</script>

<style scoped>

    .minwidth{
        min-width: 900px;
    }

    .InViewPort{
        -webkit-box-shadow:inset 0px 0px 0px 2px #f00;
        -moz-box-shadow:inset 0px 0px 0px 2px #f00;
        box-shadow:inset 0px 0px 0px 2px #f00;
    }

    .forHeight{
        overflow: auto;
        overflow-x: hidden;
        max-height: 720px;
    }

    .verticalToBottom{
        vertical-align: bottom;
    }

    .back{
        margin-top: 40px;
        background: linear-gradient(106deg, rgba(237,198,156,1) 0%, rgba(221,152,146,1) 47%, rgba(109,100,205,1) 100%);
        border-radius: 15px;
        width: 40%;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 1s;
        opacity: 70%;
    }

    .back:hover{
        opacity: 100%;
    }

    .receiversMessage{
        text-align: left;
        margin-left: 12px;
        background: linear-gradient(16deg, rgba(237,198,156,1) 0%, rgba(221,152,146,1) 85%, rgba(109,100,205,1) 100%);
    }

    .sendersMessage{
        text-align: right;
        margin-right: 12px;
        background: linear-gradient(164deg,rgba(109,100,205,1) 0%, rgba(221,152,146,1) 15%,  rgba(237,198,156,1) 100%);
    }

    .time{
        font-size:0.6rem;
        display: inline-flex;
        align-items: end;
    }

    .Messages{
        position: relative;
        border-radius: 8px;
        padding:4px 15px;
        margin:4px 12px;
        min-height: 35px;
        word-break: break-all;
    }

    .Messages .leftMessage:before{
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 10px 10px 10px;
        border-color: transparent transparent rgba(237,198,156,1) transparent;
        left: -9px;
        bottom: 0px;
    }

    .Messages .rightMessage:after{
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 10px 10px 10px;
        border-color: transparent transparent rgba(237,198,156,1) transparent;
        right: -9px;
        bottom: 0px;
    }

    .usersNameSurname{
        text-align: center;
    }

    img{
        border-radius: 50%;
        cursor: pointer;
    }

    .backOfMessages{
        display: table;
        vertical-align: bottom;
        background-color: rgba(221,152,146,1);
        border-radius: 8px;
        min-width: 400px;
        height: 720px;
    }

    .forHeight::-webkit-scrollbar {
        display: none;
    }


</style>