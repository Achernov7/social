<template >
    <div class="dialog pointer" v-if="show" @mouseup ="hideDialog" @mouseleave="hideDialog" @mousemove="setProgress">
    </div>
</template>
<script>
export default {
    name: 'DialogForCalculating',

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
            default:'rgba(0, 0, 0, 0)'
        },
    },

    methods: {
        hideDialog() {
            this.$emit('update:show', false)
            this.$emit('finish')
        },

        setProgress(e) {
            this.$emit('setProgress', e)
        }
    }
}
</script>

<style scoped>
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
</style>