<template>
    <div style="display: table; width: 100%; table-layout: fixed;" class="mb-3">
        <div v-if="paramsOfGroup && !paramsOfGroup.preview_url.preview_url.includes(`${url}/storage/images/default_image`)" class="avatarWrap mb-4 mt-2">
            <img class="ms-4 mt-2" alt="Avatar" :src="paramsOfGroup.preview_url.preview_url">
        </div>
        <div style="display: table-cell; vertical-align: middle;">    
            <div v-if="paramsOfGroup" class="nameOfGroup mb-1 mt-1 ms-3">
                {{ paramsOfGroup.name }}
            </div>
            <div class="AddPostWrap mb-4" @click = "this.$emit('bookmark', {'bookmark': 'CreateAndUpdatePost', 'params': {'group':this.paramsOfGroup}})">
                <span class="me-3">{{ $t('additional.Add_post') }}</span>
                <font-awesome-icon icon="fa-solid fa-clipboard"/>
            </div>
            <div class="me-2" style="width: 94%; float: right;">
                <SearchingPersonField v-if="paramsForPosts" v-model="paramsForPosts.searchingObject"/>
            </div>
            <div style="text-align: center;">
                <div>
                    <span class="me-3">
                        {{ $t('additional.Subscribers') }}
                    </span>
                    <span @click="showSubscribers" v-if="paramsOfGroup" class="subscribers pointer">
                        {{ paramsOfGroup.subscribers }}
                    </span>
                </div>
                <div>
                    <span class="me-3">
                        {{ $t('additional.Banned_users') }}
                    </span>
                    <span @click="showBanUsers" v-if="paramsOfGroup" class="subscribers pointer">
                        {{ paramsOfGroup.bansCount }}
                    </span>
                </div>
                <Dialog :CommonBackgroundColor="'rgba(0, 0, 0, 0.4)'" :backgroundOfContent = "'rgb(205, 142, 142)'" :minHeightContent="'720px'" :minWidthContent="'400px'" v-model:show="showUsers">
                    <UsersPopUpWithSearch @banOrUnban="(e)=>(recountBansOrSubcribers(e))" v-if="paramsOfGroup" :axiosString="axiosStringForPopup" :limit="15" />
                </Dialog>
            </div>
        </div>
    </div>
    <div v-if="paramsOfGroup" :class="{'wrapPost roll': true, 'heightWrapPostWithNoPicture': paramsOfGroup.preview_url.preview_url.includes(`${url}/storage/images/default_image`), 'heightWrapPostWithPicture': !paramsOfGroup.preview_url.preview_url.includes(`${url}/storage/images/default_image`)}" v-hiddenOverflow>
        <div v-for="post in paramsForPosts.ListOfObjects" @click="showOrTakeOffComments(post.id)" :key="post.id">
            <div class="post">
                <div style="display: table-cell;">
                    <span class="imgWrap">
                        <img v-if="post.image.mini_image" class="ms-2" alt="Avatar" :src="post.image.mini_image.mini_url">
                    </span>
                </div>
                <div style="display: table-cell; vertical-align: center; width: 80%;">
                    <div :class="{'relativePos': paramsForPosts.ListOfObjects.indexOf(post) != 0 && paramsForPosts.ListOfObjects.indexOf(post) != 1 && paramsForPosts.ListOfObjects.indexOf(post) != 2}">
                        <fullInnerText style="margin-top: -150px; margin-left: -90px;" v-if="idToShow == post.id && textOfDescriptionToShow != null" :textOfDescription = "textOfDescriptionToShow" @textOfDescriptionToShow ="textOfDescriptionToShow = null" />
                    </div>
                    <span @click.stop="showDescription(post.id)" :ref="`description_${post.id}`"  :class="{'description ms-4': true, 'pointer': post.description.length > 20}">
                        {{ post.description }}
                    </span>
                    <span @click.stop="deletePost(post)" class="icon">
                        <font-awesome-icon icon="fa-solid fa-trash" size="lg"/>
                    </span>
                    <span  @click.stop="this.$emit('bookmark', {'bookmark': 'CreateAndUpdatePost', 'params': {'post': post, 'group': paramsOfGroup}})" class="icon">
                        <font-awesome-icon icon="fa-solid fa-pen" size="lg"/>
                    </span>
                </div>
            </div>
            <div style="width: 100%;"  @click.stop>
                <div class="commentWrap roll">
                    <div v-if="idOfPostToShow == post.id" v-for="comment in commentsToShow" :key="comment">
                        <div class="d-flex">
                            <div class="userComment ms-1 mt-2" style="width: 14%;">
                                <div>
                                    <img class="ms-2" alt="Avatar" :src="comment.user.mini_image.mini_url">
                                </div>
                                <div style="text-align: center; text-overflow: ellipsis; overflow: hidden; width: 70px;">
                                    {{ comment.user.name }}
                                </div>
                            </div>
                            <div class="comment ps-2 pe-2 pt-1 me-2 roll">
                                <div style="width: 90%;"> 
                                    {{ comment.comment }} 
                                </div>
                                <div class="d-flex flex-column justify-content-center" style="width: 10%; text-align: center;">
                                    <div @click.stop="deleteComment(comment)" class="pointer">
                                        <font-awesome-icon icon="fa-solid fa-trash" size="lg"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="commentsToShow.indexOf(comment) == commentsToShow.length - 2 && commentsToShow.length%limitComments == 0" v-intersection="{'function': takeComments, 'object': post.id}" class="observer" ref="observer"></div> 
                    </div>
                </div>
            </div>
            <div v-if="paramsForPosts.ListOfObjects.indexOf(post) == paramsForPosts.observerLine" v-intersection="LoadMorePosts" class="observer" ref="observer"></div> 
        </div>
    </div>
