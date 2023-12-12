<template >
    <div class="wrapOfPopup">
        <div v-if="!ModalDialogForSureDelete">
            <div v-if="PersonPage === 'Index'" class="ms-4">
                <span class="cursor-pointer" @click="editUser">
                    <font-awesome-icon @click="editUser" icon="fa-pen" size="xl"/> 
                    <span class="ms-2">
                        {{ $t('additional.Edit_profile') }}
                    </span>
                </span>
            </div>
            <div v-if="PersonPage === 'Edit'" class="ms-4">
                <span class="cursor-pointer" @click="WatchUser">
                    <font-awesome-icon icon="fa-eye" size="xl"/> 
                    <span class="ms-2">
                        {{ $t('additional.Profile_view') }}
                    </span>
                </span>
            </div>
            <div class="mt-4 ms-4">
                <span class="cursor-pointer" @click="ModalForDeleteUser">
                    <font-awesome-icon icon="fa-trash" size="xl"/> 
                    <span class="ms-2">
                        {{ $t('additional.Delete_profile') }}
                    </span>
                </span>
            </div>
        </div>
        <div v-if="ModalDialogForSureDelete">
            <div>
                {{ $t('additional.Are_you_sure_that_you_want_to_delete_your_profile') }}
            </div>
            <div class="d-flex justify-content-center mt-3">
                <span @click="DeleteUser" class="cursor-pointer w-25 ms-1 me-4 yesNo">
                    {{ $t('additional.Yes') }}
                </span>
                <span @click="ModalDialogForSureDelete = false" class="cursor-pointer w-25 yesNo">
                    {{ $t('additional.No') }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {

    props: {
        PersonPage: {
            type: String
        }
    },

    emits: ['change'],

    data() {
        return {
            dialogVisible: false,
            ModalDialogForSureDelete: false
        }
    },

    methods: {
        editUser() {
            this.$emit('change', 'Edit')
        },
        WatchUser() {
            this.$emit('change', 'Index')
        },
        ModalForDeleteUser() {
            this.ModalDialogForSureDelete = true
        },
        DeleteUser() {
            axios.delete('/api/users/deleteAuthUser')
                .then ( r => {
                    localStorage.removeItem('x_xsrf_token');
                    this.$router.push( this.$Tr.i18nroute({ name: 'user.login'}));
                }
            )
        },
    },
}
</script>

<style scoped>

    .yesNo {
        background-color: rgb(236, 201, 158);
        border-radius: 7px;
        text-align: center;
        line-height: 28px;
    }

    .cursor-pointer {
        cursor: pointer;
    }
    .wrapOfPopup{
        min-height: 120px;
        display: flex;
        align-items: center;
    }


</style>