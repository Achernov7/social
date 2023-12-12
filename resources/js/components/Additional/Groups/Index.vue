<template>
    <div class="mt-2 ms-1" style="width: 99%;">
        <SearchingPersonField v-model="paramsForGroups.searchingObject"/>
    </div>
    <div class="groupsWrap roll" v-hiddenOverflow>
        <div v-for="link in paramsForGroups.ListOfObjects" :key="link">
            <div v-if="this.paramsForGroups.firstGroupOfObjects">
                <div @click.stop v-if ="link.id == this.paramsForGroups.firstGroupOfObjects" style="width: 100%;">
                    <span style="display: inline-block; text-align: center; width: 100%;">
                        {{ $t('additional.Yours_groups') }}
                    </span>
                </div>
            </div>
            <div v-if="this.paramsForGroups.secondGroupOfObjects">
                <div @click.stop v-if = "link.id == this.paramsForGroups.secondGroupOfObjects" style="width: 100%;">
                    <span style="display: inline-block; text-align: center; width: 100%;">
                        {{ $t('additional.Global_search') }}
                    </span>
                </div>
            </div>
            <div v-if="this.paramsForGroups.thirdGroupOfObjects">
                <div @click.stop v-if = "link.id == this.paramsForGroups.thirdGroupOfObjects" style="width: 100%;">
                    <span style="display: inline-block; text-align: center; width: 100%;">
                        {{ $t('additional.You_are_subscribed') }}
                    </span>
                </div>
            </div>
            <div class="link">
                <div style="display: table-row; cursor: pointer;" @click="changeBookmarkToGroup(link)">
                    <img class="ms-2" alt="Avatar" :src="link.mini_image.mini_url">
                    <span style="position: relative;">
                        <fullInnerText style="margin-top: -200px; margin-left: -5px;" v-if="idToShow == link.id && textOfDescriptionToShow != null" :textOfDescription = "textOfDescriptionToShow" @textOfDescriptionToShow ="textOfDescriptionToShow = null" />
                    </span>
                    <span @click="showFullName(link.id)" :ref="`name_${link.id}`" :class="{'nameOfGroup': true, 'nameWithTableCell':link.authenticated == 'creator', 'nameWithoutTableCell': link.authenticated == 'notConnectedwithYou'|| link.authenticated == 'subscribeTo',  'pointer': link.name.length > 20}">
                        {{ link.name }}
                    </span>
                    <span v-if="link.authenticated == 'creator'" @click.stop="moderateGroup(link)" class="icon">
                        <font-awesome-icon icon="fa-solid fa-edit" size="lg"/>
                    </span>
                    <span v-if="link.authenticated == 'creator'" @click.stop="changeBookmarkToEdit(link)" class="icon">
                        <font-awesome-icon icon="fa-solid fa-wrench" size="lg"/>
                    </span>
                    <span v-if="link.authenticated == 'creator'" @click.stop="deleteGroup(link)" class="icon">
                        <font-awesome-icon icon="fa-solid fa-trash" size="lg"/>
                    </span>
                    <span style="float: right" v-if="link.authenticated == 'notConnectedwithYou'|| link.authenticated == 'subscribeTo'">
                        <span class="me-2">{{ $t('additional.Subscribers')}}:</span>
                        <span style="display: inline-block; width: 150px; line-height:60px;">{{ link.subscribers }}</span>
                    </span>
                </div>
        </div>
            <div v-if="paramsForGroups.ListOfObjects.indexOf(link) == paramsForGroups.observerLine" v-intersection="LoadMoreGroups" class="observer" ref="observer"></div>
        </div>
    </div>
</template>
<script>
import fullInnerText from '@/components/UI/fullInnerText.vue';
import SearchingPersonField from '@/components/UI/SearchingPersonField.vue';
import { nextTick } from 'vue';
import GetObjectsWithPaginate from '@/Mixins/GetObjectsWithPaginate';
export default {
    mounted() {
        setTimeout(() => {
            this.axiosGet(this.paramsForGroups)
        });
    },

    mixins: [GetObjectsWithPaginate],

    watch: {
        searchingGroup(){
            this.getObjects(this.paramsForGroups)
        }
    },

    computed: {
        searchingGroup() {
            return this.paramsForGroups.searchingObject
        }
    },

    inheritAttrs: false,

    emits: ['bookmark'],

    data() {
        return {
            textOfDescriptionToShow: null,
            idToShow: null,
            paramsForGroups: {
                page: 0,
                limit: 15,
                ListOfObjects: [],
                observerLine: 0,
                lastSearch: '',
                searchingObject: '',
                gettingObject: null,
                axiosString: '/api/groups/',
                firstGroupOfObjects: null,
                secondGroupOfObjects: null,
                thirdGroupOfObjects:null,
                elementsAlreadyTook: null,
            },
            stopLoading: false
        }
    },

    components: {
        fullInnerText,
        SearchingPersonField
    },
    
    methods: {
        LoadMoreGroups(){
            if (!this.stopLoading){
                this.getObjects(this.paramsForGroups)
            }
        },
        changeBookmarkToEdit(link){
            this.$emit('bookmark', {'bookmark': 'CreateAndUpdate', 'params': link})
        },
        deleteGroup(elem){
            axios.delete(`/api/groups/delete/${elem.id}`)
                .then(res => {
                    this.paramsForGroups.ListOfObjects.splice(this.paramsForGroups.ListOfObjects.indexOf(elem), 1)
                })
                .catch(err => {
                    console.log(err)
                })
        },
        moderateGroup(link){
            this.$emit('bookmark', {'bookmark': 'Moderate', 'params': link})
        },
        showFullName(groupId){
            nextTick(() => {
                if (this.$refs[`name_${groupId}`][0].innerText.length > 20){
                    this.textOfDescriptionToShow = this.$refs[`name_${groupId}`][0].innerText
                    this.idToShow = groupId
                }
            })
        },
        changeBookmarkToGroup(link){
            this.$emit('bookmark', {'bookmark': 'Content', 'params': link})
        }
    },
}
</script>
<style scoped>

    .groupsWrap{
        overflow: hidden;
        height: 95%;
    }

    .link{
        display: table;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 5px;
        width: 99%;
        background-color: rgb(210, 162, 162);
        border-radius: 8px;
        margin: 4px;
        padding: 4px 15px 4px 4px;
    }

    .nameWithTableCell{
        display: table-cell;
    }

    .nameWithoutTableCell{
        margin-left: 160px;
    }

    .nameOfGroup{
        width: 40%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .icon{
        display: table-cell;
        cursor: pointer;
        width: 12%;
        background-color: rgb(205, 142, 142);
        text-align: center;
        border-radius: 25%;
        border: 1px solid rgb(121, 103, 103);
    }

    img {
        border-radius: 40%;
    }
    
</style>