<template>
    <div>
        <img :src="imageUrl" alt="">
    </div>
</template>
<script>
export default {
    props: {
        imageUrl: {
            type: String,
        }
    }
}
</script>
<style scoped>
    
</style>