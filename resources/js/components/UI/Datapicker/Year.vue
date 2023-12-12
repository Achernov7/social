<template lang="">
    
    <span class="listOfStatuses">
        <DivAsUl  ref="childOfSelect" :values="this.values" v-model:status="status"></DivAsUl>
    </span>
    <span  class='status' @click="$refs.childOfSelect.statuson()">
        <span>
            {{status}}
        </span>
    </span>

</template>
<script>
export default {
props: {
    modelValue: String,
    numberofYears: {
        type: Number
    },
    startingYear: {
        type: Number
    }
},

emits: ['change', 'update:modelValue'],

computed: {
    status: {
        get() {
            return this.modelValue
        },
        set(value) {
            this.$emit('update:modelValue', value.toString())
            this.$emit('change')
        }
    },
    values: {
        get() {
            const ValuesForPush = []
            for (let i = 0; i <= (this.numberofYears); i++) {
                ValuesForPush.push(this.startingYear-i)
            }
            return ValuesForPush
        }
    }
},


}
</script>
<style scoped>

.status{
    border-radius: 7px;
    border-color: rgb(12, 12, 12);
    border-style: solid;
    border-width: 2px;
    display: inline-block;
    width: 100px;
    background-color: rgb(240, 227, 227);
    cursor: pointer;
}

.listOfStatuses{
    margin-top: 25px;
        position: absolute;
        width: 100px;
}
</style>