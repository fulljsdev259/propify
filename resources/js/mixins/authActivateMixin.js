import {mapActions, mapState} from 'vuex';
import {displaySuccess, displayError} from 'helpers/messages';
import axios from '@/axios';


// TODO make a common mixin for pinboard and listings mixins(media upload at least)
export default  {
    data() {
        return {
            model: {
                email: '',
                act_code: '',
                password: '',
                password_confirmation: '',
                terms: false,
            },
            validationRules: {
                email: [{
                    required: true,
                    message: this.$t("general.email_validation.required")
                }, {
                    type: 'email',
                    message: this.$t("general.email_validation.email")
                }],
                act_code: [{
                    required: true,
                    message: this.$t("general.activate_code_required")
                }],
                password: [{
                    required: true,
                    message: this.$t("general.password_validation.required")
                }, {
                    validator: this.validatePassword,
                }],
                password_confirmation: [{
                    required: true,
                    message: this.$t("general.password_validation.required")
                },{
                    validator: this.validateConfirmPassword,
                }],
                terms: [{
                    trigger: 'blue',
                    validator: this.termValidator
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
        })
    },
    mounted() {
        if(this.$route.query.code){
            this.model.act_code = this.$route.query.code;
        }  
    },
    methods: {
        submit() {
            
            this.$refs.form.validate(async valid => {
                if (valid) {
                    try {
                        const resp = await axios.post(`residents/activateResident?code=` + this.model.act_code +
                            `&email=` + this.model.email + `&password=` + this.model.password);                            
                        displaySuccess(resp.data);
                    } catch (err) {
                        displayError(err);
                    }
                }
            });
        },
        validatePassword(rule, value, callback) {
            let validateObject = this.model;

            if (value === '' && validateObject.password_confirmation) {
                callback(new Error($t("general.password_validation.required")))
            } else {
                this.$refs.form.validateField('password_confirmation');
                callback();
            }

        },
        validateConfirmPassword(rule, value, callback) {
            let validateObject = this.model;

            if (value && !validateObject.password) {
                this.$refs.form.validateField('password');
            } else if (value !== validateObject.password) {
                callback(new Error($t("general.password_validation.match")));
            } else {
                callback();
            }
        },
        termValidator(rule, value, callback) {
            if (!value) {
                callback(new Error(this.$t('validation.terms.required')));
            } else {
                callback();
            }
        },
    },
   
};
