<template>
    <div>
        <PostsWithComments :axiosString="axiosString" :idOfType="idOfType" :additionalInfoOfPost="additionalInfo"></PostsWithComments>
    </div>
</template>
<script>

import PostsWithComments from '@/components/UI/PostsWithComments.vue';
export default {

    mounted() {
        if (this.params){
            this.idOfType = this.params.id
            this.additionalInfo = this.params
        }
    },

    props: ['params'],

    components: {
        PostsWithComments
    },

    data(){
        return{
            axiosString: '/api/groups',
            idOfType: undefined,
            additionalInfo: undefined
        }
    }
}
</script>
<style scoped>
    
</style>