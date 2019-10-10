import {format} from 'date-fns';
import tenantTitleTypes from '../../../mixins/methods/tenantTitleTypes';

export default {
    tenants({tenants: {data = []}}) {
        return data.map(tenant => {
            tenant.name = `${tenant.first_name} ${tenant.last_name}`;
            tenant.birth_date = format(new Date(tenant.birth_date), 'DD.MM.YYYY');
            tenant.user_email = tenant.user.email;

            if (tenant.building && tenant.building.address) {
                tenant.building_address_row = `${tenant.building.address.street} ${tenant.building.address.house_num}`;
                tenant.building_address_zip = `${tenant.building.address.zip} ${tenant.building.address.city}`;
            }

            tenant.building_names = tenant.contracts.map(item => item.building && item.address ? { 
                            row : item.address.street + " " + item.address.house_num ,  
                            zip : item.address.zip + " " + item.address.city  } : { row : '', zip : ''} )
            tenant.unit_names = tenant.contracts.map(item => item.unit ? item.unit.name : '')


            return tenant;
        });
    },
    tenantsMeta({tenants}) {
        return _.omit(tenants, 'data');
    },
    countries ({countries}) {
        return countries;
    }
}
