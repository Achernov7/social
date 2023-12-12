<template >
    <Dialog :CommonBackgroundColor="'rgba(0, 0, 0, 0.5)'" v-model:show="dialogVisible">
        <DropboxCustom @stopAddingImage="(e)=>stopAddingImage(e)"/>
    </Dialog>
    <Dialog :minWidthContent="'5px'" :minHeightContent="'5px'" :CommonBackgroundColor="'rgba(0, 0, 0, 0.5)'" v-model:show="showFullImage">
        <Image :imageUrl = "fullUrl"/>
    </Dialog>
    <div class="w-75 mx-auto d-flex justify-content-center h-100">
        <div class="wrapImages">
            <font-awesome-icon icon="fa-solid fa-circle-plus" @click="dialogVisible = true" class="pointer icon" size="lg"/>
            <div ref="scrollMiniImages" class="scrollMiniImages roll" v-hiddenOverflowX @mouseenter="showArrows = true" @mouseleave="showArrows = false">
                <span :ref="`image_${image.id}`" class='miniImg' v-for="image in images" :key="image.id" @click.stop="setPreviewElement(image)">
                    <div class="d-flex">
                        <div @click.stop="scrollToLeft" class="arrowLeft">
                            <font-awesome-icon v-if="showArrows" class="pointer" icon="fa-backward" size="lg"/>
                        </div>
                        <img :src="image.mini_url" style="border-radius: 8px;" class="pointer unselectable">
                        <div @click.stop="scrollToRight" class="arrowRight">
                            <font-awesome-icon v-if="showArrows" class="pointer" icon="fa-forward" size="lg"/>
                        </div>
                        <div v-if="images.indexOf(image) == images.length -3 " v-intersection="getImages" class="observer" ref="observer"></div> 
                    </div>
                </span>
            </div>
            <div class="wrapPreviewImage" unselectable>
                <div v-if="previewElement" class="d-flex justify-content-center pt-5 pb-3">
                    <div class="d-flex justify-content-center" style="width: 30%;">
                        <div class="d-flex flex-column justify-content-center">
                            <font-awesome-icon @click="previousSlide" class="pointer" icon="fa-arrow-left" size="2xl"/>
                        </div>
                    </div>
                    <div style="min-width: 249px;">
                        <div class="d-flex justify-content-center">
                            <img @click="showImageInTrueSize()" class="unselectable" :src="previewElement.preview_url" style="border-radius: 8px;">
                        </div>
                        <div class="d-flex justify-content-end me-3" style="font-size: 0.8em;">
                            {{ $t('additional.Added') }}
                            {{ previewElement.created_at }}
                        </div>
                        <div class="mt-2 d-flex justify-content-center w-100">
                            {{ $t('additional.Make_image_main') }}
                            <span class="actionIcons ms-2 pointer" @click="makeOrUnmakeImageMain">
                                <font-awesome-icon class="ms-2" v-if="!previewElement.is_main_image" icon="fa-check" size="xl"/>
                                <font-awesome-icon class="ms-2" v-else icon="fa-xmark" size="xl"/>
                            </span>
                        </div>
                        <div class="mt-1 d-flex justify-content-center">
                            {{ $t('additional.Delete_image') }}
                            <span class="actionIcons ms-2 pointer" @click="deleteImage">
                                <font-awesome-icon class="ms-2" icon="fa-trash" size="xl"/>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center" style="width: 30%;">
                        <div class="d-flex flex-column justify-content-center">
                            <font-awesome-icon @click="nextSlide" class="pointer" icon="fa-arrow-right" size="2xl"/>
                        </div>
                    </div>
                </div>
                <div v-if="previewElement" class="mt-1 d-flex w-100 justify-content-center">
                    <div v-if="addDescription" style="text-align: center;">
                        <div class="ms-4">
                            {{ $t('additional.Add_description') }}
                            <font-awesome-icon style="float: right;"  @click="addDescription = false" class="pointer" icon="fa-xmark" size="xl"/>
                        </div>
                        <div class="d-flex" style=" width: 400px;">
                            <textarea style="text-align: center; width: 85%; border-bottom-left-radius: 8px; border-top-left-radius: 8px" class="description" v-model="changingDescription"/>
                            <div class="pointer" @click.stop="saveDescription" style="width: 15%; background-color: rgba(221,152,146,1); display: flex; justify-content: center; flex-direction: column; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                <font-awesome-icon icon="fa-check" size="2xl"/>
                            </div>
                        </div>
                    </div>
                    <div v-else style="text-align: center;">
                        <div class="ms-4">
                            {{ $t('additional.Description') }}
                            <font-awesome-icon style="float: right;"  @click="this.addDescription = true" class="pointer" icon="fa-pen" size="xl"/>  
                        </div>
                        <div class="description roll" v-hiddenOverflow style="word-wrap: break-word; overflow: hidden; width: 400px; border-radius: 8px;">
                            {{ previewElement.description }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import DropboxCustom from '@/components/UI/DropboxCustom.vue'
import Image from '@/components/Additional/Images/Image.vue'
export default {

    mounted(){
        this.getImages()
    },

    inheritAttrs: false,

    data(){
        return {
            dialogVisible: false,
            images: [],
            showArrows: false,
            previewElement: null,
            showFullImage: false,
            fullUrl: undefined,
            addDescription: false,
            changingDescription:'',
            limit:15,
            idsAlreadyGetted: [],
            stopFetch: false,
            handleScroll:null
        }
    },
    
    methods: {
        openDialog(){
            this.dialogVisible = true
        },

        getImages(){

            if (this.stopFetch == true){
                return
            }

            axios.post('/api/images/index', {
                limit: this.limit,
                idsAlreadyGetted: this.idsAlreadyGetted
            })
                .then(response => {
                    if (response.data.data.length < this.limit){
                        this.stopFetch = true
                    }
                    this.images = [...this.images, ...response.data.data]
                    
                    if (response.data.data.length > 0){

                        if (this.previewElement == null){
                            this.changePreviewElement(this.images[0])
                            this.$nextTick(() => {
                                this.$refs.scrollMiniImages.children[this.images.indexOf(this.previewElement)].classList.add('active')
                            })
                        }
                        
                        response.data.data.forEach(image => {
                            this.idsAlreadyGetted.push(image.id)
                        })
                    }

                })
                .catch(err=>{
                    console.log(err);
                })
        },

        scrollToRight(){
            this.$refs.scrollMiniImages.scrollTo({
                left: this.$refs.scrollMiniImages.scrollLeft + 360,
                behavior: 'smooth'
            });
        },

        scrollToLeft(){                
            this.$refs.scrollMiniImages.scrollTo({
                left: this.$refs.scrollMiniImages.scrollLeft - 360,
                behavior: 'smooth'
            })
        },

        setPreviewElement(image){
            this.changePreviewElement(image)
            for (var i = 0; i < this.$refs.scrollMiniImages.children.length; i++) {
                if (this.$refs.scrollMiniImages.children[i].classList.contains('active')) {
                    this.$refs.scrollMiniImages.children[i].classList.remove('active')
                    break
                }
            }
            this.$refs[`image_${image.id}`][0].classList.add('active')
        },

        nextSlide(){
            this.setElementIn1Range('right')            
        },

        previousSlide(){
            this.setElementIn1Range('left')
        },

        scrollMiniImagesIfNeeded(i){
            if ( !((this.$refs.scrollMiniImages.children[i].offsetLeft + this.$refs.scrollMiniImages.children[i].offsetWidth < this.$refs.scrollMiniImages.scrollLeft + this.$refs.scrollMiniImages.offsetWidth +25) && (this.$refs.scrollMiniImages.children[i].offsetLeft >= this.$refs.scrollMiniImages.scrollLeft+20)) ){
                this.$refs.scrollMiniImages.style = "overflow-x: auto;"
                this.$refs.scrollMiniImages.scrollTo({
                    left:this.$refs.scrollMiniImages.children[i].offsetLeft - this.$refs.scrollMiniImages.offsetWidth/2 +10,
                    behavior: 'smooth'
                })
                this.$refs.scrollMiniImages.addEventListener('scrollend', () => {
                    this.$refs.scrollMiniImages.style = "overflow-x: hidden;"
                }, { once: true })
            }     
        },

        setElementIn1Range(direction){

            let dontChangeIndex = false
            
            for (var i = 0; i < this.$refs.scrollMiniImages.children.length; i++) {
                if (this.$refs.scrollMiniImages.children[i].classList.contains('active')) {
                    if (direction == 'left' && i == 0){
                        dontChangeIndex = true
                    } else if (direction == 'right' && i == this.$refs.scrollMiniImages.children.length - 1){
                        dontChangeIndex = true
                    } else {
                        this.$refs.scrollMiniImages.children[i].classList.remove('active')
                    }
                    break
                }
            }

            if (dontChangeIndex == true){
                return
            }
            
            const IndexOfPreviewElement = this.images.indexOf(this.previewElement)
            
            if (direction == 'left') {
                if (IndexOfPreviewElement != 0){
                    this.previewElement = this.images[IndexOfPreviewElement - 1]
                    this.$refs.scrollMiniImages.children[IndexOfPreviewElement - 1].classList.add('active')
                    this.scrollMiniImagesIfNeeded(IndexOfPreviewElement - 1)
                }
            } else {
                this.previewElement = this.images[IndexOfPreviewElement + 1]
                this.$refs.scrollMiniImages.children[IndexOfPreviewElement + 1].classList.add('active')
                this.scrollMiniImagesIfNeeded(IndexOfPreviewElement + 1)
            }
            
            this.changingDescription = this.previewElement.description
            
        },

        showImageInTrueSize(){
            this.fullUrl = this.previewElement.url
            this.showFullImage = true
        },

        changePreviewElement(image){
            this.previewElement = image
            this.changingDescription = this.previewElement.description
        },

        makeOrUnmakeImageMain(){
            if (this.previewElement.is_main_image == true){
                this.unmakeImageMain()
            } else {
                this.makeImageMain()
            }
        },

        makeImageMain(){
            axios.post(`/api/images/setMain/${this.previewElement.id}`)
                .then(response => {
                    if (response.data.message === 'main image updated successfully'){
                        
                        for (var i = 0; i < this.images.length; i++) {
                            if (this.images[i].is_main_image == true){
                                this.images[i].is_main_image = false
                                break
                            }
                        }

                        this.previewElement.is_main_image = true
                    }

                })
                .catch(err=>{
                    console.log(err);
                })
        },

        unmakeImageMain(){
            axios.post(`/api/images/unsetMain/${this.previewElement.id}`)
                .then(response => {
                    if (response.data.message === 'main image updated successfully'){
                        this.previewElement.is_main_image = false
                    }
                })
                .catch(err=>{
                    console.log(err);
                })
        },

        saveDescription(){
            axios.post(`/api/images/updateDescription/${this.previewElement.id}`, {description: this.changingDescription})
                .then(response => {
                    if (response.data.message === 'description updated successfully'){
                        this.addDescription = false
                        this.previewElement.description = this.changingDescription
                    }
                })
                .catch(err=>{
                    console.log(err);
                })
        },

        tryToAddDescription(){
            this.addDescription = true
            this.changingDescription = this.previewElement.description
        },

        deleteImage(){
            axios.delete(`/api/images/delete/${this.previewElement.id}`)
                .then(response => {
                    if (response.data.message === 'image deleted successfully'){
                        if (this.images.length > 1){
                            if (this.images.indexOf(this.previewElement) == 0){
                                this.images.splice(0, 1)
                                this.setPreviewElement(this.images[0])
                            } else {
                                this.setPreviewElement(this.images[this.images.indexOf(this.previewElement) - 1])
                                this.images.splice(this.images.indexOf(this.previewElement)+1, 1)
                                this.scrollMiniImagesIfNeeded(this.images.indexOf(this.previewElement))
                            }
                        } else {
                            this.images.splice(0, 1)
                            this.previewElement = null
                        }
                    }
                })
        },

        stopAddingImage(e){

            this.images = [...e.reverse(), ...this.images]
            this.dialogVisible = false

            for (var i = 0; i < this.$refs.scrollMiniImages.children.length; i++) {
                if (this.$refs.scrollMiniImages.children[i].classList.contains('active')) {
                    this.$refs.scrollMiniImages.children[i].classList.remove('active')
                    break
                }
            }
            if (this.previewElement > 0){
                this.$refs.scrollMiniImages.children[this.images.indexOf(this.previewElement)].classList.add('active')
            }

        }
    },

    components: {
        DropboxCustom,
        Image
    }
}
</script>
<style scoped>

    .actionIcons{
        width: 40px; 
        padding-left: 3px;
        transition: 0.5s;
    }

    .actionIcons:hover{
        background-color: rgb(221, 152, 146);
        border-radius: 8px;
    }

    textarea{
        resize: none;
    }

    textarea:focus{
        outline: none;
    }

    .description{
        height: 100px;
        background-color: rgb(210, 162, 162);
        padding: 2px 5px;
        position: relative;
    }

    .wrapPreviewImage{
        background-color: #bc7e7e;
        border-radius: 8px;
        margin: 8px auto;
        width: 98%;
        height: 720px;
    }

    .arrowLeft{
        position: absolute;
        top: 50px;
        left: 20px;
        border-radius: 5px;
        padding: 2px 4px;
    }

    .active{
        box-shadow: 0px 0px 2px 5px rgba(6, 6, 29, 0.7);
    }

    .arrowRight{
        position: absolute;
        right: 20px;
        top: 50px;
        border-radius: 5px;
        padding: 2px 4px;
    }

    .arrowLeft:hover, .arrowRight:hover{
        background-color: rgba(221,152,146,0.7);
    }

    .miniImg{
        margin: 20px 8px;
    }

    .scrollMiniImages{
        overflow-x: auto;
        height: 100px;
        width: 98%;
        margin: auto;
        box-sizing: border-box;
        border-radius: 8px;
        overflow-x: hidden;
        background-color:  rgb(210, 162, 162);
        display: flex;
    }
    .icon{
       position: absolute; 
       margin-left: calc(var(--width-bg) - 27px);
       margin-top: -28px;
    }
    .wrapImages{
        --width-bg: 850px;
        height: 100%;
        width: var(--width-bg);
        padding: 6px 4px;
        background-color: rgba(221,152,146,1);
        border-radius: 8px;
        position: relative;
    }
</style>