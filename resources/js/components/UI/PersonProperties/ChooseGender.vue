<template>
    <div class="w mt-2 ms-2">
        <div>{{$t('additional.Gender')}}</div>
        <div>
            <input class="mes" :selected="picked === 'Female'" type="radio" id="female" name="gender" value="Female" v-model="picked">
            <label for="female">{{$t('additional.Female')}}</label>
        </div>
        <div>
            <input class="mes" :selected="picked === 'Male'" type="radio" id="male" name="gender" value="Male" v-model="picked">
            <label for="male">{{$t('additional.Male')}}</label>
        </div>
        <div v-if="!onlyMaleFemale">
            <input class="mes" type="radio" id="any"  name="gender" value="any" v-model="picked" checked>
            <label for="any">{{$t('additional.Any')}}</label>
        </div>
    </div>
</template>
<script>
export default {

    name: 'ChooseGender',

    props: {
        modelValue: String,
        onlyMaleFemale: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        picked: {
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
    .w{
       text-align: left;
    }

    .mes{
        margin-right: 5px;
    }

    label{
        cursor: pointer;
        padding-bottom: px;
    }

    input[type='radio'] {
        accent-color: #232323;
    }   

</style>