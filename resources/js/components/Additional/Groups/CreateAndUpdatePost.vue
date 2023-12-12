<template>
    <div class="d-flex flex-column justify-content-center h-100">
        <h4 v-if="!isUpdate" class="mb-4">
            {{ $t('additional.Create_post') }}
        </h4>
        <h4 v-if="isUpdate" class="mb-4">
            {{ $t('additional.Edit_post') }}
        </h4>
        <DropzoneForOnePhoto :addRemoveLinks="true" @fileWasDeleted="FileWasDeleted = true" v-model="photo" :DefaultMessage="'additional.Add_image'" class="ms-3 mb-5"></DropzoneForOnePhoto>
        <textarea ref="Description" maxlength="255" name="Description" class="ms-5 mb-2" style="text-align: center;"></textarea>
        <div class="d-flex justify-content-center w-100 mt-4">
            <ButtonSend :defineFunction="SendData" class="button ms-1 mt-5"></ButtonSend>
        </div>
    </div>
</template>
<script>
import DropzoneForOnePhoto from '@/components/UI/PersonProperties/DropzoneForOnePhoto.vue'
import DropzoneForEdit from '@/Mixins/DropzoneForEdit'
export default {
    
    components: {
        DropzoneForOnePhoto
    },

    props: ['params'],

    emits: ['bookmark'],

    mixins: [DropzoneForEdit],

    data() {
        return {
            photo: null,
            paramsOfGroup: {},
            isUpdate: false,
            idToUpdate: null,
            FileWasDeleted: false
        }
    },

    mounted() {
        if (this.params != null) {
            if (this.params.group){
                this.paramsOfGroup = this.params.group
            }
            if (this.params.post){
                this.isUpdate = true
                this.idToUpdate = this.params.post.id
                this.$refs.Description.value = this.params.post.description
                if (this.params.post.image.preview_image != null){
                    this.DropzoneForEdit(this.params.post.image.preview_image.preview_url)
                }
            }
        }
    },

    methods: {
        SendData(){
            const data = new FormData()

            const files = this.photo.getAcceptedFiles()
            if (files.length > 0) {
                data.append('image', files[0])
            } else if (this.isUpdate && this.FileWasDeleted){
                data.append('image', null)
            }

            if (this.$refs.Description.value.length == 0){
                console.log('Description is empty')
            } else {
                data.append('description', this.$refs.Description.value)
            }
            
            if (this.$refs.Description.value.length>0){
                if (this.isUpdate){
                    axios.post(`/api/groups/${this.paramsOfGroup.id}/posts/update/${this.idToUpdate}`, data)
                        .then(response => {
                            if (response.data.message === 'post updated successfully'){
                                this.$emit('bookmark', {'bookmark': 'Moderate', 'params': this.paramsOfGroup})
                            }
                        })
                        .catch(
                            err => {
                                console.log(err)
                            }
                        )
                } else {
                    axios.post(`/api/groups/${this.paramsOfGroup.id}/posts/create`, data)
                        .then(response => {
                            if (response.data.message === 'post created successfully'){
                                this.$emit('bookmark', {'bookmark': 'Moderate', 'params': this.paramsOfGroup})
                            }
                        })
                        .catch(
                            err => {
                                console.log(err)
                            }
                        )
                }
            }
        }
    }
}
</script>
<style scoped>

    textarea{
        background: rgb(240, 227, 227);
        border-radius: 8px;
        width: calc(100% - 80px);
        outline: none;
        border: none;
        padding-left: 8px;
        padding-right: 8px;
        padding-top: 3px;
        height: 120px;
    }

    H4{
        margin: 0 auto;
        margin-bottom: 5px;
    }

    .button{
        width: 85%;
        display: inline-block;
    }
    
</style>