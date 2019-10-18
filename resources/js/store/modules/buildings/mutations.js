import { EventBus } from '../../../event-bus.js';
export default {
    SET_BUILDINGS(state, buildings) {
        state.buildings = buildings;
        if(buildings.data)
            EventBus.$emit('building-get-counted', buildings.total);
    }
}