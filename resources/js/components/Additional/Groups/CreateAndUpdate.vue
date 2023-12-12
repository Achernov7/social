<template>
    <div class="d-flex flex-column justify-content-center h-100">
        <h4 v-if="!isUpdate" class="mb-4">
            {{ $t('additional.Creating_a_group') }}
        </h4>
        <h4 v-if="isUpdate" class="mb-4">
            {{ $t('additional.Edit_group') }}
        </h4>
        <DropzoneForOnePhoto v-model="photo" :DefaultMessage="'additional.Add_image'" class="ms-3 mb-5"></DropzoneForOnePhoto>
        <div class="table">
            <div class="tableRow">
                <div class="leftCells">
                    <label class="mb-4" for="Name">{{ $t('additional.Name') }}</label>
                </div>
                <div class="rightCells">
                    <input maxlength="50" id="Name" ref="Name" type="text" class="mb-4">
                </div>
            </div>
            <div class="tableRow">
                <div class="leftCells">
                    <label for="Description">{{ $t('additional.Description') }}</label>
                </div>
                <div class="rightCells">
                    <textarea maxlength="255" ref="Description" name="Description" id="Description" class="mb-1"></textarea>
                </div>
            </div>
        </div>
        <div class="table">
            <div class="tableRow">
                <div style="vertical-align: text-top;" class="leftCells">
                    <label for="Links">{{ $t('additional.Links') }}</label>
                </div>
                <div class="rightCells">
                    <div class="w-100" style="background-color: #bc7e7e;">
                        <input ref="addLink" class="mb-3" style="padding-right: 22px" id="Links" type="text">
                        <font-awesome-icon @click="addToTheListOfLinks()" icon="fa-solid fa-plus" class="plus" size="lg"/>
                        <div class="wrapLinks">
                            <div class="mb-2" v-for="link in ListOfLInks" :key="link">
                                <div @click.prevent="" class="linksDiv">
                                    <span class="linkInarray">
                                        {{ link }}
                                    </span>
                                    <font-awesome-icon @click.prevent="DeleteFromTheListOfLinks()" icon="fa-solid fa-minus" class="minus me-1 mt-1" size="lg"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ButtonSend :defineFunction="SendData" class="ms-3"></ButtonSend>
    </div>
</template>
<script>
import DropzoneForOnePhoto from '@/components/UI/PersonProperties/DropzoneForOnePhoto.vue'
import DropzoneForEdit from '@/Mixins/DropzoneForEdit'

export default {

    data(){
        return{
            ListOfLInks: [],
            photo: null,
            isUpdate: false,
            idToUpdate: null
        }
    },

    props: [
        'params'
    ],

    mixins: [DropzoneForEdit],

    mounted(){
        if (this.params != null){
            this.isUpdate = true
            this.idToUpdate = this.params.id
            this.$refs.Name.value = this.params.name
            this.$refs.Description.value = this.params.description
            this.ListOfLInks = this.params.links
            if (!this.params.preview_url.preview_url.includes(`${import.meta.env.VITE_URL}/storage/images/default_image`)){
                this.DropzoneForEdit(this.params.preview_url.preview_url)
            }
        }
    },

    components: {
        DropzoneForOnePhoto
    },

    methods: {
        addToTheListOfLinks(){
            if (this.$refs.addLink.value.length > 0){
                if (this.ListOfLInks.length > 4){
                    this.ListOfLInks.splice(0, 1)
                }
                this.ListOfLInks.push(this.$refs.addLink.value)
                this.$refs.addLink.value = ''
            }
        },

        DeleteFromTheListOfLinks(){
            this.ListOfLInks.splice(this.ListOfLInks.indexOf(this.$refs.addLink.value), 1)
        },

        SendData(){
            const data = new FormData()
            const files = this.photo.getAcceptedFiles()
            if (files.length > 0) {
                data.append('image', files[0])
            }
            if (this.$refs.Name.value.length == 0){
                console.log('Name is empty')
            } else {
                data.append('name', this.$refs.Name.value)
            }

            if (this.$refs.Description.value.length == 0){
                console.log('Description is empty')
            } else {
                data.append('description', this.$refs.Description.value)
            }
            for (let i = 0; i < this.ListOfLInks.length; i++){
                data.append(`links[${i}]`, this.ListOfLInks[i])
            }

            if (this.$refs.Description.value.length>0 && this.$refs.Name.value.length>0){
                if (this.isUpdate){
                    
                    axios.post(`/api/groups/update/${this.idToUpdate}`, data)
                        .then(response => {
                            
                            if (response.data.message === 'group updated successfully'){
                                this.$emit('bookmark', {'bookmark': 'Index'})
                            }
                        })
                        .catch(err => {
                            console.log(err)
                        })
                } else {
                    axios.post('/api/groups/create', data)
                        .then(response => {
                            
                            if (response.data.message === 'group created successfully'){
                                this.$emit('bookmark', {'bookmark': 'Index'})
                            }
                        })
                        .catch(err => {
                            console.log(err)
                        })
                }
            }
        }
    }
}
</script>

<style scoped>

    label{
        background-color: #bc7e7e;
    }

    .linkInarray{
        display: inline-block;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        padding-right: 30px;
        margin-left: 5px;
    }

    .nondisplay{
        display: none;
    }
    .wrapLinks{
        display: block;
        height: 160px;
    }

    .minus{
        position: relative;
        cursor: pointer;
        float: right;
        bottom: 32px;
    }

    .tableRow{
        display: table-row;
    }

    .table{
        display: table;
        table-layout: fixed;
        width: 100%;
    }

    .leftCells{
        display: table-cell;
        width: 20%;
        vertical-align: middle;
        text-align: center;
    }

    .rightCells{
        display: table-cell;
        width: 100%;
    }

    .linksDiv{
        cursor: default;
        background-color: rgb(240, 227, 227);
        overflow: hidden;
        width: 554px;
        border-radius: 8px;
        margin-bottom: 4px;
        height: 23px;
    }

    .plus{
        cursor: pointer;
        position: relative;
        right: 20px;
    }

    H4{
        margin: 0 auto;
        margin-bottom: 5px;
    }
    input{
        border-radius: 8px;
        width: calc(100% - 30px);
        padding-left: 8px;
        padding-right: 8px;
        outline: none;
        border: none;
        background: rgb(240, 227, 227);
    }

    input:focus{
        box-shadow: rgb(104, 106, 162) 0px 0px 3px 4px;
    }

    textarea{
        background: rgb(240, 227, 227);
        border-radius: 8px;
        width: calc(100% - 30px);
        outline: none;
        border: none;
        padding-left: 8px;
        padding-right: 8px;
        padding-top: 3px;
        height: 80px;
    }

    textarea:focus{
        box-shadow: rgb(104, 106, 162) 0px 0px 3px 4px;
    }
</style>