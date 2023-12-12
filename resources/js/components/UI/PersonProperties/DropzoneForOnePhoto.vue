<template lang="">
    <span class="dropzoneWrapper">
        <div class="w-50 d-inline-block">
            <div class="dropzone d-flex btn text-center text-light justify-content-center" ref="dropzone">

            </div>
        </div>
    </span>
</template>
<script>
import Dropzone from 'dropzone';

export default {

    mounted() {
            this.dropzone = new Dropzone(this.$refs.dropzone, {
                url:'ghfggh', 
                dictDefaultMessage: this.$t(this.DefaultMessage),
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF,.pdf,.pub",
                maxFiles: 3,
                thumbnailWidth: null,
                thumbnailHeight: 80,
                autoProcessQueue: false,
                addRemoveLinks: this.addRemoveLinks,
                dictRemoveFile: 'Remove',
                
            });

            setTimeout(() => {
                this.dropzone.on("addedfiles", function(file) {

                    if (document.querySelectorAll('div.dz-complete')[0]){
                        document.querySelectorAll('div.dz-complete')[0].remove();
                    }

                    if (this.element.firstChild.classList.contains('dz-message')){
                        this.element.removeChild(this.element.firstChild);
                    }
                    
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }  
                });

                this.dropzone.on("removedfile", () => {
                    this.$emit('fileWasDeleted', true)
                })
            });
    },

    props: {
        modelValue:'object',
        DefaultMessage: 'string',
        addRemoveLinks:{
            type: Boolean,
            default: false
        }
    },

    computed: {
        dropzone: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue', value)
            }
        },
    }
}
</script>

<style>
    .dz-details{
        display: none;
    }   


    .dz-image-preview{
        margin: 0px 10px;
    }

    .dz-message{
        display:flex;
        justify-content: center;
    }

    .dz-success-mark,
    .dz-error-mark{
        display: none;
    }

    .dz-remove{
        color: black;
        text-decoration: none;

    }

    .dz-remove:hover{
        color: black;
        text-decoration: underline;
    }

    .dz-button{
        width: 100%;
        border-radius: 8px;
        background-color: rgb(240, 227, 227);
        border: none;
    }
</style>

<style scoped>
    .dropzoneWrapper{
        width: 100%;
        text-align: center;
    }

    .dropzone{
        background-color:rgb(240, 227, 227);
        margin: 12px 0px;
        width: 96%;
        min-height: 95px;
        border-color: black;
        border-width: 2px;
    }
</style>
