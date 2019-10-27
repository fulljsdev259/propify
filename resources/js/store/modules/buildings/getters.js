export default {
    buildings({buildings: {data = []}}) {
        return data.map(building => {
            building.basement = building.basement ? 'Yes' : 'No';

            if (building.address) {
                building.address_row = `${building.address.street} ${building.address.house_num}`;
                building.address_zip = `${building.address.zip} ${building.address.city}`;
            }

            building.residents = building.contracts.map(contract => contract.resident)
            building.residentscount = building.residents.length > 2 ? (building.residents.length - 2) : 0;
            building.residents = building.residents.splice(0, 2);
            
            const managers = [...building.managers];
            building.managerscount = 0;
            if(managers.length) {
                building.managers = building.managers.splice(0, 2);
                if(managers.length > 2) {
                    building.managerscount = managers.length - 2;
                }
            }

            return building;
        });
    },
    latestBuildings({buildings: {data = []}}) {
        return data.map(building => {
            building.basement = building.basement ? 'Yes' : 'No';

            if (building.address) {
                building.address_row = `${building.address.street} ${building.address.house_num}`;
                building.address_zip = `${building.address.zip} ${building.address.city}`;
            }

            building.residents = building.contracts.map(contract => contract.resident)
            building.residentscount = building.residents.length > 2 ? (building.residents.length - 2) : 0;
            building.residents = building.residents.splice(0, 2);
            
            const managers = [...building.managers];
            building.managerscount = 0;
            if(managers.length) {
                building.managers = building.managers.splice(0, 2);
                if(managers.length > 2) {
                    building.managerscount = managers.length - 2;
                }
            }

            return building;
        });
    },
    buildingsMeta({buildings}) {
        return _.omit(buildings, 'data');
    },
}
