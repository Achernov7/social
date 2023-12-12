<template>
    <Dialog :CommonBackgroundColor="'rgba(0, 0, 0, 0.4)'" :minHeightContent="'580px'" v-model:show="dialogLikeVisible">
        <PopUpForUserLikes :whoLiked="usersWhoLikedPost" :idOfPost="idOfPostWhoLikes" :numberOfLikes="limitLikes" :axiosString="axiosString" @updateUsers="(e)=>addUsersWhoLike(e)"/>
    </Dialog>
    <div @click="changeShowAdditionalInfo" v-if="this.additionalInfo" class="ms-1 mt-1 mb-1 d-flex justify-content-center pointer" style="background-color:rgb(210, 162, 162); width: 99%; border-radius: 8px; padding-top: 2px; padding-bottom: 2px;">
        <div v-if="showAdditionalInfo == false" class="triangleDown"/>
        <div v-if="showAdditionalInfo == true" class="triangleUp"/>
    </div>
    <transition name="showAdditionalInfo">
        <div v-if="this.additionalInfo && showAdditionalInfo" class="ms-1 mb-3 additionalInfo">
            <div class="d-flex h-100">
                <div class="d-flex flex-column justify-content-center">
                    <ButtonStandart v-if="this.additionalInfo.authenticated == 'subscribeTo'" :value="'You_are_following'" class="ms-4 mb-2" @click="unSubscribe(additionalInfo.id)"></ButtonStandart>
                    <ButtonStandart v-if="this.additionalInfo.authenticated == 'notConnectedwithYou'" :value="'Subscribe'" class="ms-4 mb-2" @click="SubscribeToTheGroup(additionalInfo.id)"></ButtonStandart>
                    <img class="ms-1" style="width: 210px;" alt="Avatar" :src="this.additionalInfo.preview_url.preview_url">
                </div>
                <div class="ms-3 name">
                    <div style="text-align: center;">
                        <h3>{{ this.additionalInfo.name }}</h3>
                    </div>
                    <div class="mb-2 roll" style="overflow: auto; max-height: 75px">
                        <span>{{ this.additionalInfo.description }}</span>
                    </div>
                    <div v-if="this.additionalInfo.links">
                        <div class="mb-1">
                            {{ this.$t('additional.Links') }}
                        </div>
                        <div v-for="link in this.additionalInfo.links" :key="link.id">
                            <a :href="link"> 
                                <div class="link">
                                    {{ link }}
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <div class="postWrap noroll" id="nn" ref="postWrap" v-hiddenOverflow>
        <div class="post mt-2 ms-1" v-for="post in posts" :key="post.id">        
            <div style="height: 100%;">
                <div style="display: table; width: 100%; height: 100%; table-layout: fixed;">
                    <div style="display: table-cell; width: 30%;">
                        <div :class="{'wrapTriangleLeftRight': true, 'lessOpacity': !groupIsShowing || numberOfGroupisShowing != post.id}" @mouseenter="changeGroupShowing(post)" @mouseleave="leaveFromShowing()">
                            <div class="d-flex flex-column justify-content-center" style="height: 100%; padding-left: 3px;">
                                <div v-if="groupIsShowing == false || numberOfGroupisShowing != post.id" class="triangleRight"/>
                                <div v-if="groupIsShowing == true && numberOfGroupisShowing == post.id" class="triangleLeft"/>
                                <transition name="showGroup">
                                    <div v-if="groupIsShowing && numberOfGroupisShowing == post.id" style="position: relative; width: 64px;">
                                        <div class="groupInfo">
                                            <div class="d-flex flex-column justify-content-center" style="height: 100%;">
                                                <div class="mb-4" style="writing-mode: vertical-rl; transform: rotate(180deg); line-height: 50px;">
                                                    {{ post.group.name }}
                                                </div>
                                                <div>
                                                    <img :src="post.group.mini_image.mini_url" alt="Avatar">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center" style="height: 560px;">
                            <div v-if="post.image.preview_image">
                                <img class="ms-2 postPicture" alt="Avatar" :src="post.image.preview_image.preview_url">
                            </div>
                            <div class="mt-4" style="text-align: center;">
                                <font-awesome-icon @click.stop="sendDislike(post.id)" v-if="this.likedsPostsByUser.includes(post.id)" icon="fas fa-thumbs-up" class="pointer" size="xl"/>
                                <font-awesome-icon @click.stop="sendLike(post.id)" v-if="!this.likedsPostsByUser.includes(post.id)" icon="far fa-thumbs-up" class="pointer" size="xl"/>
                                <span class="ms-3 pointer" @click="userLikesShow(post)">
                                    {{ post.likes_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div style="display: table-cell; vertical-align: top">
                        <div class="ms-4 mt-4 postDescription roll">
                            {{ post.description }}
                        </div>
                        <div class="commentWrap mt-3 ms-4" v-hiddenOverflow :ref="`CommentWrap_${post.id}`">
                            <div :class="{'comment roll me-1 pt-1':true, 'mb-3':!(post.comments.indexOf(comment) == post.comments.length-1)}" v-for="comment in post.comments" :key="comment.id">
                                <div class="d-flex" style="min-height: 80px;">
                                    <div class="d-flex flex-column justify-content-center">
                                        <div @click.prevent="PushToTheUserPage(comment.user.id)" class="pointer">
                                            <img :src="comment.user.mini_image.mini_url" alt="Avatar">
                                        </div>
                                        <div @click.prevent="PushToTheUserPage(comment.user.id)" class="pointer" style="text-align: center; text-overflow: ellipsis; overflow: hidden; width: 70px;">
                                            {{ comment.user.name }}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center ms-3" style="overflow: hidden;">
                                        <div style=" text-overflow: ellipsis; overflow: hidden;">
                                            {{ comment.comment }}
                                        </div>
                                    </div>
                                </div>
                                <div v-if="post.comments.indexOf(comment) == post.comments.length-2 && post.comments.length > limitComments-1" v-intersection="{'function':getComments, 'object': post}" class="observer" ref="observer"></div> 
                            </div>
                        </div>
                        <div class="mt-2 ms-4" style="width: 94%;">
                            <ChatMessage :defineAxiosString="`/api/groups/posts/comments/create/${post.id}`" @messageWasSent="(e)=>{CommentWasSent(e)}"></ChatMessage>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="posts.indexOf(post) == this.obersverLine" v-intersection="getPosts" class="observer" ref="observer"></div> 
        </div>
    </div>
</template>
<script>
import PopUpForUserLikes from '@/components/UI/PopUpForUserLikes.vue';
import PushToTheUserPage from '@/Mixins/PushToTheUserPage';
import ChatMessage from '@/components/UI/ChatMessage.vue';
export default {

    mounted() {
        this.getPosts()
        setTimeout(() => {
            if (this.additionalInfoOfPost){
                this.additionalInfo = this.additionalInfoOfPost
                this.heightOfPostWrap = '760px'
            }
        });
        if (this.heightOfComponent){
            this.heightOfPostWrap = this.heightOfComponent
        }
    },

    mixins: [PushToTheUserPage],

    components: {
        PopUpForUserLikes,
        ChatMessage
    },

    props: {
        axiosString: {
            type: String
        },
        idOfType: {
            type: Number
        },
        additionalInfoOfPost: {
            type: Object
        },
        heightOfComponent: {
            type: String,
        }
    },

    data() {
        return {
            posts: [],
            page:0,
            limitPosts: 10,
            limitLikes: 10,
            limitComments: 10,
            likedsPostsByUser:[],
            daysNeedToLoad:30,
            obersverLine: 0,
            stopLoadingPosts:false,
            dialogLikeVisible:false,
            usersWhoLikedPost:[],
            idOfPostWhoLikes: null,
            arrayOfPostsWithStopLoadingComments:[],
            showAdditionalInfo: false,
            heightOfPostWrap: '780px',
            transitionOfPostWrap: '1s',
            youSubScribeTo:[],
            youSubScribeToEnd:null,
            groupIsShowing:false,
            numberOfGroupisShowing:null,
            additionalInfo:null
        }
    },

    methods: {
        getPosts(){
            setTimeout(() => {                
                if (!this.stopLoadingPosts){
                    var limits = {'limitPosts': this.limitPosts, 'limitLikes': this.limitLikes, 'limitComments': this.limitComments}
                    var ids = [];
                    this.posts.forEach(element => {
                        ids.push(element.id)
                    });
    
                    if (typeof this.idOfType != 'undefined'){
                        var fullAxiosRequest = `${this.axiosString}/${this.idOfType}/posts/withCommentsAndLikes`
                    } else {
                        var fullAxiosRequest = `${this.axiosString}/posts`;
                    }
                    
                    axios.post(fullAxiosRequest, {page: this.page, limit: limits, daysNeedToLoad: this.daysNeedToLoad, idsOfPosts: ids, youSubScribeTo: this.youSubScribeTo, youSubScribeToEnd: this.youSubScribeToEnd})
                        .then( response => {

                                if (response.data.likedPosts){
                                    this.likedsPostsByUser = response.data.likedPosts
                                }
                                if (response.data.youSubScribeTo){
                                    this.youSubScribeTo = response.data.youSubScribeTo
                                }
                                if (response.data.youSubScribeToEnd){
                                    this.youSubScribeToEnd = response.data.youSubScribeToEnd
                                }
                                this.posts = [...this.posts, ...response.data.data]
                                this.page += 1
                                if (response.data.data.length < this.limitPosts){
                                    this.stopLoadingPosts = true
                                }
                                this.obersverLine = this.posts.length-2
                            }
                        )
                }
            });
        },

        sendLike(id){
            axios.post(`${this.axiosString}/posts/like/${id}`)
                .then(response => {
                    
                    if (response.data.message === 'post liked successfully'){
                        this.likedsPostsByUser.push(id)
                        this.posts.every(post => {
                            if (post.id == id){
                                post.likes_count += 1
                                return false
                            }
                            return true
                        })
                    }
                })
        },

        sendDislike(id){
            axios.post(`${this.axiosString}/posts/dislike/${id}`)
                .then(response => {
                    
                    if (response.data.message === 'post disliked successfully'){
                        this.likedsPostsByUser.splice(this.likedsPostsByUser.indexOf(id), 1)
                        this.posts.every(post => {
                            
                            if (post.id == id){
                                post.likes_count -= 1
                                return false
                            }
                            return true
                        })
                    }
                })
        },

        getComments(post){
            if (post.comments.length % this.limitComments == 0){
                if (!this.arrayOfPostsWithStopLoadingComments.includes(post.id)){
                    axios.post(`${this.axiosString}/posts/comments/${post.id}/${this.limitComments}`, {lastCreatedAt: post.comments[post.comments.length-1].created_at})
                        .then(response => {
                            if (response.data.data.length < this.limitComments){
                                this.arrayOfPostsWithStopLoadingComments.push(post.id)
                            }
                            this.posts.every(postGeneral => {
                                if (postGeneral.id == post.id){
                                    postGeneral.comments = [...postGeneral.comments, ...response.data.data]
                                    return false
                                }
                                return true
                            })
                        })
                }
            }
        },

        userLikesShow(post){
            this.usersWhoLikedPost = post.users_who_liked
            this.idOfPostWhoLikes = post.id
            this.dialogLikeVisible = true
        },

        addUsersWhoLike(e){
            this.posts.every(post => {
                if (post.id == e.idOfPost){
                    post.users_who_liked = e.value
                    this.usersWhoLikedPost = e.value
                    return false
                }
                return true
            })
        },

        CommentWasSent(e){
            this.posts.every(post => {
                if (post.id == e.data.postId){
                    post.comments.unshift(e.data.data)
                    return false
                }
                return true
            })
            this.$nextTick(() => {
                this.$refs[`CommentWrap_${e.data.postId}`][0].scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            })
        },

        changeShowAdditionalInfo(){
            if (this.showAdditionalInfo){
                this.showAdditionalInfo = !this.showAdditionalInfo
                this.transitionOfPostWrap = '0.3s'
                this.heightOfPostWrap = '400px'
                setTimeout(() => {
                    this.transitionOfPostWrap = '0.9s'
                    this.heightOfPostWrap = '760px'
                }, 310);
            } else {
                this.showAdditionalInfo = !this.showAdditionalInfo
                this.transitionOfPostWrap = '0s'
                this.heightOfPostWrap = '400px'
                setTimeout(() => {
                    this.transitionOfPostWrap = '0.2s'
                    this.heightOfPostWrap = '465px'
                }, 200)
            }
        },

        changeGroupShowing(post){
            this.groupIsShowing = true
            this.numberOfGroupisShowing = post.id
            
        },

        leaveFromShowing(){
            this.groupIsShowing = false
            this.numberOfGroupisShowing = null
        },

        SubscribeToTheGroup(id){
            axios.post(`${this.axiosString}/subscribe/${id}`)
                .then(response => {
                    if (response.data.message == 'successfully subscribed'){
                        this.additionalInfo.authenticated = 'subscribeTo'
                    }
                })
        },

        unSubscribe(id){
            axios.post(`${this.axiosString}/unsubscribe/${id}`)
                .then(response => {
                    if (response.data.message == 'successfully unsubscribed'){
                        this.additionalInfo.authenticated = 'notConnectedwithYou'
                    }
                })
        }
    },
}
</script>
<style scoped>

    .lessOpacity{
        opacity: 0.3;
    }

    .groupInfo{
        position: absolute;
        top: -260px;
        left: 12px;
        background-color: rgba(221,152,146,1);
        height: 500px;
        width: 100%;
        min-width: 0px;
        border-radius: 10px;
    }
    .showGroup-enter-active {
        transition: 1s linear;
        max-width: 30px;
    }
    
    .showGroup-enter-to{
        opacity: 1;
        max-width: 200px;
    }

    .showGroup-enter-from{
        opacity: 0;
        max-width: 0px;
    }


    .showAdditionalInfo-enter-active, .showAdditionalInfo-leave-active {
        transition: 0.5s ease;
        max-height: 900px;
    }

    .showAdditionalInfo-leave-to
    {
        opacity: 0;
        max-height: 0px;
    }

    .showAdditionalInfo-enter-to{
        opacity: 1;
        max-height: 900px;
    }

    .showAdditionalInfo-enter-from{
        opacity: 1;
        max-height: 0px;
    }

    .wrapTriangleLeftRight{
        background-color: rgba(221,152,146,1);
        float: left;
        height: 562px;
        border-radius: 8px;
        width: 15px;
    }


    .triangleUp{
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid black;
    }

    .triangleDown{
        width: 0; 
        height: 0; 
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 10px solid black;
    }

    .triangleRight{
        width: 0; 
        height: 0; 
        border-bottom: 10px solid transparent;
        border-left:10px solid black;
        border-top: 10px solid transparent;
    }

    .triangleLeft{
        width: 0; 
        height: 0; 
        border-bottom: 10px solid transparent;
        border-right:10px solid black;
        border-top: 10px solid transparent;
    }

    .link, name{
        width: 450px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    a{
        color: black;
        box-sizing: border-box;
    }

    a:link {
        text-decoration: none;
    }

    a:visited {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }

    .additionalInfo{
        width: 99%;
        background-color:  rgb(210, 162, 162);
        padding: 8px;
        border-radius: 8px;
        height: 280px;
    }

    .comment{
        background-color: #b88e8e;
        border-radius: 8px;
        padding-left: 4px;
        max-height: 100px;
        overflow-y: auto;
    }

    .postDescription{
        max-height: 75px;
        overflow-y: auto;
    }

    .postWrap{
        height: v-bind('heightOfPostWrap');
        overflow: hidden;
        transition: v-bind('transitionOfPostWrap');
    }

    .noroll::-webkit-scrollbar {
        display: none;
    }

    .commentWrap{
        height: 340px;
        overflow: hidden;
    }

    .commentWrap::-webkit-scrollbar {
        display: none;
    }

    img{
        border-radius: 40%;
    }

    .postPicture{
        width: 190px;
    }

    .post{
        width: 99%;
        background-color:  rgb(210, 162, 162);
        padding: 8px;
        border-radius: 8px;
        height: 580px;
    }

</style>