</template>
<script>
import axios from 'axios'
import SearchingPersonField from '@/components/UI/SearchingPersonField.vue';
import fullInnerText from '@/components/UI/fullInnerText.vue';
import { nextTick } from 'vue';
import GetObjectsWithPaginate from '@/Mixins/GetObjectsWithPaginate';
import UsersPopUpWithSearch from '@/components/Additional/Groups/UsersPopUpWithSearch.vue'
export default {

    watch: {
        searchingPost(){
            this.getObjects(this.paramsForPosts)
        }
    },

    mixins: [GetObjectsWithPaginate],

    props: [
        'params'
    ],

    emits: [
        'bookmark'
    ],

    mounted() {
        if (this.params != null){
            this.paramsOfGroup = this.params
            this.paramsForPosts.axiosString = `/api/groups/${this.paramsOfGroup.id}/posts`
            setTimeout(() => {
                this.axiosGet(this.paramsForPosts)
            });
        }
    },

    components: {
        SearchingPersonField,
        fullInnerText,
        UsersPopUpWithSearch
    },

    data() {
        return {
            paramsOfGroup: null,
            idToShow: null,
            stopLoading: false,
            textOfDescriptionToShow: null,
            paramsForPosts: {
                page: 0,
                limit: 15,
                ListOfObjects: [],
                observerLine: 0,
                lastSearch: '',
                searchingObject: '',
                gettingObject: null,
                axiosString: '',
            },
            limitComments: 7,
            showComments: false,
            commentsToShow:[],
            idOfPostToShow: null,
            stopLoadingComments:false,
            showUsers: false,
            axiosStringForPopup:''
        }
    },

    computed: {
        url(){
            return import.meta.env.VITE_URL
        },
        searchingPost(){
            return this.paramsForPosts.searchingObject
        }
    },

    methods: {
        LoadMorePosts(){
            if (!this.stopLoading){
                this.getObjects(this.paramsForPosts)
            }
        },

        showDescription(postId){
            nextTick(() => {
                if (this.$refs[`description_${postId}`][0].innerText.length > 20){
                    this.textOfDescriptionToShow = this.$refs[`description_${postId}`][0].innerText
                    this.idToShow = postId
                }
            })
        },
        deletePost(elem){
            axios.delete(`/api/groups/${this.paramsOfGroup.id}/posts/delete/${elem.id}`)
                .then(res => {
                    if (res.data.message === 'post deleted successfully'){
                        this.paramsForPosts.ListOfObjects.splice(this.paramsForPosts.ListOfObjects.indexOf(elem), 1)
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },

        showOrTakeOffComments(id){
            if (this.idOfPostToShow == id){
                this.idOfPostToShow = null
            } else {
                this.stopLoadingComments = false
                this.idOfPostToShow = id
                this.commentsToShow = []
                this.takeComments(id)
            }
        },

        takeComments(id){
            if (this.commentsToShow.length > 0){
                var lastcreated = {lastCreatedAt: this.commentsToShow[this.commentsToShow.length-1].created_at}
            } else {
                var lastcreated = {}
            }
            axios.post(`/api/groups/posts/comments/${id}/${this.limitComments}`, lastcreated)
                    .then(res => {
                        
                        if (res.data.data.length < this.limitComments){
                            this.stopLoadingComments = true
                        }

                        this.commentsToShow = [...this.commentsToShow, ...res.data.data]
                    })
                    .catch(err => {
                        console.log(err)
                    })
        },

        deleteComment(comment){
            axios.delete(`/api/groups/posts/comments/delete/${comment.id}/`)
                .then(res => {
                    if (res.data.message === 'comment deleted successfully'){
                        this.commentsToShow.splice(this.commentsToShow.indexOf(comment), 1)
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },

        showSubscribers(){
            this.axiosStringForPopup = `/api/groups/${this.paramsOfGroup.id}/users/getSubscribers`
            this.showUsers = true
        },

        showBanUsers(){
            this.axiosStringForPopup = `/api/groups/${this.paramsOfGroup.id}/users/getBanUsers`
            this.showUsers = true
        },

        recountBansOrSubcribers(e){
            if (e == 'ban'){
                this.paramsOfGroup.bansCount = this.paramsOfGroup.bansCount + 1
                this.paramsOfGroup.subscribers = this.paramsOfGroup.subscribers - 1
            } else {
                this.paramsOfGroup.bansCount = this.paramsOfGroup.bansCount - 1
                this.paramsOfGroup.subscribers = this.paramsOfGroup.subscribers + 1
            }
        }

    },
}
</script>
<style scoped>

    .subscribers{
        display: inline-block;
        border-radius: 8px;
        min-width: 20px;
    }

    .subscribers:hover{
        transition: 0.3s;
        background-color: rgb(210, 162, 162);
    }
    .commentWrap{
        border-radius: 8px;
        width: 87%;
        background-color:rgb(205, 142, 142);
        margin: 0px auto;
        max-height: 300px;
        overflow: auto;
    }

    .commentWrap::-webkit-scrollbar{
        display: none;
    }

    .comment{
        border-radius: 8px;
        width: 98%;
        margin: 7px auto;
        min-height: 50px;
        max-height: 90px;
        overflow: auto;
        background-color:  rgb(210, 162, 162);
        display: flex;
    }

    .relativePos{
        position: relative
    }

    .wrapPost{
        overflow: hidden;
    }

    .heightWrapPostWithPicture{
        height: 69%
    }

    .heightWrapPostWithNoPicture{
        height: 81%
    }


    .avatarWrap{
        display: table-cell;
        width: 40%;
    }

    .description{
        margin-left: 20px;
        display: inline-block;
        text-align: center;
        width: 50%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: bottom
    }

    .icon{
        cursor: pointer;
        margin-right: 25px;
        float: right;
    }

    .nameOfGroup{
        font-size: 1.2rem;
        width: 94%;
        text-align: center;
        box-sizing: border-box;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    img{
        border-radius: 40%;
    }
    .imgWrap{
        width: 70px;
        display: inline-block;
    }
    .post{
        display: table;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 5px;
        width: 99%;
        background-color: rgb(210, 162, 162);
        border-radius: 8px;
        margin: 4px;
        padding: 2px 15px 2px 2px;
    }

    .AddPostWrap{
        padding: 5px;
        width: 94%;
        background-color: rgb(210, 162, 162);
        border-radius: 8px;
        margin: 9px 15px;
        text-align: center;
        font-size: 1.1rem;
    }

    .AddPostWrap:hover{
        cursor: pointer;
        box-shadow: 2px -4px 2px 0px rgb(255, 255, 255);
        scale: 1.01;
    }

</style>