import axios from '@/axios';
export default (config = {}) => {
    const {model = 'model'} = config;
    return {
        methods: {
            async checkavailabilityResidentType(rule, value, callback) {
                console.log('checkavailabilityResidentType', validateObject)
                let validateObject = this[model];
                console.log('checkavailabilityResidentType', validateObject)
                {
                    try {
                        const resp = await axios.get('resident/get_residents__id__type?id=' + validateObject.id);                 
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