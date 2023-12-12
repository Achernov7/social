<template >
    <div class="Requests">
        <UserBar :searchingUser="searchingUser" :users="RequestsToBeFriend" :height="'610px'" :width="'93%'" @addUsersAfterFetch="(e)=>this.addSubscribers(e)" :AxiosString="'/api/friends/getSubscribersOfAuthWithPagination'"></UserBar>
    </div>

</template>
<script>

export default {

    props: ['searchingUser'],

    mounted() {
        this.getSubscribersOfUser()
    },

    data() {
        return {
            RequestsToBeFriend: null,
        }
    },

    methods: {
        getSubscribersOfUser() {
            axios.post('/api/friends/getSubscribersOfAuthWithPagination', { page: 0, limit: 10})
                .then(response => {
                    this.RequestsToBeFriend = response.data
                }
            )
        },

        addSubscribers(users) {
            this.RequestsToBeFriend = [...this.RequestsToBeFriend, ...users]
        }
    }
}
</script>
<style scoped>
    .Requests{
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
        width: 100%;
    }

</style>