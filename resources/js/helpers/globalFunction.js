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
        },
        getLightenDarkenColor(col, amt) {
  
            var usePound = false;
          
            if (col[0] == "#") {
                col = col.slice(1);
                usePound = true;
            }
         
            var num = parseInt(col,16);
         
            var r = (num >> 16) + amt;
         
            if (r > 255) r = 255;
            else if  (r < 0) r = 0;
         
            var b = ((num >> 8) & 0x00FF) + amt;
         
            if (b > 255) b = 255;
            else if  (b < 0) b = 0;
         
            var g = (num & 0x0000FF) + amt;
         
            if (g > 255) g = 255;
            else if (g < 0) g = 0;
         
            return (usePound?"#":"") + (g | (b << 8) | (r << 16)).toString(16);
          
        }
    } 
 }

export default globalFunction;
