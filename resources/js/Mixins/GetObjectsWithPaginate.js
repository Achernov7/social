export default {

    methods: {
        getObjects(ObjectWithparameters){
            if (ObjectWithparameters.lastSearch != ObjectWithparameters.searchingObject){
                ObjectWithparameters.page = 0
                ObjectWithparameters.observerLine = 0
                this.stopLoading = false
                ObjectWithparameters.ListOfObjects = [],
                ObjectWithparameters.firstGroupOfObjects = null,
                ObjectWithparameters.secondGroupOfObjects = null,
                ObjectWithparameters.elementsAlreadyTook = null
            }
            if (ObjectWithparameters.gettingObject){
                clearTimeout(ObjectWithparameters.gettingObject)
            }

            ObjectWithparameters.gettingObject = setTimeout(() => {
                this.axiosGet(ObjectWithparameters)
            }, 700);
        },

        axiosGet(ObjectWithparameters){
            
            axios.get(ObjectWithparameters.axiosString, {
                    params: {
                        search:ObjectWithparameters.searchingObject,
                        page: ObjectWithparameters.page,
                        limit: ObjectWithparameters.limit,
                        elementsAlreadyTook: ObjectWithparameters.elementsAlreadyTook,
                        secondGroupOfObjects: ObjectWithparameters.secondGroupOfObjects
                    }
                })
                    .then(res => {
                        
                        ObjectWithparameters.ListOfObjects = [...ObjectWithparameters.ListOfObjects, ...res.data.data]
                        ObjectWithparameters.observerLine = ObjectWithparameters.ListOfObjects.length - 3
                        ObjectWithparameters.page++
                        if (res.data.data.length < ObjectWithparameters.limit){
                            this.stopLoading = true
                        }
                        if (res.data.alreadyTook){
                            ObjectWithparameters.elementsAlreadyTook = res.data.alreadyTook
                        }
                        res.data.data.forEach(element => {
                            if ("authenticated" in element){
                                if (element.authenticated == 'creator'){
                                    if (ObjectWithparameters.firstGroupOfObjects == null){
                                        ObjectWithparameters.firstGroupOfObjects = element.id
                                    }
                                } if (element.authenticated == 'notConnectedwithYou'){
                                    if (ObjectWithparameters.secondGroupOfObjects == null){
                                        ObjectWithparameters.secondGroupOfObjects = element.id
                                    }
                                } else if (element.authenticated == 'subscribeTo'){
                                    if (ObjectWithparameters.thirdGroupOfObjects == null){
                                        ObjectWithparameters.thirdGroupOfObjects = element.id
                                    }
                                }
                            }
                        });
                        ObjectWithparameters.lastSearch = ObjectWithparameters.searchingObject
                    })
                    .catch(err => {
                        console.log(err)
                    })
        },
    }
}