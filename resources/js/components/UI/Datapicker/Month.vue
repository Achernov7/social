<template >

    <span class="listOfStatuses">
        <DivAsUl  ref="childOfSelect" :values="this.values" v-model:status="status"></DivAsUl>
    </span>
    <span  class='status' @click="$refs.childOfSelect.statuson()">
        <span>
            {{status}}
        </span>
    </span>
</template>


<script>
export default {
    props: {
        modelValue: String,
        months: {
            type: Array,
        }
    },

    emits: ['change', 'update:modelValue'],

    data() {
        return {
            monthsForCompare: 
            {
                "January": 1,
                "February": 2,
                "March": 3,
                "April": 4,
                "May": 5,
                "June": 6,
                "July": 7,
                "August": 8,
                "September": 9,
                "October": 10,
                "November": 11,
                "December": 12
            }
        }
    },

    computed: {
        status: {
            get() {
                for (let key in this.monthsForCompare) {
                    if (this.monthsForCompare[key] == this.modelValue) {
                        return this.$t(`additional.Months.${key}`)
                    }
                }
            },
            set(value) {
                for (let key in this.monthsForCompare) {
                    if (this.$t(`additional.Months.${key}`) == value) {
                        this.$emit('update:modelValue', this.monthsForCompare[key].toString())
                        this.$emit('change')
                    }
                }
                
            }
        },

        values() {
            const i18nMonths = []
            this.months.forEach((item) => {
                i18nMonths.push(this.$t(`additional.Months.${item}`))
            })

            return i18nMonths
        }
    },
}
</script>
<style scoped>

    .status{
        border-radius: 7px;
        border-color: rgb(12, 12, 12);
        border-style: solid;
        border-width: 2px;
        display: inline-block;

        width: 100px;
        background-color: rgb(240, 227, 227);
        cursor: pointer;
    }

    .listOfStatuses{
        margin-top: 25px;
        position: absolute;
        width: 100px;
    }
</style>