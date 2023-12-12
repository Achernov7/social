<template >
    <div class="userLikeWrap roll" v-hiddenOverflow>
        <div class="mb-2 pointer userToSHow" v-for="user in usersToSHow" :key="user.id" @click.prevent="PushToTheUserPage( user.whoLiked.id)">
            <div class="d-flex">
                <div>
                    <img v-if="user.whoLiked.mini_image" :src="user.whoLiked.mini_image.mini_url" alt="">
                </div>
                <div class="d-flex flex-column justify-content-center ms-4">
                    {{ user.whoLiked.name }}
                </div>
            </div>
            <div v-if="usersToSHow.indexOf(user) == usersToSHow.length-2" v-intersection="getUsers" class="observer" ref="observer"></div> 
        </div>
    </div>
</template>
<script>
import PushToTheUserPage from '@/Mixins/PushToTheUserPage'

export default {
    props: {
        whoLiked: Array,
        idOfPost: Number,
        numberOfLikes: Number,
        axiosString: String
    },

    computed: {
        usersToSHow: {
            get() {
                return this.whoLiked
            },
            set(value) {
                this.$emit('updateUsers', {'idOfPost': this.idOfPost, 'value': value})
            }
        },
    },

    mixins: [PushToTheUserPage],

    data() {
        return {
            stopFetch:false
        }
    },

    methods: {
        getUsers() {
            if (!this.stopFetch && (this.whoLiked.length % this.numberOfLikes == 0)) {
                axios.get(`${this.axiosString}/posts/likes/${this.idOfPost}/${this.numberOfLikes}`, {
                    params: {
                        lastCreatedAt: this.whoLiked[this.whoLiked.length-1].created_at,
                    }
                })
                    .then(response => {
                        if (response.data.data.length < this.numberOfLikes) {
                            this.stopFetch = true
                        }
                        this.usersToSHow = [...this.usersToSHow, ...response.data.data]
                    })
            }
        }
    },




}
</script>
<style scoped>

    .userToSHow{
        padding: 5px;
        border-radius: 8px;
        background-color:  rgb(210, 162, 162);
    }

    img{
        border-radius: 40%;
    }

    .userLikeWrap{
        height: 550px;
        overflow: hidden;
    }
    
</style>