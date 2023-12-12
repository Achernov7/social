<template>
    <div class="mb-2">
        {{ $t('additional.Birthday_date') }}
    </div>
    <div class="datepicker">
        <span>
            <Day v-model="day" :daysInMonth = "daysInMonth"></Day>
        </span>
        <span>
            <Month v-model="month" :months = "months" @change='editValueOfDaysChangingMonth(this.month, this.year, this.day)'></Month>
        </span>
        <span>
            <Year v-model="year" :numberofYears="numberofYears" :startingYear="startingYear" @change='editValueOfDaysChangingYear(this.month, this.year, this.day)'></Year>
        </span>
    </div>
</template>


<script>

import Day from './Day.vue';
import Month from './Month.vue';
import Year from './Year.vue';
import { ref } from 'vue'

export default {

    props: ['modelValue'],

    watch: {
        day(){
            this.updateBirthdayDate(this.day, this.month, this.year)
        },
        month(){
            this.updateBirthdayDate(this.day, this.month, this.year)
        },
        year(){
            this.updateBirthdayDate(this.day, this.month, this.year)
        }
    },

    components: {
        Day,
        Month,
        Year,
    },

    methods: {
        updateBirthdayDate(day, month, year){
            this.$emit('update:modelValue', this.addZeroToDayIfNeed(day)+'-'+this.addZeroToMonthIfNeed(month)+'-'+year)
        }
    },

    setup(props, context) {

        const date = new Date();

        if (props.modelValue) {
            var day = ref(props.modelValue.split('-')[0]);
            var month = ref(props.modelValue.split('-')[1]);
            var year = ref(props.modelValue.split('-')[2]);
        } else {
            var day = ref(date.getDate());
            var month = ref(date.getMonth() + 1);
            var year = ref(date.getFullYear()-14);
        }

        var daysInMonth = ref(new Date(year.value, month.value, 0).getDate());

        const addZeroToDayIfNeed = (day) => {
            var regExp = /^[0]/;
            if (day<10 && !regExp.test(day)) {
                return '0' + day
            } else {
                return day
            }
        }

        const addZeroToMonthIfNeed = (month) => {
            var regExp = /^[0]/;
            if (month<10 && !regExp.test(month)) {
                return '0' + month
            } else {
                return month
            }
        }

        const updateBirthdayDate = (day, month, year) => {
            context.emit('update:modelValue', addZeroToDayIfNeed(day)+'-'+addZeroToMonthIfNeed(month)+'-'+year);
        }

        const editValueOfDaysChangingMonth = (month, year, daySelected) => {
            daysInMonth.value = new Date(year, Number(month), 0).getDate();
            if (daySelected > daysInMonth.value) {
                day.value = daysInMonth.value;
            }
        }


        const editValueOfDaysChangingYear = (month, year, daySelected) => {
            daysInMonth.value = new Date(year, month, 0).getDate();
            if (daySelected > daysInMonth.value) {
                day.value = daysInMonth.value;
            }
        }


        const months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ]



        const startingYear = ref(date.getFullYear()-14);
        const numberofYears = 70
        updateBirthdayDate(day.value, month.value, year.value);



        return {
            months,
            numberofYears,
            startingYear,
            daysInMonth,
            day,
            month,
            year,
            editValueOfDaysChangingMonth,
            editValueOfDaysChangingYear,
            addZeroToDayIfNeed,
            addZeroToMonthIfNeed,
            updateBirthdayDate
        }
    }
}
</script>
<style scoped>

    .datepicker{
        display: flex;
    }

    span {
        display: inline-block;
        margin: 0 2px;
    }
</style>