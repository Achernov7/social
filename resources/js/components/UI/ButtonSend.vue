<template>
        <input @click.prevent="defineFunction" type="submit" :value="$t('additional.Send')" class="btn btn-b">
</template>

<script>
export default {

    name: 'ButtonSend',
    
    props: ['defineFunction'],
}
</script>

<style scoped>
    .btn-b {
        background: linear-gradient(196deg, rgba(237,198,156,1) 0%, rgba(221,152,146,1) 47%, rgba(109,100,205,1) 100%);
    }
    .btn-b:hover{
        background: linear-gradient(16deg, rgba(237,198,156,1) 0%, rgba(221,152,146,1) 47%, rgba(109,100,205,1) 100%);
    }
    .btn-b:active{
        box-shadow: 4px -4px 6px 0px rgb(255, 255, 255);
    }
</style>