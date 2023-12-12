<template>
    <div>
        <div ref="messages" contenteditable="true" @keydown="(e)=>countChars(e)" class="textInput form-control mb-2"  @paste.prevent="(e)=>setObjectFromPaste(e)"></div>
        <ButtonSend :defineFunction="SendChatMessage" class="buttonRight"></ButtonSend>
        <Emoji @smile="(e)=>{chooseSmile(e)}" class="emoji me-2 mt-2"></Emoji>
    </div>
</template>
<script>
import CountCharsForMessage from '../../Mixins/CountCharsForMessage';
import Emoji from '@/components/UI/Emoji.vue';
import SetObjectForPaste from '@/Mixins/SetObjectForPaste';
export default {
    mixins: [CountCharsForMessage, SetObjectForPaste],

    props: ['defineAxiosString', 'additional', 'functionForAdditional'],

    components: {
        Emoji
    },

    methods: {
        SendChatMessage(){
            if (typeof this.functionForAdditional != 'undefined'){
                if (typeof this.additional != 'undefined'){
                    console.log(this.functionForAdditional(this.additional));
                    var params = {
                        chat_message: this.$refs.messages.innerHTML,
                        additional: this.functionForAdditional(this.additional)
                    }
                } else {
                    console.log('need additional param');
                }
            } else if (typeof this.additional != 'undefined'){
                var params = {
                    chat_message: this.$refs.messages.textContent,
                    additional: this.additional
                } 
            } else {
                var params = {
                    chat_message: this.$refs.messages.textContent
                }
            }

            axios.post(this.defineAxiosString, params)
                .then(res=>{
                    this.$emit('messageWasSent', res)
                    this.$refs.messages.innerHTML = ''
                })
                .catch(err=>{
                    console.log(err);
                })
        },
        

        chooseSmile(e){
            this.$refs.messages.insertAdjacentHTML('beforeend', e)
        },
    }
}
</script>
<style scoped>
    .textInput{
        max-height: 65px;
        overflow: auto;
    }

    .buttonRight{
        float: right;
    }

    .emoji{
        float: right;
    }
    
</style>