<template lang="">
        <span class= "abs">
            <div class="listOfStatuses">
                <div class="spacer"></div>
                <DivAsUl  @onsend="(OnSend)=>this.On=OnSend"  ref="childOfSelect" :values="this.values" v-model:status="status"></DivAsUl>
            </div>
        </span>
        <span class='status' @click="$refs.childOfSelect.statuson()">
            <span>
                {{status}}
            </span>
        </span>

</template>
<script>
export default {

    props: {
        modelValue: String,
        daysInMonth: {
            type: Number,
        }
    },

    computed: {
        status: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue', value.toString())
            }
        },
        values: {
            get() {
                return Array.from({length: this.daysInMonth}, (_, i) => i + 1)
            }
        }
    },


}
</script>
<style scoped>
    .abs{
        position: relative;
    }

    .status{
        border-radius: 7px;
        border-color: rgb(12, 12, 12);
        border-style: solid;
        border-width: 2px;
        display: inline-block;

        width: 40px;
        background-color: rgb(240, 227, 227);
        cursor: pointer;
    }

    .listOfStatuses{
        display: inline-block;
        margin-top: 25px;
        position: absolute;
        width: 40px;
        left: -2px;

    }

</style>