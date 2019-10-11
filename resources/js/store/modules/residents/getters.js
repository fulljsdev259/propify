import {format} from 'date-fns';
import residentTitleTypes from '../../../mixins/methods/residentTitleTypes';

export default {
    residents({residents: {data = []}}) {
        return data.map(resident => {
            resident.name = `${resident.first_name} ${resident.last_name}`;
            resident.birth_date = format(new Date(resident.birth_date), 'DD.MM.YYYY');
            resident.user_email = resident.user.email;

            if (resident.building && resident.building.address) {
                resident.building_address_row = `${resident.building.address.street} ${resident.building.address.house_num}`;
                resident.building_address_zip = `${resident.building.address.zip} ${resident.building.address.city}`;
            }

            resident.building_names = resident.contracts.map(item => item.building && item.address ? { 
                            row : item.address.street + " " + item.address.house_num ,  
                            zip : item.address.zip + " " + item.address.city  } : { row : '', zip : ''} )
            resident.unit_names = resident.contracts.map(item => item.unit ? item.unit.name : '')


            return resident;
        });
    },
    residentsMeta({residents}) {
        return _.omit(residents, 'data');
    },
    countries ({countries}) {
        return countries;
    }
}
