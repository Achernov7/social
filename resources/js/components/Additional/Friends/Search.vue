<template lang="">
    <div class="mb-5">
        {{$t('additional.Search_options')}}
    </div>
    <div>
        <SearchingTownUl v-model="searchedTownUI"></SearchingTownUl>
        <SelectAgeFromTo v-model:valueFrom="valueFrom" v-model:valueTo="valueTo"></SelectAgeFromTo>
        <ChooseGender v-model="gender"></ChooseGender>
        <div class="ms-2">
            <SelectFamilyStatus v-model="familystatus"></SelectFamilyStatus>
        </div>
    </div>
</template>
<script>

export default {

    props: ['searchedTown'],

    inheritAttrs: false,
    data() {
        return {
            valueFrom: 'From',
            valueTo: 'To',
            gender: 'any',
            familystatus: 'additional.Choose_status',
        }
    },


    computed: {
        searchedTownUI: {
            get() {
                return this.searchedTown
            },
            set(value) {
                this.$emit('update:searchedTown', value)
            }
        },
    },

    watch: {
        valueFrom: {
            handler(value) {
                this.$emit('update:valueFrom', value)
            },
            deep: true
        },
        valueTo: {
            handler(value) {
                this.$emit('update:valueTo', value)
            },
            deep: true
        },
        gender: {
            handler(value) {
                this.$emit('update:gender', value)
            },
            deep: true
        },
        familystatus: {
            handler(value) {
                this.$emit('update:familystatus', value)
            },
            deep: true
        }

    },

}
</script>
<style scoped>
    div{
        text-align: center;
        margin-bottom: 5px;
        box-sizing: border-box;
    }

</style>