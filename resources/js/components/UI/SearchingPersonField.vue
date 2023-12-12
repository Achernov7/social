<template>
    <div class="d-flex w-100">
        <div ref="searchfield"  contenteditable ="true" @keydown="(e)=>countChars(e)" @keyup="this.searchingUser = this.$refs.searchfield.textContent" @paste.prevent="(e)=>setObjectFromPaste(e)" class="searchingBar ps-2 mb-2 me-2"></div>
        <font-awesome-icon v-if="this.searchingUser.length < 75 " class="mb-1" icon="fa-solid fa-magnifying-glass" size="xl"/>
    </div>
</template>
<script>
import CountCharsForSearch from '@/Mixins/CountCharsForSearch';
import SetObjectForPaste from '@/Mixins/SetObjectForPaste';

export default {
    props: {
        modelValue: String 
    },

    name: 'SearchingPersonField',

    mixins: [CountCharsForSearch, SetObjectForPaste],

    computed: {
        searchingUser: {
            get() {
                return this.modelValue
            },
            set(value) {
                if (value.length < 498) {
                    this.$emit('update:modelValue', value)
                } else {
                    this.$emit('update:modelValue', value.slice(0, 498))
                    console.log('Too long value for searching')
                }
                this.$emit('update:modelValue',value)
            }
        }
    }
}
</script>
<style scoped>

    .searchingBar{
        background-color:rgb(210, 162, 162);
        border-radius: 7px;
        width: 100%;
        display: inline-block;
        text-align: center;
        overflow: hidden;
        height: 23px;
    }

    .searchingBar:focus{
        outline: none;
        box-shadow: 0 0 10px #141050;
    }
    
</style>