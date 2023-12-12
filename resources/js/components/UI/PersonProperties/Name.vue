<template >
    <div>
        {{ $t('additional.Name') }}
    </div>
    <input class="NameInput" type="text" maxlength="25" v-model="name">
</template>
<script>
export default {
    props: {
        modelValue: String 
    },

    computed: {
        name: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue', value)
            }
        }
    }
}
</script>
<style scoped>
    .Name{
        position: relative;
    }
    .NameInput{
        background-color:rgb(240, 227, 227);
        border-radius: 7px;
        width: 96%;
        padding-left: 5px;
    }

    .NameInput:focus{
        outline: none;
        box-shadow: 0 0 10px #141050;
    }
</style>