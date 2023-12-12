<template >
    <div>
        {{$t("additional.Family_status")}}
    </div>
    <div v-if="this.On" class='status' @click="$refs.childOfSelect.statuson()">
        <span>
            {{status}}
        </span>
    </div>
    <div class="listOfStatuses">
        <DivAsUl  ref="childOfSelect"  @onsend="(OnSend)=>this.On=OnSend" :values="this.values" v-model:status="status"></DivAsUl>
    </div>

</template>
<script>

export default {
    name: 'SelectFamilyStatus',

    data() {
        return {
            On: true,
            familystatues: ["additional.Choose_status","additional.Not_married","additional.Married","additional.Seeing","additional.Engaged","additional.In_Love","additional.In_civil_marriage","additional.It_s_comlicated","additional.In_active_search"],
        }
    },

    props: {
        modelValue: String 
    },

    computed:{
        status:{            
            get() {
                return this.$t(this.modelValue)
            },
            set(value) {
                this.$emit('update:modelValue', this.transformValueToKeyOfI18n(value))
            }
        },
        
        values(){
            return this.familystatues.map((item)=>{
                return this.$t(item)
            })
        }
    },

    methods: {
        transformValueToKeyOfI18n(value){
            
            var i18nstatus=''

            this.familystatues.forEach((item)=>{
                if (this.$t(item)==value){
                    i18nstatus = item
                }
            })
            return i18nstatus
        }
    }


}
</script>
<style scoped>

    .listOfStatuses{
        position: relative;
    }

    .status{
        border-radius: 7px;
        border-color: rgb(12, 12, 12);
        border-style: solid;
        border-width: 2px;

        width: 96%;
        background-color: rgb(240, 227, 227);
        cursor: pointer;

    }


</style>