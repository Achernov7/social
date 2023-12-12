<template>
    <div class="d-flex justify-content-center h-100">
        <div class="wrapTape">
            <PostsWithComments :heightOfComponent="'830px'" :axiosString="axiosString"></PostsWithComments>
        </div>
    </div>
</template>

<script>
import PostsWithComments from '@/components/UI/PostsWithComments.vue';
export default {
    
    components:{
        PostsWithComments
    },

    data(){
        return{
            axiosString: '/api/groups',
        }
    }
}
</script>
<style scoped>

    .wrapTape{
        max-width: 950px;
        width: 55%;
        padding: 6px 4px;
        background-color: rgba(221,152,146,1);
        border-radius: 8px;
        height: 100%;
        min-width: 680px;
    }
    
</style>
