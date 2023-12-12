import i18n from './i18n'
import { nextTick } from 'vue';

const Trans = {
    get defaultLocale(){
        return import.meta.env.VITE_DEFAULT_LOCALE
    },

    get supportedLocales(){
        return import.meta.env.VITE_SUPPORTED_LOCALES.split(",");
    },

    set currentLocale(newLocale){
        i18n.global.locale.value = newLocale
    },

    get currentLocale(){
        return i18n.global.locale.value
    },

    islocaleSupported(locale){
        return Trans.supportedLocales.includes(locale)
    },

    getUserlocale(){
        const locale =window.navigator.language ||
        window.navigator.userLanguage  ||
        Trans.defaultLocale
        return {
            locale: locale,
            localeNoRegion: locale.split('-')[0]
        }
    },

    getPersisterdLocale(){
        const persistedLocale = localStorage.getItem('user-locale')

        if(Trans.islocaleSupported(persistedLocale)){
            return persistedLocale
        } else {
            return null
        }
    },

    guessDefaultLocale(){
        const userPersistedlocale = Trans.getPersisterdLocale()
        if(userPersistedlocale){
            return userPersistedlocale
        }

        const userPrefferredLocale = Trans.getUserlocale()

        if (Trans.islocaleSupported(userPrefferredLocale.locale)){
            return userPrefferredLocale.locale
        }

        if (Trans.islocaleSupported(userPrefferredLocale.localeNoRegion)) {
            return userPrefferredLocale.localeNoRegion
        }

        return Trans.defaultLocale
    },

    async switchLanguage(newLocale){
        await Trans.loadLocaleMessages(newLocale)
        Trans.currentLocale = newLocale
        document.querySelector('html').setAttribute('lang', newLocale)
        localStorage.setItem('user-locale', newLocale)
    },

    async loadLocaleMessages(locale){
        if(!i18n.global.availableLocales.includes(locale)){
            const messages = await import(`./locales/${locale}.json`)
            i18n.global.setLocaleMessage(locale, messages.default)
        }
        
        return nextTick();
    },


    async routeMiddleware(to, from, next){
                
        const paramLocale = to.params.locale

        if (!Trans.islocaleSupported(paramLocale)){

            Trans.currentLocale = Trans.guessDefaultLocale()
            await Trans.switchLanguage(Trans.currentLocale)
            to.params.locale = Trans.currentLocale
            
        } else {
            await Trans.switchLanguage(paramLocale)
        }

        Trans.route(to, from, next)    
    },

    route(to, from, next){
        document.title = to.meta.title || i18n.global.t(`titles.${import.meta.env.VITE_DEFAULT_TITLE}`);
        const token = localStorage.getItem('x_xsrf_token');
        if(!token){
            if (to.name === 'user.login' || to.name === 'user.registration') {
                return next();
            } else {
                return next({path: `/${Trans.currentLocale}/user/login`});
            }
        }
        
        if (to.name === 'user.login' || to.name === 'user.registration' || to.path === '/' && token){
            return next({path: `/${Trans.currentLocale}/user/profile`});
        }
        
        return next();
    },

    i18nroute(to) {
        return {
            ...to,
            params: {
                locale: Trans.currentLocale,
                ...to.params
            }
        }
    }
}

export default Trans