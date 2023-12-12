<template>
    <div>
        <div class="usersList" ref="userList" v-hiddenOverflow>
            <Loading v-if="isUsersLoading" :height="'2.5em'" :width="'2.5em'" :textSize="'1em'" :modelSize="'1.1em'" ></Loading>
            <TransitionGroup name="users-list" >
                <div @click="PushToTheUserPage(user.id)" v-for="user in users" :key="user.id" class="usersWrap">
                    <div v-if="this.firstFriend">
                        <div @click.stop v-if ="user.id == this.firstFriend.id" class="headForFriendsAndOthers">
                            <span class="Friends">
                                {{ $t('additional.Friends') }}
                            </span>
                        </div>
                    </div>
                    <div v-if="this.firstSubscriber">
                        <div @click.stop v-if = "user.id == this.firstSubscriber.id" class="headForFriendsAndOthers">
                            <span class="Friends">
                                {{ $t('additional.Subscribers') }}
                            </span>
                        </div>
                    </div>
                    <div v-if="this.firstSubscribeTo">
                        <div @click.stop v-if = "user.id == this.firstSubscribeTo.id" class="headForFriendsAndOthers">
                            <span class="Friends">
                                {{ $t('additional.You_subscribed_to') }}
                            </span>
                        </div>
                    </div>
                    <div v-if="this.firstWithNoRelationShip">
                        <div @click.stop v-if = "user.id == this.firstWithNoRelationShip.id" class="headForFriendsAndOthers">
                            <span class="Friends">
                                {{ $t('additional.Global_search') }}
                            </span>
                        </div>
                    </div>
                    <div :class="{'greenCircle': ((this.firstWithNoRelationShip && user.id != this.firstWithNoRelationShip.id) && (this.firstSubscriber &&this.firstSubscriber.id != user.id) && (this.firstSubscribeTo && this.firstSubscribeTo.id != user.id) && (this.firstFriend && this.firstFriend.id != user.id)), 
                        'greenCircleForFirstInGroup': ((this.firstWithNoRelationShip && user.id == this.firstWithNoRelationShip.id) || (this.firstSubscriber && this.firstSubscriber.id == user.id) || (this.firstSubscribeTo && this.firstSubscribeTo.id == user.id) || (this.firstFriend && this.firstFriend.id == user.id))}" v-if="user.online"/>
                    <span class="col-1 ms-3">
                        <img alt="Avatar" :src="user.mini_image.mini_url">
                    </span>
                    <span class='name col-2 me-1' style="overflow: clip; white-space: nowrap; text-overflow: ellipsis;">
                        {{ user.name }}
                    </span>
                    <span class="col-2 me-2" style="overflow: clip; white-space: nowrap; text-overflow: ellipsis;">
                        {{ user.surname }}
                    </span>
                    <span class="col-2" style="overflow: clip; white-space: nowrap; text-overflow: ellipsis;">
                        {{ user.town }}
                    </span>
                    <span class="message" @click.stop="this.$router.push( this.$Tr.i18nroute({ name: 'user.messageTo', params: { id: user.id }}))">
                        <font-awesome-icon icon='fa-solid fa-envelope' size="xl"/> 
                    </span>
                    <div v-if="users.indexOf(user) == this.obersverLine" v-intersection="LoadMoreUsers" class="observer" ref="observer"></div> 
                </div>
            </TransitionGroup>
        </div>
    </div>
