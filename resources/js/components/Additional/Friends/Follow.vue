<template >
    <div class="youSubscribed">
        <UserBar :searchingUser="searchingUser" :users="SubscribedToUsersWithoutAccept" :height="'610px'" :width="'93%'" @addUsersAfterFetch="(e)=>this.addSubscribers(e)" :AxiosString="'/api/friends/getSubscribedToOfAuthWithPagination'"></UserBar>
    </div>

</template>
<script>
import PushToTheUserPage from '@/Mixins/PushToTheUserPage'
export default {
    mounted() {
        this.getSubscribersOfUser()
    },

    props: ['searchingUser'],

    mixins: [PushToTheUserPage],

    data() {
        return {
            SubscribedToUsersWithoutAccept: null,
        }
    },

    methods: {
        getSubscribersOfUser() {
            axios.post('/api/friends/getSubscribedToOfAuthWithPagination', { page: 0, limit: 10})
                .then(response => {
                    this.SubscribedToUsersWithoutAccept = response.data
                }
            )
        },

        addSubscribers(users) {
            this.SubscribedToUsersWithoutAccept = [...this.SubscribedToUsersWithoutAccept, ...users]
        }
    }
}
</script>
<style scoped>
    .youSubscribed{
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
        width: 100%;
    }

</style>