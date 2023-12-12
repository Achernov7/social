<template >
    <div class="dialog" v-if="show" @click ="hideDialog">
        <transition name="dialogContent">
            <div v-if="showContent" @click.stop class="dialog__content">
                <slot></slot>
            </div>
        </transition>
        </div>
</template>
<script>
export default {
    name: 'Dialog',

    watch: {
        show(){
            this.$nextTick(() => {
                this.showContent = this.show
            })
        }
    },

    data() {
        return {
            showContent: false
        }
    },
    
    props: {
        show: {
            type: Boolean,
            default: false
        },
        CommonBackgroundColor:{
            default:'rgba(0, 0, 0, 0.1)'
        },
        backgroundOfContent:{
            default: 'linear-gradient(16deg, rgba(237,198,156,1) 0%, rgba(221,152,146,1) 47%, rgb(174, 100, 205) 100%)'
        },
        minWidthContent:{
            default:'250px'
        },
        minHeightContent:{
            default:'150px'
        },
        padding:{
            default:'20px'
        },
        margin:{
            default:'auto'
        }
    },

    methods: {
        hideDialog() {
            this.$emit('update:show', false)
        }
    }
}
</script>

<style scoped>

    .dialogContent-enter-active {
        transition: all 0.5s;
    }
    .dialogContent-enter-to{
        scale: 1;
    }
    
    .dialogContent-enter-from{
        scale: 0;
    }
    .dialog{
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: v-bind('CommonBackgroundColor');
        position: fixed;
        display: flex;
        z-index: 1;
    }

    .dialog__content{
        margin: v-bind('margin');
        background: v-bind(' backgroundOfContent');
        border-radius: 12px;
        min-width: v-bind('minWidthContent');
        min-height: v-bind('minHeightContent');
        
        padding: v-bind('padding');
    }
</style>