</template>
<script>
import PushToTheUserPage from '@/Mixins/PushToTheUserPage'
export default {
    
    mixins: [PushToTheUserPage],

    props: [
        'searchingUser', 
        'searchedTown', 
        'valueFrom', 
        'valueTo', 
        'gender', 
        'familystatus', 
        'searchfield'
    ],

    data() {
        return {
            users:[],
            isUsersLoading: false,
            timeoutForUserLoading:'',
            page: 0,
            limit: 20,
            obersverLine: 0,
            stopFetchFriends: false,
            stopFetchSubscribers: false,
            stopFetchSubscribeTo: false,
            stopFetchOtherUsers: false,
            propsAlredayHandled: ''
        }
    },
    

    methods: {
        LoadMoreUsers() {
            var additionalSearchParams = []
            if (this.searchingUser){
                var UserForSearch = this.searchingUser.substring(0 , 70)  
            }
            if (UserForSearch){
                additionalSearchParams.push({nameOfUserToSearch: UserForSearch})
            }
            if (this.searchfield == 'Search'){

                if (this.searchedTown){

                    additionalSearchParams.push({town: this.searchedTown})
                }
                if (this.valueFrom && this.valueFrom !== 'From'){
                    additionalSearchParams.push({ageFrom: this.valueFrom})
                }
                if (this.valueTo && this.valueTo !== 'To'){
                    additionalSearchParams.push({ageTo: this.valueTo})
                }
                if (this.gender){
                    additionalSearchParams.push({gender: this.gender})
                }
                if (this.familystatus){
                    additionalSearchParams.push({familystatus: this.familystatus})
                }
            }


            if (!this.stopFetchFriends ){
                this.fetchUsers('/api/friends/getFriendsOfAuthWithPagination/', 'stopFetchFriends', additionalSearchParams)
            }

            if (this.stopFetchFriends && !this.stopFetchSubscribers){
                this.fetchUsers('/api/friends/getSubscribersOfAuthWithPagination/', 'stopFetchSubscribers', additionalSearchParams)
            }

            if (this.stopFetchFriends && this.stopFetchSubscribers && !this.stopFetchSubscribeTo){
                this.fetchUsers('/api/friends/getSubscribedToOfAuthWithPagination/', 'stopFetchSubscribeTo', additionalSearchParams)
            }

            if (this.stopFetchFriends && this.stopFetchSubscribers && this.stopFetchSubscribeTo && !this.stopFetchOtherUsers){
                this.fetchUsers('/api/friends/getUsersWithNoRelationshipWithAuthWithPagination/', 'stopFetchOtherUsers', additionalSearchParams)
            }    
        },

        fetchUsers(AxiosString, WhatStopToFetch, additionalSearchParams){
            this.isUsersLoading = true,
            axios.get ( AxiosString, {params:{ page:this.page, limit:this.limit, additionalParams: additionalSearchParams}})
                .then(response => {
                    
                    this.page ++
                    this.users = [...this.users, ...response.data]
                    clearTimeout(this.timeoutForUserLoading)
                    this.timeoutForUserLoading = setTimeout(() => {
                        this.isUsersLoading = false
                    }, 700)

                    if (this.users.length > 0) {
                            setTimeout(() => {
                                this.obersverLine = this.users.length-1
                            })
                        }
                    if (response.data.length !== this.limit) {

                        this.page = 0
                        if (this.users.length > 0) {
                            this.obersverLine = this.users.length+5000
                        } else if (AxiosString === '/api/friends/getFriendsOfAuthWithPagination/') {
                            this.fetchUsers('/api/friends/getSubscribersOfAuthWithPagination/', 'stopFetchSubscribers', additionalSearchParams)
                        } else if (AxiosString === '/api/friends/getSubscribersOfAuthWithPagination/') {
                            this.fetchUsers('/api/friends/getSubscribedToOfAuthWithPagination/', 'stopFetchSubscribeTo', additionalSearchParams)
                        } else if (AxiosString === '/api/friends/getSubscribedToOfAuthWithPagination/') {
                            this.fetchUsers('/api/friends/getUsersWithNoRelationshipWithAuthWithPagination/', 'stopFetchOtherUsers', additionalSearchParams)
                        }

                        switch (WhatStopToFetch) {
                            case "stopFetchFriends":
                                this.stopFetchFriends = true
                                this.obersverLine = this.users.length-2
                                break;
                                case "stopFetchSubscribers":
                                    this.stopFetchSubscribers = true
                                    this.obersverLine = this.users.length-2
                                    break;
                                    case "stopFetchSubscribeTo":
                                        this.stopFetchSubscribeTo = true
                                        this.obersverLine = this.users.length-2
                                        break;
                                        case "stopFetchOtherUsers":
                                            this.stopFetchOtherUsers = true
                                            break;               
                        }
                    }
                    
                })
                .catch(err => {
                    console.log(err)
                })
        },
    },

    computed : {
        firstFriend() {
            return this.users.find((elem)=>{
                return elem.role == 'friends'
            })
        },
        firstSubscriber() {
            return this.users.find((elem)=>{
                return elem.role == 'YoursSubscriber'
            })
        },
        firstSubscribeTo() {
            return this.users.find((elem)=>{
                return elem.role == 'YouSubscribeTo'
            })
        },

        firstWithNoRelationShip() {
            return this.users.find((elem)=>{
                return elem.role == 'WithNoRelationship'
            })
        },
    },

    watch: {
        $props: {
            handler() {
                clearTimeout(this.propsAlredayHandled);     
                this.propsAlredayHandled = setTimeout(() => {
                    this.users = []
                    this.stopFetchFriends = false,
                    this.stopFetchSubscribers = false,
                    this.stopFetchSubscribeTo = false,
                    this.stopFetchOtherUsers = false,
                    this.page = 0
                    this.obersverLine = 0
                    this.LoadMoreUsers();
                    this.propsAlredayHandled = false
                }, 600);
            },
            deep: true,
            immediate: true
        },
        searchfield: {
            handler() {
                setTimeout(() => {
                    clearTimeout(this.propsAlredayHandled);
                });
            }
        }
    }

}
</script>
<style scoped>

    .greenCircleForFirstInGroup{
        position: absolute;
        background-color:  green;
        border-radius: 50%;
        width: 10px;
        height: 10px;
        top: 31px;
        left: 64px;
    }

    .greenCircle{
        position: absolute;
        background-color: green;
        border-radius: 50%;
        width: 10px;
        height: 10px;
        top: 10px;
        left: 64px;
    }

    .headForFriendsAndOthers{
        background-color: #bc7e7e;
        position: relative;
        bottom: 5px;
        right: 3px;
        width: 101%;
    }

    .Friends{
        text-align: center;
        width: 100%;
    }

    span{
        display: inline-block;
    }

    img {
        border-radius: 30%;
    }

    .usersWrap{
        background-color:rgb(194, 138, 138);
        margin: 4px;
        border-radius: 7px;
        padding: 3px;
        cursor: pointer;
        position: relative;
    }

    .message {
        width: 50px;
        height: 35px;
        border-radius: 15px;
        text-align: center;
        line-height:35px;
        transition-duration: .5s;
    }

    .message:hover {
        cursor: pointer;
        background-color: #cda7a7;
        border-radius: 15px;

    }

    .name{
        margin-left: 20%;
    }

    .surname{
        margin-left: 30px
    }

    .users-list-item {
        display: inline-block;
        margin-right: 10px;
    }

    .users-list-enter-active, .messages-list-leave-active {
        transition: all 1s ease;
    }

    .users-list-enter-from, .messages-list-leave-to {
        opacity: 0;
        transform: translateX(30px);
    }

    .usersList{
        background-color: #bc7e7e;
        height: 790px;
        border-radius: 8px;
        margin: 8px;
        padding-top: 2px;
        overflow: hidden;
    }

    .usersList::-webkit-scrollbar {
        width: 12px;
        background-color:  #cda7a7;
        border-radius: 10px;
    }

    .usersList::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        border-radius: 10px;
    }

    .usersList::-webkit-scrollbar-thumb {
        border-radius: 14px;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
    }
</style>