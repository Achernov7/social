<template >
    <div ref="target" class="descriptionWindow roll">
        {{textOfDescriptionToShow}}
    </div>
</template>
<script>
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'
export default {

    props: {
        textOfDescription: {
            type: String,
            default: null
        }
    },

    watch: {
        textOfDescription: {
            immediate: true,
            handler() {
                this.textOfDescriptionToShow = this.textOfDescription
            }
        }
    },

    setup(props, context) {
        const target = ref(null)
        const textOfDescriptionToShow = ref(null)

        onClickOutside(target, () => {
            textOfDescriptionToShow.value = null
            context.emit('textOfDescriptionToShow', false)
        })

        return { target, textOfDescriptionToShow }
    }
}
</script>
<style scoped>

    .descriptionWindow{
        position: absolute;
        background-color: rgb(160, 102, 102);;
        border-radius: 8px;
        padding: 5px;
        width: 400px;
        height: 150px;
        box-sizing: border-box;
        opacity: 90%;
        word-wrap: break-word;
        overflow: auto;
    }
    
</style>