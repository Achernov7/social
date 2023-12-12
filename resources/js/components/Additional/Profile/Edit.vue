<template >
    <DropzoneForOnePhoto v-model="dropzone" :DefaultMessage="'additional.Add_avatar'"></DropzoneForOnePhoto>
    <Name v-model="name"></Name>
    <Surname v-model="surname"></Surname>
    <div class="mt-2 mb-2">
        <DatePicker v-model="birthdayDate"></DatePicker>
    </div>
    <SearchingTownUl v-model="town"></SearchingTownUl>
    <ChooseGender :onlyMaleFemale="true" v-model="gender"></ChooseGender>
    <SelectFamilyStatus v-model="familyStatus"></SelectFamilyStatus>
    <AboutYourself v-model="about"></AboutYourself>
    <div class="w-100 mt-3">
        <ButtonSend :defineFunction="SendData" class="w-50"></ButtonSend>
    </div>
</template>
<script>
import Name from '@/components/UI/PersonProperties/Name.vue'
import Surname from '@/components/UI/PersonProperties/Surname.vue'
import DatePicker from '@/components/UI/Datapicker/DatePicker.vue';
import DropzoneForOnePhoto from '@/components/UI/PersonProperties/DropzoneForOnePhoto.vue'
import AboutYourself from '@/components/UI/PersonProperties/AboutYourself.vue';
export default {

    name: 'editProfile',

    props: ['modelValue'],

    emits: ['ShouldLoadUserdata', 'change'],

    components: {
        AboutYourself,
        DropzoneForOnePhoto,
        Surname,
        Name,
        DatePicker
    },

    data() {
        return {
            name: this.modelValue.name !== null ? this.modelValue.name : '',
            surname: this.modelValue.surname !== null ? this.modelValue.surname : '',
            birthdayDate: this.modelValue.birthdayDate !== null ? this.modelValue.birthdayDate : '',
            dropzone: null,
            about: this.modelValue.about !== null && this.modelValue.about !== 'null' ? this.modelValue.about : '',
            town: this.modelValue.town !== null && this.modelValue.town !== 'null' ? this.modelValue.town : '',
            gender: this.modelValue.gender !== null ? this.modelValue.gender : 'Male',
            familyStatus: this.modelValue.familyStatus !== null ? this.modelValue.familyStatus : 'additional.Choose_status',
        }
    },

    methods: {
        SendData() {
            const data = new FormData()
            const files = this.dropzone.getAcceptedFiles()
            if (files.length > 0) {
                data.append('image', files[0])
            }
            if (this.familyStatus !== 'additional.Choose_status') {
                data.append('familyStatus', this.familyStatus)
            }
            data.append('name', this.name)
            data.append('surname', this.surname)
            data.append('birthdayDate', this.birthdayDate)
            data.append('town', this.town)
            data.append('gender', this.gender)
            data.append('about', this.about)
            axios.post('/api/users/store', data)
                .then(response => {
                    if(response.data.message === 'user updated successfully'){
                        this.$emit('ShouldLoadUserdata')
                        this.$emit('change', 'Index')
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        }
    }
}
</script>
<style scoped>
    
</style>