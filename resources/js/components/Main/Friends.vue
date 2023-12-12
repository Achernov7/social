<template>
    <div class="w-75 mx-auto d-flex justify-content-center wrapCommon h-100">
        <vueTitle :title="$t('titles.Friends')"></vueTitle>
            <div class="d-flex flex-column justify-content-top h-100 w-75 me-4">
                <span class="d-flex w-100">
                    <SearchingPersonField v-model="searchingUser"></SearchingPersonField>
                </span>
                <div class="w-100 h-100 OrangeBackForWindow">
                    <SearchingWindow 
                        :searchingUser="searchingUser" 
                        :searchfield="searchfield"
                        :searchedTown="searchedTown"
                        :valueFrom="valueFrom"
                        :valueTo="valueTo"
                        :gender="gender"
                        :familystatus="familystatus"
                    />
                </div>
            </div>
            <div class="w-25 h-100 OrangeBack me-4">
                <div class="rightsideTabs">
                    <a href="#" @click=" this.searchfield = 'Requests'" class = "rightsideTab">
                        {{ $t('additional.Friend requests') }}
                    </a>
                    <a href="#" @click=" this.searchfield = 'Search'" class="rightsideTab">
                        {{ $t('additional.Search') }} 
                    </a>
                    <a href="#" @click=" this.searchfield = 'Follow'" class="rightsideTab">
                        {{ $t('additional.You_are_following') }} 
                    </a>
                </div>
                <div class="frame">
                    <KeepAlive>
                        <component :is="searchfield" :searchingUser="searchingUser" 
                            v-model:searchedTown="searchedTown"
                            v-model:valueFrom="valueFrom"
                            v-model:valueTo="valueTo"
                            v-model:gender="gender"
                            v-model:familystatus="familystatus"
                        />
                    </KeepAlive>
                </div>
            </div>
    </div>
</template>

<script>
import SearchingWindow from '@/components/Additional/Friends/SearchingWindow.vue';
import SearchingPersonField from '@/components/UI/SearchingPersonField.vue';
import Search from '@/components/Additional/Friends/Search.vue';
import Requests from '@/components/Additional/Friends/Requests.vue';
import Follow from '@/components/Additional/Friends/Follow.vue';
export default {

    data() {
        return {
            searchfield: 'Search',
            searchingUser: '',
            searchedTown: '',
            valueFrom: '',
            valueTo: '',
            gender: '',
            familystatus: '',
        }
    },

    components: {
        Search,
        Requests,
        Follow,
        SearchingWindow,
        SearchingPersonField
    }
}
</script>
<style scoped>

    .wrapCommon{
        height: 100%;
    }

    .OrangeBackForWindow{
        background-color: rgb(210, 162, 162);
        border-radius: 7px;
        height: 100%;
        max-height: 835px;
    }
    .OrangeBack{
        background-color: rgb(210, 162, 162);
        border-radius: 7px;
        height: 100%;
        max-height: 900px;
    }

    .rightsideTab{
        display: block;
        background-color:rgb(194, 138, 138);
        border-radius: 7px;
        margin: 7px 4px;
        text-align:center;
        text-decoration: none;
        color:#000;
    }
    .frame{
        background-color: #bc7e7e;
        border-radius: 7px;
        height: 85%;
        margin-left: 4px;
        margin-right: 4px;
        margin-top: 20px;
        margin-bottom: 20px;
        padding-top: 1px;
    }

    .rightsideTabs{
        display: block;
        height: 10%;
        min-height: 79px;
    }

</style>