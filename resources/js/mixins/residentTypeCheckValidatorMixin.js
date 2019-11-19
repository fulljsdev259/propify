import axios from '@/axios';
export default (config = {}) => {
    const {model = 'model'} = config;
    return {
        methods: {
            async checkavailabilityResidentType(rule, value, callback) {
                let validateObject = this[model];
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
                    if(validateObject.type != '' && validateObject.relations.length > 0) {
                        if(this.original_type != validateObject.type)
                            callback(new Error(this.$t('models.resident.relation.type_validation_error')));
                    }
                        
                }
            }
        }
    }  
};