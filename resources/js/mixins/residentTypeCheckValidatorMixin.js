import axios from '@/axios';
export default (config = {}) => {
    const {model = 'model'} = config;
    return {
        methods: {
            async checkavailabilityResidentType(rule, value, callback) {
                console.log('checkavailabilityResidentType', validateObject)
                let validateObject = this[model];
                {
                    try {
                        const resp = await axios.get(`/residents/${validateObject.id}/type`);                 
                    } catch(error) {
                        if(error.response.data.success == false) {
                            callback(new Error(error.response.data.message));
                        }
                    }
                }
            }
        }
    }  
};