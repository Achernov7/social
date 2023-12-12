<template lang="">
    <div>
        {{ $t('additional.Town') }}
    </div>
    <input maxlength="50" @keyup="search()" class="TownInput" v-model="searchedTown" type="text">
    <div class="town mb-2">
        <DivAsUl ref="childOfSelect" @onsend="(OnSend)=>changeTown(OnSend)" :SentOnWithClickOutside="true" :restrictions="14"  v-model:values="this.TownGuess"  v-model:status="status"></DivAsUl>
    </div>
</template>
<script>

export default {

    name: 'SearchingTownUl',

    data() {
        return {
            On: true,
            status:'',
            TownGuess: [],
            areas: [],
            MissedInAreaTowns:['Москва', 'Санкт-Петербург']
        }
    },

    props: {
        modelValue: String 
    },
    
    computed: {
        searchedTown: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue', value)
            }
        },

        values(){
            return this.TownGuess
        }

    },

    mounted() {
        axios.get('https://api.hh.ru/areas/113',
        {
            withCredentials: false,
            transformRequest: [function (data, headers) {
                delete headers['X-Socket-Id'];
                return data;
            }]
        })
        .then(response => {
            this.areas = response.data
        })
        .catch(err=>{
            console.log(err);
        })
    },

    methods: {

        changeTown(OnSend){
            if (OnSend){
                this.searchedTown = this.status
            }
        },

        search() {
            this.TownGuess = []
            if (this.areas){
                this.areas.areas.forEach(element => {
                    for(const name of element.areas){
                        this.PushToUl(name.name);
                    }
                });
    
                this.MissedInAreaTowns.forEach(element => {
                    this.PushToUl(element);
                })
                setTimeout(() => {
                    this.$refs.childOfSelect.statuson() 
                });
            }
        },

        PushToUl(elem){
            var foundBrackets = elem.match(/\s\(.*\)/g);
            if (elem.toString().replace(foundBrackets, '')
                .toLowerCase().includes(this.searchedTown.toString().toLowerCase()))
                    {
                        if (!this.TownGuess.includes(elem)){
                            this.TownGuess.push(elem)
                        }
                    }
        },        
    }
}
</script>
<style scoped>
    .town{
        position: relative;
    }
    .TownInput{
        margin-left: 5px;
        background-color:rgb(240, 227, 227);
        border-radius: 7px;
        width: 96%;
        padding-left: 5px;
    }

    .TownInput:focus{
        outline: none;
        box-shadow: 0 0 10px #141050;
    }

</style>