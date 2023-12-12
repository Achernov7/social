export default {
    methods: {
        setObjectFromPaste(e){
            const text = e.clipboardData ? e.clipboardData.getData("text/plain") : ""

            const selection = document.getSelection();
            if (!selection) return;
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(new Text(text));
            range.collapse();
            selection.removeAllRanges();
            selection.addRange(range);
            
        },
    }
}