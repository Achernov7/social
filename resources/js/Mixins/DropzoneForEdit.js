export default {
    methods: {
        DropzoneForEdit(url){
            let mockFile = { name: "Filename 2", size: 12345 };
            const elem = document.querySelectorAll('div.dz-message');
            elem[0].style.display = "none";
            this.photo.displayExistingFile(mockFile, url);
        }
    },
}