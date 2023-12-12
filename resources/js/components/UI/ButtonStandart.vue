<template>
    <div class="">
        <input @click.prevent="defineFunction" type="submit" :value="$t(`additional.${value}`)" class="btn btn-b d-block">
    </div>
</template>

<script>
export default {

    name: 'ButtonStandart',

    props: ['defineFunction', 'value'],
}
</script>

<style scoped>

input {
    min-width: 160px;
}

.btn-b {
    background: linear-gradient(196deg, rgba(237,198,156,1) 0%, rgba(221,152,146,1) 47%, rgba(109,100,205,1) 100%);
}
.btn-b:hover{
    background: linear-gradient(16deg, rgba(237,198,156,1) 0%, rgba(221,152,146,1) 47%, rgba(109,100,205,1) 100%);
}
</style>