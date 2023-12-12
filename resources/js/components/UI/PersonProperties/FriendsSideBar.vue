<template >
    <div class="wrap">
        <div class="mb-4 buttonWrap"  v-if="SomeonesElsesPage">
            <ButtonStandart v-if="SomeonesElsesPage" class="mb-2 messageButton" :value="'Write_message'" @click.stop="this.$router.push( this.$Tr.i18nroute({ name: 'user.messageTo', params: { id: this.$route.params.id }}))"/>  
            <ButtonStandart v-if="!AlreadySubscribeOnYou && !YouAreFriends && !YouAreSubscribed" :defineFunction="AddAsFriend" :value="'Add_as_friend'" class="messageButton"/>
            <ButtonStandart v-if="AlreadySubscribeOnYou" :defineFunction="AcceptToBeFriends" :value="'Accept_friend_request'" class="messageButton"/>
            <ButtonStandart v-if="YouAreFriends" :defineFunction="DeleteFromFriendList" :value="'You_are_friends'" class="messageButton"/>
            <ButtonStandart v-if="YouAreSubscribed" :defineFunction="CancelYourSubscription" :value="'You_are_subscribed'" class="messageButton"/>
        </div>
    </div>
    <div class="textWrap">
        {{ $t('additional.Friends') }}
    </div>
    <UserBar :withAlreadyTaken="true" :users="someoneElsesFriends" :height="'358px'" :width="'200px'" :usersId="usersId" @addUsersAfterFetch="(e)=>this.$emit('addFriendsToFriendList', e)" :AxiosString="'/api/friends/getFriendsForPaginationOfAnyUser/'"></UserBar>
</template>
<script>

export default {

    emits: ['addFriendsToFriendList', 'changeStatus'],

    props: ['SomeonesElsesPage', 'someoneElsesSubscribers', 'subscribers', 'friends', 'someoneElsesFriends', 'AuthUser', 'usersId'],

    methods: {
        AddAsFriend(){
            axios.post(`/api/friends/addAsFriend/${this.$route.params.id}`)
                .then(response => {
                    if (response.data.message == 'You subscribed to this user'){
                        this.$emit('changeStatus', {
                            'action':'subscribeToTheUser',
                            'id': this.AuthUser
                        })
                    }
                })
                .catch(err=>{
                    console.log(err)
                })
        },

        AcceptToBeFriends(){
            const alreadyDisplayed = this.someoneElsesFriends.map(x => x.id)
            axios.get(`/api/friends/acceptToBeFriends/${this.$route.params.id}/${alreadyDisplayed.length}`)
                .then(response => {
                    if (response.data.message == 'You are now friends with this user'){
                        if (response.data.AuthInfo){
                            this.$emit('changeStatus', {          
                                'action':'acceptToBeFriends', 
                                'id':this.$route.params.id,
                                'AuthInfo':response.data.AuthInfo
                            })
                        } else {
                            this.$emit('changeStatus', {          
                                'action':'acceptToBeFriends', 
                                'id':this.$route.params.id
                            })
                        }
                    }
                })
                .catch(err=>{
                    console.log(err)
                })
        },

        DeleteFromFriendList(){
            axios.delete(`/api/friends/deleteFromFriendList/${this.$route.params.id}`)
                .then(response => {
                    if (response.data.message == 'The user is now your subscriber'){
                        this.$emit('changeStatus', {
                            'action':'deleteFromFriendListWithAddToSubscribers',  
                            'id':this.$route.params.id
                        })
                    }
                    if (response.data.message == 'You are no longer friends with this user'){
                        this.$emit('changeStatus', {
                            'action':'deleteFromFriendList',
                            'id':this.$route.params.id
                        })
                    }
                })
                .catch(err=>{
                    console.log(err)
                })
        },

        CancelYourSubscription(){
            axios.delete(`/api/friends/CancelYourSubscription/${this.$route.params.id}`)
                .then(response => {
                    
                    if (response.data.message == 'You sucsessfully unsubscribed from this user'){
                        this.$emit('changeStatus', {
                            'action':'CancelYourSubscription',  
                            'id':this.$route.params.id
                        })
                    }
                })
                .catch(err=>{
                    console.log(err)
                })
        },

    },

    computed: {
        AlreadySubscribeOnYou(){
            var forReturn = false
            this.subscribers.forEach(element => {
                if (element.id == this.$route.params.id){
                    forReturn =  true
                }
            });
            return forReturn
        },
        YouAreFriends(){
            var forReturn = false
            this.friends.forEach(element => {
                if (element.id == this.$route.params.id){
                    forReturn =  true
                }
            })
            return forReturn
        },
        YouAreSubscribed(){
            var forReturn = false
            this.someoneElsesSubscribers.forEach(element => {
                if (element.id == this.AuthUser){
                    forReturn =  true
                }
            })
            return forReturn
        }
    }


}
</script>
<style scoped>

    img {
        border-radius: 30%;
    }

    .textWrap{
        margin-left: 32%;
    }

    .wrap{
        padding-right: 34px;
    }


    .messageButton{
        margin: 0px auto;
    }

    .buttonWrap{
        display: flex;
        justify-content: center;
        flex-direction: column;
        margin-left: 12%;
        width: 80%;
    }

</style>