<template>
    <Dialog v-if="!SomeonesElsesPage" v-model:show="dialogVisible">
        <PopupDeleteOrChange :PersonPage="PersonPage" @change="(ChangeFor)=>changeFor(ChangeFor)"/>
    </Dialog>
    <div class="w-75 mx-auto d-flex justify-content-center wrap">
        <vueTitle v-if="userData" :title="this.userData.name "></vueTitle>
            <div class="h-100 w-75 me-4">
                <div class="d-flex justify-content-between w-100 h-100 OrangeBack">
                    <font-awesome-icon v-if="!SomeonesElsesPage" class="cogs" @click="show" icon="fa-cogs" size="xl"/>
                    <div class="photoAndFriends">
                        <div style="position: absolute; top: -28px; margin-left: 25px; text-align: center; width: 75%;" v-if="userData && userData.last_activity">
                            <span v-if="userData.last_activity != 'Online'">
                                <span>
                                    {{ $t('additional.Was_online') }}:
                                </span>
                                <span>
                                    {{ userData.last_activity }}
                                </span>
                            </span>
                            <span v-else>
                                {{ $t('additional.Online') }}
                            </span>
                        </div>
                        <div class="photo" v-if="userData">
                            <img alt="Avatar" v-if="userData.preview_image" :src="userData.preview_image.preview_url" class="mb-3">
                        </div>
                        <div>      
                            <FriendsSideBar v-if="userData && !userData.isAuthUser" :SomeonesElsesPage="SomeonesElsesPage" :someoneElsesSubscribers="userData.someoneElsesSubscribers" :friends="userData.friends" :subscribers="userData.subscribers" :someoneElsesFriends="userData.someoneElsesFriends" @changeStatus="(id)=>changeStatus(id)" :AuthUser="userData.AuthUser" @addFriendsToFriendList="(friends)=>addFriendsToFriendList(friends)"></FriendsSideBar>
                            <FriendsSideBar v-if="userData && userData.isAuthUser" :usersId="userData.id" :SomeonesElsesPage="SomeonesElsesPage" :someoneElsesFriends="userData.friends" @addFriendsToFriendList="(friends)=>addFriendsToFriendList(friends)"></FriendsSideBar>
                        </div>
                    </div>
                    <div class="content w-100 h-100 d-flex flex-column mt-4">
                        <component @change="(ChangeFor)=>changeFor(ChangeFor)" v-model="userData" :is="PersonPage" @ShouldLoadUserdata="getUser"></component>
                    </div>
                </div>
            </div>
    </div>
</template>

<script>
import FriendsSideBar from '../UI/PersonProperties/FriendsSideBar.vue';
import Index from '@/components/Additional/Profile/Index.vue'
import PopupDeleteOrChange from '@/components/Additional/Profile/PopupDeleteOrChange.vue'
import Edit from '@/components/Additional/Profile/Edit.vue';
export default {

    inheritAttrs:false,

    name: "Profile",

    components: {
        Edit,
        PopupDeleteOrChange,
        Index,
        FriendsSideBar
    },

    mounted() {
        var regExp = /profile\/\d+/;
        if (regExp.test(this.$route.fullPath)) {
            this.SomeonesElsesPage = true
            this.getSomeonesElsesPage()
        } else {
            this.SomeonesElsesPage = false
            this.getUser()
        }
    },

    data() {
        return {
            PersonPage: 'Index',
            dialogVisible: false,
            userData: null,
            SomeonesElsesPage:null,
            isUsersLoading: false
        }
    },

    methods: {
        show() {
            this.dialogVisible = true;
        },
        changeFor(PersonPage) {
            this.PersonPage = PersonPage;
            this.dialogVisible = false;
        },
        getUser(){
            axios.get('/api/users')
            .then(response => {
                this.userData = response.data
            })
        },
        getSomeonesElsesPage(){
            axios.get(`/api/users/${this.$route.params.id}`)
            .then(response => {
                if (response.data.isAuthUser){
                    this.SomeonesElsesPage = false
                }
                this.userData = response.data
            })
            .catch(err=>{
                if (err.response.status === 404){
                    this.$router.push( this.$Tr.i18nroute({ name: '404'}));
                }
            })
        },

        addFriendsToFriendList(friends){
            if (this.userData.AuthUser){
                this.userData.someoneElsesFriends = [...this.userData.someoneElsesFriends, ...friends]
            } else {
                this.userData.friends = [...this.userData.friends, ...friends]
            }
        },
        
        changeStatus(obj){
            if (obj.action == 'acceptToBeFriends'){
                if (obj.AuthInfo){
                    this.userData.someoneElsesFriends.push(obj.AuthInfo)
                }
                this.userData.subscribers.splice(this.userData.subscribers.findIndex(x => x.id == obj.id), 1)
                this.userData.friends.push({
                    id: obj.id
                })
            } else if (obj.action == 'subscribeToTheUser'){
                this.userData.someoneElsesSubscribers.push({
                    id: this.userData.AuthUser
                })
            } else if (obj.action == 'CancelYourSubscription'){
                this.userData.someoneElsesSubscribers.splice(this.userData.someoneElsesSubscribers.findIndex(x => x.id == this.userData.AuthUser), 1)
                
            } else if (obj.action == 'deleteFromFriendListWithAddToSubscribers'){
                const AuthIndex = this.userData.someoneElsesFriends.findIndex(x => x.id == this.userData.AuthUser)
                AuthIndex != -1 ? this.userData.someoneElsesFriends.splice(AuthIndex, 1) : null
                this.userData.friends.splice(this.userData.friends.findIndex(x => x.id == obj.id), 1)
                this.userData.subscribers.push({
                    id: obj.id
                })
            } else if (obj.action == 'deleteFromFriendList'){
                const AuthIndex = this.userData.someoneElsesFriends.findIndex(x => x.id == this.userData.AuthUser)
                AuthIndex != -1 ? this.userData.someoneElsesFriends.splice(AuthIndex, 1) : null
                this.userData.friends.splice(this.userData.friends.findIndex(x => x.id == obj.id), 1)
            }
        }
    }


}
</script>

<style scoped>

    .wrap{
        height: 100%;
    }

    img {
        border-radius: 30%;
    }

    .cogs{
        position: absolute;
        right: 5px;
        top:5px;
        cursor: pointer;
    }

    .photoAndFriends{
        position: relative;
        width: 30%;
        margin-top: 8%;
        margin-left: 30px;
    }

    .photo{
        min-width: 250px;
    }

    .content{
        display: inline-block;
        text-align: center;
    }
    .OrangeBack{
        background-color: rgb(210, 162, 162);
        border-radius: 7px;
        height: 100%;
        position: relative;
    }
</style>