import axios from '@/axios';
export default (config = {}) => {
    const {model = 'model'} = config;
    return {
        methods: {
            async checkavailabilityResidentType(rule, value, callback) {
                let validateObject = this[model];
                console.log('checkavailabilityResidentType', validateObject)
                if(validateObject.id)
                {
                    try {
                        const resp = await axios.get(`/residents/${validateObject.id}/type`);                 
                    } catch(error) {
                        if(error.response.data.success == false) {
                            callback(new Error(error.response.data.message));
                        }
                    }
                }
                else {
                    if(validateObject.type != '' && validateObject.contracts.length > 0)
                        callback(new Error("You can't change type as this resident has " + validateObject.contracts.length + " contracts"));
                }
            }
        }
    }  
};