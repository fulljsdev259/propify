import {mapActions, mapState} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';

// TODO make a common mixin for pinboard and listings mixins(media upload at least)
export default  {
    data() {
        return {
            model: {
                email: '',
                password: ''
            },
            validationRules: {
                email: [{
                    required: true,
                    message: this.$t("general.email_validation.required")
                }, {
                    type: 'email',
                    message: this.$t("general.email_validation.email")
                }],
                password: [{
                    required: true,
                    message: this.$t("general.password_validation.required")
                }]
            }
        }
    },
    props: {
        
    },
    computed: {
        ...mapState({
            loggedInUser: ({users}) => {
                return users.loggedInUser;
            }
        }),
        
    },
    methods: {
        submit() {
            this.$refs.form.validate(async valid => {
                if (valid) {
                    try {
                        await this.login(this.model);
                        const {data: {settings: {language}}, ...rest} = await this.me();
                        if(!this.$i18n.locale){
                            this.$i18n.locale = language;
                        }                            
                        this.$router.push({
                            name: 'residentDashboard'
                        });

                        displaySuccess(rest);
                    } catch (err) {
                        displayError(err);
                    }
                }
            });
        },

        ...mapActions(['me', 'login']),
    },
};
