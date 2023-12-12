<script>
    export default {
        name: 'vueTitle',
        props: ['title'],
        watch: {
            title: {
                immediate: true,
                handler() {
                    document.title = this.title;
                }
            }
        },
        render () {
        },
    }
</script>