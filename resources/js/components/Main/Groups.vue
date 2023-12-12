<template >
    <vueTitle :title="$t('main.Groups')"></vueTitle>
    <div class="w-75 mx-auto d-flex-column justify-content-center h-100">
        <div class="d-flex justify-content-center w-100">
            <div class="w-50 wrapBookmarks d-flex justify-content-between">
                <div>
                    <span class="bookmark" @click="this.bookmark = 'Content'"> {{$t('additional.Content')}} </span>
                    <span class="bookmark" @click="this.bookmark = 'Index'"> {{$t('additional.List')}} </span>
                </div>
                <div class="me-2">
                    <font-awesome-icon @click="changeToCreateAndUpdate" icon="fa-solid fa-circle-plus" class="pointer" size="lg"/>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center h-100 w-100">
            <div class="d-flex flex-column justify-content-start w-50 GroupWrap">
                <div class="frame">
                    <component :params="params" @bookmark="(e)=>ChangeBookmark(e)" :is="bookmark" :key="componentKey"/>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Content from '@/components/Additional/Groups/Content.vue';
import Moderate from '@/components/Additional/Groups/Moderate.vue';
import Index from '@/components/Additional/Groups/Index.vue';
import CreateAndUpdate from '@/components/Additional/Groups/CreateAndUpdate.vue';
import CreateAndUpdatePost from '@/components/Additional/Groups/CreateAndUpdatePost.vue'
import { nextTick } from 'vue';
export default {
    data(){
        return{
            bookmark: 'Content',
            params:null,
            componentKey: 0,
        }
    },

    inheritAttrs: false,

    components:{
        Content,
        Moderate,
        Index,
        CreateAndUpdate,
        CreateAndUpdatePost
    },

    methods:{
        ChangeBookmark(e){
            
            this.bookmark = e.bookmark
            if (e.params){
                this.params = e.params
            }
            nextTick(() => {
                this.params = null
            })
        },

        changeToCreateAndUpdate(){
            this.componentKey += 1;  
            this.bookmark = 'CreateAndUpdate'
        }

    }

}
</script>
<style scoped>

    .frame{
        background-color: #bc7e7e;
        border-radius: 7px;
        height: 100%;
        padding: 2px;
    }
    .wrapBookmarks{
        min-width: 750px
    }
    .GroupWrap {
        min-width: 750px;
        border-radius: 8px;
        min-height: 796px;
        height: 814px;
        background-color: rgba(221,152,146,1);
        padding: 12px;
    }

    .bookmark{
        background-color: rgba(221,152,146,1);
        border-radius: 5px 5px 0px 0px;
        display: inline-block;
        min-width: 120px;
        text-align: center;
        margin-left: 10px;
        cursor: pointer;
    }

    .bookmark:hover{
        box-shadow: rgb(201, 181, 181) 2px 0px 9px 2px;
    }
</style>