<template>
        <div v-if="!isButton" :class=" {
            'mainSideDiv mb-3': !isTopMenuElement,
            'mainTopDiv': isTopMenuElement
            }">
            <router-link :to="$Tr.i18nroute({ name: routerTo})" :class="{
                'mainTop': isTopMenuElement, 
                'mainSide': !isTopMenuElement, 
                'text-decoration-none text-body': routerTo || !routerTo
            }"> 
                <font-awesome-icon v-if="fontawesome" :icon=fontawesome size="xl"/> 
                <span :class=" {
                        'mainTitle': !isTopMenuElement,
                        'mainTitleTop': isTopMenuElement
                    }">
                    <slot></slot>
                    <span v-if="numberWithIcon" class="ms-1 badge bg-primary rounded-pill">{{ this.numberWithIcon}}</span>
                </span> 
            </router-link>
        </div>
        <div v-if="isButton">
            <a href="#" @click.prevent="defineFunction" class="text-decoration-none text-body"> 
                <span class="logout">
                    <slot></slot>
                </span>
            </a>
        </div>    
</template>

<script>
export default {

    name: 'MainMenuButton',

    props: [ 'routerTo', 'fontawesome', 'isTopMenuElement', 'isButton', 'defineFunction', 'numberWithIcon'],


}
</script>

<style scoped>
    .mainSide {
        display: inline-block;
        width: 100%;
    }
    .mainSideDiv {
        position: relative;
    }
    .mainTitle {
        position: absolute;
        left: 30px;   
    }
    .mainTop {
        display: inline-block;
        width: 100%;
        transition-duration: .3s;
    }
    .mainTopDiv {
        margin: 0 3px;
    }

    a.mainSide:hover:not(.router-link-active) .mainTitle {
        text-decoration: underline rgb(254, 254, 254) solid 2px; 
    }

    a.mainTop:hover:not(.router-link-active) .mainTitleTop {
        text-decoration: underline rgb(254, 254, 254) solid 2px; 
    }

    .logout:hover{
        width: 100%;
        text-decoration: underline rgb(254, 254, 254) solid 2px; 
    }


    .mainSide.router-link-active{
        border-radius:5px;
        background-color: #9370DB;
        padding:2% 5%;
        transition-duration: .3s;
        box-shadow: -3px -1px 4px rgb(170, 93, 93);
    }
    
    .mainTop.router-link-active{
        border-radius:5px;
        background-color:rgb(240, 210, 178);
        padding: 1px 3px;
        transition-duration: .3s;
        box-shadow: 2px -2px 3px rgb(221,152,146,1);
    }
    .mainSide.router-link-active .mainTitle {
        position: absolute;
        left: 40px; 
        transition-duration: .5s;
    }




</style>