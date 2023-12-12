<template >
    <div class="mt-1 mb-1">{{ $t('additional.About_yourself') }}</div>
    <div class="divForTextArea">
        <textarea maxlength="400" class="textArea" v-model="AboutYourself"></textarea>
    </div>
</template>
<script>
export default {
    props: {
        modelValue: String 
    },

    computed: {
        AboutYourself: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue',value)
            }
        }
    }
}
</script>
<style scoped>

    .divForTextArea{
        height: 120px;
    }

    .textArea{
        width: 96%;
        border-radius: 8px;
        display: block;
        background-color: rgb(240, 227, 227);
        padding: 5px 8px;
        height: 100%;
        border-color: black;
        border-width: 2px;
        resize: none;
    }

    .textArea:focus{
        outline: none;
        box-shadow: 0 0 10px #141050;
    }
</style>