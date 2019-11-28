import {format} from 'date-fns';

export default {
    residents({residents: {data = []}}) {
        return data.map(resident => {
            resident.name = `${resident.first_name} ${resident.last_name}`;
            resident.birth_date = format(new Date(resident.birth_date), 'DD.MM.YYYY');
            resident.user_email = resident.user.email;

            resident.building_names = resident.relations.map(item => item.address ? { 
                            row : item.address.street + " " + item.address.house_num ,  
                            zip : item.address.zip + " " + item.address.city  } : { row : '', zip : ''} )
            resident.unit_names = resident.relations.map(item => item.unit ? item.unit.name : '')

            return resident;
        });
    },
    latestResidents(data, getters, { application: { constants } }) {
        return data.latestResidents.map(resident => {
            resident.name = `${resident.first_name} ${resident.last_name}`;
            resident.status_label = `models.resident.status.${constants.residents.status[resident.status]}`;
            resident.address = ''
            if(resident.relations.length && resident.relations[0].address)
                resident.address = resident.relations[0].address['street'] + ' ' + resident.relations[0].address['house_num']; // @TODO : this address should be checked once the api is done
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
