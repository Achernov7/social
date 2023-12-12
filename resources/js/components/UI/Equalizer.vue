<template>
    <div class="box">
        <div class="line-1 line"></div>
        <div class="line-2 line"></div>
        <div class="line-3 line"></div>
        <div class="line-4 line"></div>
        <div class="line-5 line"></div>
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

    @keyframes go-up-down {
        0% { height: 0; }
        100% { height: 40px; }
    }

    @keyframes color_change_1 {
        0% { background-color: rgb(228, 147, 191); }
        100% { background-color: rgb(177, 112, 146); }
    }

    @keyframes color_change_2 {
        0% { background-color: rgb(200, 130, 168); }
        100% { background-color: rgb(138, 87, 113); }
    }

    @keyframes color_change_3 {
        0% { background-color: rgb(180, 114, 150); }
        100% { background-color: rgb(108, 67, 88); }
    }

    @keyframes color_change_4 {
        0% { background-color: rgb(154, 96, 128); }
        100% { background-color: rgb(82, 50, 67); }
    }
    
    @keyframes color_change_5 {
        0% { background-color: rgb(130, 80, 107); }
        100% { background-color: rgb(54, 32, 44); }
    }

    .line{
        position: relative;
        width: 10px;
        margin-left: 2px;
    }

    .line-1 {
        animation-name: go-up-down, color_change_1;
        animation-duration: 0.3s, 0.3s;
        animation-iteration-count: infinite, infinite;
        animation-direction: alternate, alternate;
    }

    .line-2 {
        animation-name: go-up-down, color_change_2;
        animation-duration: 0.4s, 0.4s;
        animation-iteration-count: infinite, infinite;
        animation-direction: alternate, alternate;
    }

    .line-3 {
        animation-name: go-up-down, color_change_3;
        animation-duration: 0.6s, 0.6s;
        animation-iteration-count: infinite, infinite;
        animation-direction: alternate, alternate;
    }

    .line-4 {
        animation-name: go-up-down, color_change_4;
        animation-duration: 0.7s, 0.7s;
        animation-iteration-count: infinite, infinite;
        animation-direction: alternate, alternate;
    }

    .line-5 {
        animation-name: go-up-down, color_change_5;
        animation-duration: 0.9s, 0.9s;
        animation-iteration-count: infinite, infinite;
        animation-direction: alternate, alternate;
    }


</style>