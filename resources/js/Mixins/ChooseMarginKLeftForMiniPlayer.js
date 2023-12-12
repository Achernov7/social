export default {

    data() {
        return {
            marginLeftForPlayer: '0px',
            defaultSettingsForRouteName: {
                'user.messages': {
                    'nameOfClass': 'messagewrap',
                    'multiplierOfInsideElement': 0.1,
                    'multiplierOfScreenWidth': 1.7,
                    'correctForWideMonitor': 100
                },
                'user.messageTo': {
                    'nameOfClass': 'backOfMessages',
                    'multiplierOfInsideElement': 0.1,
                    'multiplierOfScreenWidth': 1.7,
                    'correctForWideMonitor': 100
                },
                'user.groups': {
                    'nameOfClass': 'GroupWrap',
                    'multiplierOfInsideElement': 0.1,
                    'multiplierOfScreenWidth': 1.7,
                    'correctForWideMonitor': 100
                },
                'user.friends': {
                    'nameOfClass': 'OrangeBackForWindow',
                    'multiplierOfInsideElement': 0.1,
                    'multiplierOfScreenWidth': 0.7,
                    'correctForWideMonitor': 280
                },
                'user.profile': {
                    'nameOfClass': 'OrangeBack',
                    'multiplierOfInsideElement': 1,
                    'multiplierOfScreenWidth': 2,
                    'correctForWideMonitor': 120
                },
                'user.profileOf': {
                    'nameOfClass': 'OrangeBack',
                    'multiplierOfInsideElement': 1,
                    'multiplierOfScreenWidth': 2,
                    'correctForWideMonitor': 120
                },
                'get.news': {
                    'nameOfClass': 'wrapTape',
                    'multiplierOfInsideElement': 1,
                    'multiplierOfScreenWidth': 2,
                    'correctForWideMonitor': 120
                    
                },
                'user.gallery': {
                    'nameOfClass': 'wrapImages',
                    'multiplierOfInsideElement': 1,
                    'multiplierOfScreenWidth': 2,
                    'correctForWideMonitor': 120
                }
            }
        }
    },

    mounted() {
        setTimeout(() => {
            window.addEventListener('resize', () => {
                if (this.$route.name == 'user.music' || this.$route.name == 'user.login' || this.$route.name == 'user.registration' || this.$route.name == '404') return
                this.setWidth(this.defaultSettingsForRouteName[this.$route.name].nameOfClass, this.defaultSettingsForRouteName[this.$route.name].multiplierOfInsideElement, this.defaultSettingsForRouteName[this.$route.name].multiplierOfScreenWidth, this.defaultSettingsForRouteName[this.$route.name].correctForWideMonitor);
            })
        }, 1000);
    },

    methods: {
        setWidth(className, multiplierOfInsideElement, multiplierOfScreenWidth, correctForWideMonitor) {
            if (window.screen.width < 1340) {
                this.marginLeftForPlayer = '20%';
            } else if (window.screen.width > 2300) {
                setTimeout(() => {
                    var wrapWidth = document.getElementsByClassName(className)[0].offsetWidth
                    this.marginLeftForPlayer = `${((multiplierOfScreenWidth * window.screen.width - wrapWidth*multiplierOfInsideElement)/window.screen.width)*100 - correctForWideMonitor}px`;
                });
            } else {
                setTimeout(() => {
                    var wrapWidth = document.getElementsByClassName(className)[0].offsetWidth
                    this.marginLeftForPlayer = `${((multiplierOfScreenWidth * window.screen.width - wrapWidth*multiplierOfInsideElement)/window.screen.width)*100}px`;
                });
            }
        }
    },

    watch: {
        $route() {
            if(this.$route.name != 'user.music' && this.$route.name != 'user.login' && this.$route.name != 'user.registration' && this.$route.name != '404') {
                this.setWidth(this.defaultSettingsForRouteName[this.$route.name].nameOfClass, this.defaultSettingsForRouteName[this.$route.name].multiplierOfInsideElement, this.defaultSettingsForRouteName[this.$route.name].multiplierOfScreenWidth, this.defaultSettingsForRouteName[this.$route.name].correctForWideMonitor);
            }
        }
    },
}