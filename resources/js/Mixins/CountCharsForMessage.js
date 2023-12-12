export default {
    methods: {
        countChars(e) {
            if (this.$refs.messages.innerHTML.length > 498 && e.key !== 'Backspace' && e.key !== 'Delete' ) {
                e.preventDefault();
            }
        }
    }
}