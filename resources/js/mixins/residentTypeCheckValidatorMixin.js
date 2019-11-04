import axios from '@/axios';
export default (config = {}) => {
    const {model = 'model'} = config;
    return {
        methods: {
            async checkavailabilityResidentType(rule, value, callback) {
                let validateObject = this[model];
                console.log('checkavailabilityResidentType', validateObject)
                console.log('original type', this.original_type, validateObject.type)
                if(validateObject.id)
                {
                    if(this.original_type !== validateObject.type) {
                        try {
                            const resp = await axios.get(`/residents/${validateObject.id}/type`, {type: validateObject.type});                 
                        } catch(error) {
                            if(error.response.data.success == false) {
                                callback(new Error(error.response.data.message));
                            }
                        }
                    }
                }
                else {
                    if(validateObject.type != '' && validateObject.contracts.length > 0) {
                        callback(new Error(this.$t('models.resident.contract.type_validation_error')));
                    }
                        
                }
            }
        }
    }  
};