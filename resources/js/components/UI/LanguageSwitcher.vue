<template lang="">
    <select @change="switchlanguage">
        <option v-for="sLocale in supportedLocales" 
            :key="`locale-${sLocale}`" 
            :value="sLocale"
            :selected="locale === sLocale"
            >
            {{ t(`locale.${sLocale}`) }}
        </option>
    </select>
</template>
<script>
import { useI18n } from 'vue-i18n';
import Tr from '@/i18n/translation';
import { useRouter } from 'vue-router';
export default {
        setup() {
            const { t, locale } = useI18n();
            const supportedLocales = Tr.supportedLocales;

            const router = useRouter();

            const switchlanguage = async(event) => {
                const newlocale = event.target.value;

                await Tr.switchLanguage(newlocale);

                try {
                    await router.replace({ params: {locale: newlocale} });
                } catch (e) {
                    console.log(e);
                    router.push({ path: "/" });
                }
            }

            return { t, locale, supportedLocales,  switchlanguage};
        }
}
</script>

<style scoped>
    select {
        background: inherit;
    }
    select option {
        background-color: rgb(157, 125, 175);
    }
</style>