export default {
    methods: {
        PushToTheUserPage(id) {
            this.$router.push( this.$Tr.i18nroute({ name: 'user.profileOf', params: { locale: this.$Tr.currentLocale , id: id } }));
        }
    }
}