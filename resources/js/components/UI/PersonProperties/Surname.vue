<template >
    <div>
        {{ $t('additional.Surname') }}
    </div>
    <input class="surnameInput" type="text" maxlength="25" v-model="surname">
</template>
<script>
export default {
    props: {
        modelValue: String 
    },

    computed: {
        surname: {
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
    .surnameInput{
        background-color:rgb(240, 227, 227);
        border-radius: 7px;
        width: 96%;
        padding-left: 5px;
    }

    .surnameInput:focus{
        outline: none;
        box-shadow: 0 0 10px #141050;
    }
</style>