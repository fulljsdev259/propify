import {format} from 'date-fns';

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
    latestResidents({residents}, getters, { application: { constants } }) {
        return residents.data.map(resident => {
            resident.name = `${resident.first_name} ${resident.last_name}`;
            resident.status_label = `models.resident.status.${constants.residents.status[resident.status]}`;
            resident.address = ''
            if(resident.contracts.length)
                resident.address = resident.contracts[0].address['street'] + ' ' + resident.contracts[0].address['house_num']; // @TODO : this address should be checked once the api is done
            resident.status_class_suffix = constants.residents.status[resident.status];

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
