export default {

    data () {
        return {
            position: 0
        }
    },

    methods: {
        keyup(event, index) {

            if (event.key === 'ArrowUp') {
                if (index !== 1) {
                    this.changePositionForArrowUp(index)
                }
            } else if (event.key === 'ArrowDown') {
                if (index < this.inputsLength) {
                    this.changePositionForArrowDown(index)
                }
            } else {
                this.position = event.target.selectionStart;
            }
        },

        changePositionForArrowUp(index) {
            if (!(this.$refs[index - 1][0].value.length > this.position)) {
                this.position = this.$refs[index - 1][0].value.length
            };
            this.setFocus(index - 1)
        },

        changePositionForArrowDown(index) {
            if (this.$refs[index + 1][0].value.length < this.position) {
                this.position = this.$refs[index + 1][0].value.length
            };
            this.setFocus(index+1)
        },

        setFocus(index) {
            this.$refs[index][0].setSelectionRange(this.position, this.position);
            this.$refs[index][0].focus()
        },
        

        keydown(event) {
            if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
                event.preventDefault();
            }
        }
    },  


    computed: {
        inputsLength() {
            return this.inputs.length
        }
    },
}