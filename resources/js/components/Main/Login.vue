<template>
    <CenterAlignment>
        <vueTitle :title="$t('titles.Login')"></vueTitle>
            <h2>{{ $t("additional.Enter your personal account")}}</h2>
                <template v-for="index in this.inputsLength" :key="index">
                    <div v-if="index == 1">
                        <input 
                            @keyup.enter.prevent="login()" 
                            @keydown="(e) => keydown(e)" 
                            @keyup="(e) => keyup(e, index)" 
                            type="text" 
                            v-focus 
                            v-model="inputs[index-1].model" 
                            :placeholder ="inputs[index-1].placeholder" 
                            class = "form-control mb-2 w-75" 
                            :ref="index" 
                        >
                    </div>
                    <div v-else>
                        <input 
                            @keyup.enter.prevent="login()"
                            @keydown="(e) => keydown(e)" 
                            @keyup="(e) => keyup(e, index)" 
                            :type="inputs[index-1].type"
                            v-model="inputs[index-1].model" 
                            :placeholder ="inputs[index-1].placeholder" 
                            class = "form-control mb-2 w-75" 
                            :ref="index" 
                        >
                    </div>
                </template>
            <ButtonSend  :defineFunction="login"></ButtonSend>
    </CenterAlignment>
</template>

<script>
import InputKeyUpKeyDown from '@/Mixins/InputKeyUpKeyDown'

export default {
    name: "Login",

    mixins: [InputKeyUpKeyDown],

    data() {
        return {
            inputs: [
                { type: "text", model: null, placeholder: this.$t("additional.email")},
                { type: "password", model: null, placeholder: this.$t("additional.password")}
            ]
        }
    },

    methods: {
        login() {
            axios.get('/sanctum/csrf-cookie')
                .then(response => {
                    axios.post('/login', {email: this.inputs[0].model, password: this.inputs[1].model})
                        .then(response => {
                            localStorage.setItem('x_xsrf_token', response.config.headers['X-XSRF-TOKEN']);
                            this.$router.push( this.$Tr.i18nroute({ name: 'user.profile'}) );
                        })
                        .catch(err=>{
                            console.log(err);
                        })
                })
        }
    },

}
</script>
