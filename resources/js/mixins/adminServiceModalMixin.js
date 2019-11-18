import {mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';

export default (config = {}) => {
    let mixin = {
        data() {
            return {
                selectedServiceRequest: {id: ''},
                showServiceMailModal: false,
                mailSending: false
            };
        },
        methods: {
            ...mapActions(['sendServiceRequestMail']),
            serviceWasSelected(service) {
                const foundService = _.find(this.services, {id: service});

                this.setSelectedServiceRequest(foundService);
                this.showServiceMailModal = true;
            },
            setSelectedServiceRequest(service) {
                this.selectedServiceRequest = Object.assign({}, service);
            },

            closeMailModal(reset = true) {
                this.showServiceMailModal = false;
                if (reset) {
                    this.setSelectedServiceRequest({});
                }
            },
            async sendServiceMail(serviceAttachModel) {
                if (serviceAttachModel.provider) {
                    this.mailSending = true;

                    try {
                        if(typeof(serviceAttachModel.provider) == 'object') {
                            const resp = await this.sendServiceRequestMail({
                                request: this.model.id,
                                service_provider_id: serviceAttachModel.provider.edit_id,
                                title: serviceAttachModel.subject,
                                body: serviceAttachModel.body,
                                cc: serviceAttachModel.cc,
                                bcc: serviceAttachModel.bcc,
                                property_manager_id: serviceAttachModel.manager,
                                to: serviceAttachModel.to
                            });
                        }
                        else if(typeof(serviceAttachModel.provider) == 'number') {
                            const resp = await this.sendServiceRequestMail({
                                request: this.model.id,
                                service_provider_id: serviceAttachModel.provider,
                                title: serviceAttachModel.subject,
                                body: serviceAttachModel.body,
                                cc: serviceAttachModel.cc,
                                bcc: serviceAttachModel.bcc,
                                property_manager_id: serviceAttachModel.manager,
                                to: serviceAttachModel.to
                            });
                        }

                        if (resp) {
                            this.closeMailModal(false);
                            displaySuccess({
                                success: true,
                                message: this.$t('models.request.mail.success')
                            });
                        }

                    } catch (e) {                        
                        displayError(e)                        
                    } finally {
                        this.mailSending = false;
                    }
                }
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods
                };
                break;
            case 'edit':
                mixin.methods = {
                    ...mixin.methods
                };

                break;
        }
    }


    return mixin;
};


