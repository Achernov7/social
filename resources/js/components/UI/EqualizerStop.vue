<template>
    <div class="box">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
</template>
<script>
export default {
    
}
</script>
<style scoped>

    .box {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        width: 50px;
        height: 50px;
    }

    .box *:not(.line-1) {
        margin-left: 2px;
    }

    .line{
        position: relative;
        width: 10px;
        margin-left: 2px;
        height: 40px;
        background-color: rgb(54, 32, 44);
    }

</style>