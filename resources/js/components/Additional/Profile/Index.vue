<template >
    <div v-if="this.PersonProperties !== null" class="mt-5">
        <h5>
            {{ $t('additional.Name') }}
        </h5>
        <div>
            {{ this.PersonProperties.name }}
        </div>

        <div v-if="this.PersonProperties.surname !== null">
            <h5>
                {{ $t('additional.Surname') }}
            </h5>
            <div>
                {{ this.PersonProperties.surname }}
            </div>
        </div>
        
        <div v-if="this.PersonProperties.birthdayDate !== null">
            <h5>
                {{ $t('additional.Birthday_date') }}
            </h5>
            <div>
                {{ this.PersonProperties.birthdayDate }}
            </div>
        </div>

        <div v-if="this.PersonProperties.town !== null && this.PersonProperties.town !== 'null'">
            <h5>
                {{ $t('additional.Town') }}
            </h5>
            <div>
                {{ this.PersonProperties.town }}
            </div>
        </div>

        <div v-if="this.PersonProperties.gender !== null ">
            <h5>
                {{ $t('additional.Gender') }}
            </h5>
            <div >
                {{ $t(`additional.${this.PersonProperties.gender}`) }}
            </div>
        </div>

        <div v-if="this.PersonProperties.familyStatus !== null && this.PersonProperties.familyStatus !== 'null'">
            <h5>
                {{ $t('additional.Family_status') }}
            </h5>
            <div>
                {{ $t(`${this.PersonProperties.familyStatus}`) }}
            </div>
        </div>

        <div v-if="this.PersonProperties.about !== null && this.PersonProperties.about !== 'null'">
            <h5>
                {{ $t('additional.About_yourself') }}
            </h5>
            <div>
                {{ this.PersonProperties.about }}
            </div>
        </div>
    </div>
</template>
<script>
export default {

    props: ['modelValue'],

    computed: {
        PersonProperties:{
            get() {
                return this.modelValue
            },
        }
    }
}
</script>
<style scoped>

    .wrap{
        display: flex;
        flex-direction: column;
    }

    div {
        margin-bottom: 20px;
    }
</style>