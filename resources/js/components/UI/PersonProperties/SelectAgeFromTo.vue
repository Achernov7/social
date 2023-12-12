<template >
    <div>{{$t('additional.Age')}}</div>
        <div>
            <div class="d-flex justify-content-between">
                <span>
                    <select v-model="valueFromchild">
                        <option selected value="From">{{$t('additional.From')}}</option>
                        <option v-for="index in this.RangeOfAgeForFrom" :key="index" >
                            {{ index }}
                        </option>
                    </select>
                </span>
                <span>
                    <select v-model="valueTochild">
                        <option selected value="To">{{$t('additional.To')}}</option>
                        <option v-for="index in this.RangeOfAgeForTo" :key="index" >
                            {{ index }}
                        </option>
                    </select>
                </span>
            </div>
        </div>
</template>
<script>
export default {

    name: 'SelectAgeFromTo',

    props: ['valueFrom', 'valueTo'],

    data() {
        return {
            maxlengthofAgeSearch: 80,
            startofLengthSearch: 14,
        }
    },

    computed: {
        RangeOfAgeForTo() {
            let arr = [];
            if (this.valueFromchild == 'From') {
                for (var i = this.startofLengthSearch; i <= this.maxlengthofAgeSearch; i++)
                arr.push(i);
            } else {
                for (var i = this.valueFromchild; i <= this.maxlengthofAgeSearch; i++)
                arr.push(i);
            }

            return arr;
        },

        RangeOfAgeForFrom() {
            let arr = [];
            if (this.valueTochild == 'To') {
                for (var i = this.startofLengthSearch; i <= this.maxlengthofAgeSearch; i++)
                arr.push(i);
            } else {
                for (var i = this.startofLengthSearch; i <= this.valueTochild; i++)
                arr.push(i);
            }
            return arr;
        },

        valueFromchild: {
            get() {
                return this.valueFrom
            },
            set(value) {
                this.$emit('update:valueFrom', value)
            }
        },

        valueTochild: {
            get() {
                return this.valueTo
            },
            set(value) {
                this.$emit('update:valueTo', value)
            }
        }

    }

}
</script>
<style scoped>
    span{
        width: 43%;
        margin: auto;
    }
    select{
        text-align: center;
        border-radius: 5px;
        width: 100%;
        background-color:rgb(240, 227, 227);
        border-color: black;
        border-width: 2px;
    }

    select option{
        background-color:  rgb(197, 149, 130);
    }
</style>