<template>
    <div class="usersList roll" ref="userList" :style="{ overflow: overflow}" @mouseenter="this.overflow = 'auto'" @mouseleave="this.overflow = 'hidden'">
        <div @click="PushToTheUserPage(user.id)" v-for="user in users" :key="user.id" class="usersWrap pointer">
            <img alt="Avatar" :src="user.mini_image.mini_url">
            <span class='name'>
                {{ user.name }}
            </span>
            <div v-if="users.indexOf(user) == this.obersverLine" v-intersection="LoadMoreFriends" class="observer" ref="observer"></div> 
        </div>
        <Loading v-if="isUsersLoading" :height="'2.5em'" :width="'2.5em'" :textSize="'1em'" :modelSize="'1.1em'" ></Loading>
    </div>
</template>
<script>
import PushToTheUserPage from '@/Mixins/PushToTheUserPage'
export default {

    props: ['users', 'usersId', 'height', 'width', 'AxiosString', 'withSurname', 'searchingUser', 'withAlreadyTaken'],

    name: "UserBar",

    data() {
        return {
            overflow: 'hidden',
            isUsersLoading: false,
            page: 1,
            limit: null,
            obersverLine: 0,
            stopFetch: false,
            handleLoad: null
        }
    },

    mixins: [PushToTheUserPage],

    methods: {

        LoadMoreFriends() {
            if (!this.stopFetch){

                this.isUsersLoading = true
                if (!this.$route.params.id) {
                    this.fetchUsers(this.usersId)
                } else {
                    this.fetchUsers(this.$route.params.id)
                }
                
            }
        },

        fetchUsers(UserId) {
            if (UserId){
                var axiosLine = `${this.AxiosString}${UserId}`
            } else {
                var axiosLine = `${this.AxiosString}`
            }
            if (this.limit == null){
                this.limit = this.users.length
            }
            const params = {
                page:this.page,
                limit:this.limit,
            }
            if (this.withAlreadyTaken){
                params.alreadyTaken = this.users.map(user => user.id)
            }
            axios.post(axiosLine, params)
                        .then(response => {
                            this.page ++
                            this.obersverLine = this.obersverLine + this.limit
                            this.isUsersLoading = false
                            this.$emit('addUsersAfterFetch', response.data)
                            if (response.data.length !== this.limit) {
                                this.stopFetch = true
                            }
                        })
                        .catch(err => {
                            console.log(err)
                        })
            
        }
    }
}
</script>
<style scoped>

    img {
        border-radius: 30%;
    }

    .usersWrap{
        background-color:rgb(194, 138, 138);
        margin: 4px;
        border-radius: 7px;
        padding: 3px;
    }

    .name{
        display: inline-block;
        margin-left: 20%;
        width: 70px;
        white-space: nowrap;
        overflow: clip;
        text-overflow: ellipsis;
    }

    .surname{
        margin-left: 30px
    }

    .usersList{
        background-color: #bc7e7e;
        width: v-bind(width);
        height: v-bind(height);
        border-radius: 8px;
        margin-left: 10px;
        padding-top: 2px;
    }
    
</style>