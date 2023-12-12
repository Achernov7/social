export default {
    methods: {
        countChars(e) {                
            if (this.$refs.searchfield.innerHTML.length > 70 && e.key !== 'Backspace' && e.key !== 'Delete' ) {
                e.preventDefault();
            }
        }
    },
}