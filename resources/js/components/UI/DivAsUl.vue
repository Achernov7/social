<template lang="">
    <div class="outer">
        <div ref="target" class="inner roll pointer">
            <div tabindex="0" class="elem" 
            v-for="index in this.statusesLength"
                :style = "index > restrictions  ? {'display': 'none'} : {}"
                @mouseenter="setFocusMouse(index)"
                @keyup="(event) => switchElem(event, index)" 
                @keydown="(event)=>keydownPreventUpDown(event)" 
                @click="choose(statuses[index-1])"
                @keyup.enter="choose(statuses[index-1])"
                :ref="index" :key="index" 
            >
                {{statuses[index-1]}}
            </div>
        </div>
    </div>

</template>
<script>
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'
export default {
    name: "DivAsUl",

    props: {
        values: {
            type: Array,
            default: () => []
        },
        SentOnWithClickOutside: {
            type: Boolean
        },
        restrictions: {
            type: Number,
        }
    },

    computed:{
        statusesLength() {
            return this.statuses.length
        },
    },

    methods: {
        statuson() {
            this.statuses = this.values
            this.$emit('onsend', false)
            
        },

        choose(status) {
            this.$emit('update:status', status)
            this.statuses= []
            this.$emit('onsend', true)
        },

        setFocusMouse(index) {
            this.$refs[index][0].focus()
        },

        keydownPreventUpDown(event){
            if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
                event.preventDefault();
            }
        },

        switchElem(event, index) {
            if (event.key === 'ArrowUp') {
                if (index !== 1) {
                    this.$refs[index-1][0].focus()
                }
            } else if (event.key === 'ArrowDown') {
                if (index !== this.statusesLength) {
                    this.$refs[index+1][0].focus()
                }
            }
        }
        
    },

    setup(props, context) {
        const target = ref(null)
        const statuses = ref([])


        onClickOutside(target, () => {
            statuses.value = []
            
            if (!props.SentOnWithClickOutside) {
                context.emit('onsend', true)
            }
        })

        return { target, statuses }
    }
}



</script>
<style scoped>

    .elem:focus{
        background-color: rgb(215, 198, 198);
        outline: none;
    }

    .inner{
        max-height: 180px;
        width: 98%;
        overflow: auto;
        border-radius: 8px;
        background-color: rgb(197, 149, 130);
    }

    .outer{
        margin-left: 4px;
        border-radius: 8px;
        width: 95%;
        background-color: rgb(197, 149, 130);
        position: absolute;
    }


</style>