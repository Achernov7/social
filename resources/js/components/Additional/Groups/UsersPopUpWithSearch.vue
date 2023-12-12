<template>
    <SearchingPersonField v-model="searchingUser"/>
    <div class="usersWrap">
        <div style="display: table; width: 380px; border-spacing: 0px 5px; table-layout: fixed;">
            <div v-for="user in users">
                <div style="display: table-row; width: 100%; background-color: rgb(210, 162, 162);">
                    <div style="display: table-cell; border-top-left-radius: 8px; border-bottom-left-radius: 8px; padding:4px 0px">
                        <img class="ms-2" alt="Avatar" :src="user.mini_image.mini_url">
                    </div>
                    <div style="display: table-cell; width: 120px;">
                        {{ user.name }}
                    </div>
                    <div style="display: table-cell; width: 120px;">
                        {{ user.surname }}
                    </div>
                    <div v-if="this.typeOfRequestToButton =='ban'" style="display: table-cell; width: 80px; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                        <font-awesome-icon @click="banOrUnban(user)" icon="fas fa-ban" size="xl" style="cursor: pointer;"/>
                    </div>
                    <div v-if="this.typeOfRequestToButton =='unban'" style="display: table-cell; width: 80px; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                        <font-awesome-icon @click="banOrUnban(user)" icon="fas fa-check" size="xl" style="cursor: pointer;"/>
                    </div>
                </div>
                <div v-if="users.indexOf(user) == users.length - 2" v-intersection="getUsers" class="observer" ref="observer"></div> 
            </div>
        </div>
    </div>
</template>
<script>
import SearchingPersonField from '@/components/UI/SearchingPersonField.vue';
import axios from 'axios';
export default {
    components: {
        SearchingPersonField,
    },

    mounted() {
        this.getUsers()
        if (this.axiosString.includes('getSubscribers')){
            this.typeOfRequestToButton = 'ban'
        } else if (this.axiosString.includes('getBanUsers')){
            this.typeOfRequestToButton = 'unban'
        }
    },

    data() {
        return {
            searchingUser: '',
            page:0,
            users: [],
            stopFetch: false,
            gettingUsers:null,
            typeofComponent: 'UsersPopUpWithSearch',
            typeOfRequestToButton: null,
        }
    },

    watch: {
        searchingUser() {
            this.page = 0
            this.stopFetch = false
            this.users = []
            this.getUsers()
        }
    },

    props:[
        'axiosString',
        'limit',
        'typeOfComponent',
    ],

    methods: {
        getUsers() {
            if (!this.stopFetch){  
                clearTimeout(this.gettingUsers);

                this.gettingUsers = setTimeout(() => {  
                    
                    var params = {page: this.page, limit: this.limit }
                    if (this.users.length > 0){
                        params.IdsOfAlreadyExistedUsers = this.users.map(user => user.id)
                    }
                    if (this.searchingUser.length > 0){
                        params.searchingUser = this.searchingUser
                    }

                    axios.get(`${this.axiosString}`, {
                            params: params
                        })
                        .then(response => {
                            if (response.data.data.length < this.limit){
                                this.stopFetch = true
                            } else {
                                this.page++
                            }
                            this.users = [...this.users, ...response.data.data]
                        })
                }, 400);
            }
        },

        banOrUnban(user){
            axios.post(`${this.axiosString}/${this.typeOfRequestToButton}/${user.id}`)
                .then(response => {
                    if (this.typeOfRequestToButton == 'ban'){
                        if (response.data.message === 'user banned successfully'){
                            this.users.splice(this.users.indexOf(user), 1)
                            this.$emit('banOrUnban', 'ban')

                            if (this.users.length < this.limit){
                                this.getUsers()
                            }
                        }
                    } else if (this.typeOfRequestToButton == 'unban'){
                        if (response.data.message === 'user unbanned successfully'){
                            this.users.splice(this.users.indexOf(user), 1)
                            this.$emit('banOrUnban', 'unban')

                            if (this.users.length < this.limit){
                                this.getUsers()
                            }
                        }
                    }
                })
        },

    }
}
</script>
<style scoped>

    .usersWrap::-webkit-scrollbar {
        display: none;
    }

    .usersWrap{
        overflow: auto;
        height: 700px;
    }

    img{
        border-radius: 40%;
    }
    
</style>