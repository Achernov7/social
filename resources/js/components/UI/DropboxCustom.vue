<template>
    <div>
        <div @dragenter.stop.prevent @dragover.stop.prevent @drop="(e)=>drop(e)" type="file" ref="dropbox" class="dropbox d-flex flex-column justify-content-center" style="text-align: center; margin: auto;" @click.prevent="this.$refs.imageInput.click()">
            <div ref="inscription">
                {{ $t('additional.Add_no_more_than_4_images_that_no_larger_than_4MB') }}
            </div>
        </div>
        <div v-if="loadPercent != 0" class="d-flex flex-column justify-content-center mt-3" style="height: 80px;">
            <div class="d-flex justify-content-center h-100">
                <div class="d-flex flex-column justify-content-center" style="font-weight: bold;">{{ $t('additional.Loading') }}</div>
                <div class="ms-3 d-flex justify-content-start" style="width: 300px;">
                    <div class="loading"/>
                </div>
            </div>
        </div>
        <input ref="imageInput" type="file" multiple accept="image/*" style="visibility: hidden; position: absolute;" @change="(e)=>handleFilesForInput(e)">
        <div class="w-100 d-flex justify-content-center mt-3" v-if="loadPercent == 0">
            <ButtonStandart :defineFunction="send" :value="'Save'"/>
        </div>
    </div>
</template>
<script>
export default {

    data() {
        return {
            loadPercent: 0
        }
    },

    methods:{
        drop(e){
            e.preventDefault()
            e.stopPropagation()
            let files = e.dataTransfer.files
            this.handleFiles(files)
        },

        handleFiles(files){
            if (this.$refs.dropbox.firstElementChild == this.$refs.inscription) {
                this.$refs.dropbox.firstElementChild.remove();
            }
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.startsWith("image/")) {
                    continue;
                }

                if (this.$refs.dropbox.childElementCount > 3) {
                    this.$refs.dropbox.firstElementChild.remove();
                }

                const img = document.createElement("img");
                const div = document.createElement("div");
                const nameOfPicture = document.createElement("span");
                
                img.style = "margin: 5px; border-radius: 8px; ";
                img.file = file;
                img.height = 70;
                img.width = 70;
                div.style = "display: flex; align-items: center;";
                nameOfPicture.style = "margin-left: 5px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;";

                nameOfPicture.textContent = file.name;
                div.appendChild(img);
                div.appendChild(nameOfPicture);
                this.$refs.dropbox.appendChild(div);

                const reader = new FileReader();
                reader.onload = (e) => {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        handleFilesForInput(e){
            if (e.target.files.length) { 
                this.handleFiles(e.target.files)
            }
        },

        send(){
            const imgs = this.$refs.dropbox.getElementsByTagName('img');
            let data = new FormData();
            for (let i = 0; i < imgs.length; i++) {
                data.append('images[]', imgs[i].file);
            }
            axios({
                    method: 'post',
                    url: '/api/images/store',
                    data: data,
                    onUploadProgress: (progressEvent) => {
                        this.loadPercent = `${Math.round(progressEvent.progress* 1000)/10}%`
                    }
                })
                    .then(res => {
                        this.loadPercent = 0
                        this.$emit('stopAddingImage', res.data.data)
                    })
                    .catch(err=>{
                        this.loadPercent = 0
                        console.log(err);
                    })
        }
    }
}
</script>
<style scoped>

    .loading{
        height: 15px;
        width: v-bind(loadPercent);
        margin: auto 0;
        border-radius: 5px;
        background-color: rgb(174, 125, 121);
    }
    .dropbox{
        background-color: rgb(243, 255, 240);
        min-height: 100px;
        width: 280px;
        border-radius: 8px;
    }
</style>