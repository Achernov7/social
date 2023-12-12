import {createRouter, createWebHistory} from 'vue-router';

import Tr from './i18n/translation';


const routes=[
        {
            path:"/:locale?/news",
            name:'get.news',
            component:() => import('./components/Main/GetPosts.vue')
        },
        {
            path:"/:locale?/user/login",
            name:'user.login',
            component:() => import('./components/Main/Login.vue'),
            
        },  
        {
            path:"/:locale?/user/registration",
            name:'user.registration',
            component:() => import('./components/Main/Registration.vue')
        },
        {
            path:"/:locale?/user/profile",
            name:'user.profile',
            component:() => import('./components/Main/Profile.vue')
        },
        {
            path:"/:locale?/user/profile/:id",
            name:'user.profileOf',
            component:() => import('./components/Main/Profile.vue')
        },
        {
            path:"/:locale?/user/friends",
            name:'user.friends',
            component:() => import('./components/Main/Friends.vue'),
        },
        {
            path:"/:locale?/user/music",
            name:'user.music',
            component:() => import('./components/Main/Music.vue')
        },  
        {
            path:"/:locale?/user/gallery",
            name:'user.gallery',
            component:() => import('./components/Main/Gallery.vue')
        },
        {
            path:"/:locale?/user/groups",
            name:'user.groups',
            component:() => import('./components/Main/Groups.vue')
        },
        {
            path:"/:locale?/user/messages/",
            name:'user.messages',
            component:() => import('./components/Main/Messages.vue')
        },
        {
            path:"/:locale?/user/messages/:id?",
            name:'user.messageTo',
            component:() => import('./components/Main/dialogWindow.vue')
        },
        {
            path:"/:locale?/404",
            name:'404',
            component:() => import('./components/Main/404.vue')
        },
        {
            path:"/:catchAll(.*)",
            name:'catch',
            component:() => import('./components/Main/404.vue')
        }
];  

const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_BASE_URL),
    routes
});


router.beforeEach((to, from, next) => {
    Tr.routeMiddleware(to, from, next)
    
})


export default router;