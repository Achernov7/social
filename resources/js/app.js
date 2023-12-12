import './bootstrap';
import { createApp } from 'vue';
import router from '@/router';
import i18n from '@/i18n/i18n';
import App from '@/components/App.vue'
import GlobalVariables from '@/globalVariables'
import fontawesome from '@/components/UI/fontawesome'
import components from '@/components/UI'
import directives from '@/directives'


const app = createApp(App);


for (let key in GlobalVariables) {
    app.config.globalProperties[`$${key}`] = GlobalVariables[key];
}

app.component(fontawesome.name, fontawesome.fontAweSomeIcon)
components.forEach(component => {
    app.component(component.name, component)
})

directives.forEach(directive => {
    app.directive(directive.name, directive)
})

app
    .use(router)
    .use(i18n)
    .mount('#app');
