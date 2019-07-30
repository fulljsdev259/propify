let globalFunction = {
    methods:  {
        getRequestStatusColor(status, type='id') {            
            const colorByID = {
                1: '#bbb',
                2: '#ebb563',
                3: '#ebb563',
                4: '#67C23A',
                5: '#ebb563',
                6: '#67C23A'
            };
            const colorByName = {
                received: '#bbb',
                in_processing: '#ebb563',
                assigned: '#ebb563',
                done: '#67C23A',
                reactivated: '#ebb563',
                archived: '#67C23A'
            };
            if(type === 'name'){                
                return colorByName[status];
            }            
            return colorByID[status];
        },
        getUnitsCountColor(status, type='id') {            
            const colorByID = {
                1: '#bbb',
                2: '#ebb563',
                3: '#ebb563',
                4: '#67C23A',
                5: '#ebb563',
                6: '#67C23A'
            };
            const colorByName = {
                total_units: '#bbb',
                occupied_units: '#FFA400',
                free_units: '#F9690E',
                active: '#5fad64',
                not_active: '#dd6161'
            };
            if(type === 'name'){                
                return colorByName[status];
            }            
            return colorByID[status];
        }
    } 
 }

export default globalFunction;
