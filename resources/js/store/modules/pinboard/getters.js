export default {
    pinboard({pinboard: {data = []}}, getters, rootState) {
        // const {application: {constants: {pinboard: pinboardConstants}}} = rootState;

        // return data.map(pinboard => {
        //     pinboard.status_label = pinboardConstants.status[pinboard.status];
        //     pinboard.visibility_label = pinboardConstants.visibility[pinboard.visibility];
        //     pinboard.type_label = pinboardConstants.type[pinboard.type];
        //     pinboard.preview = `${pinboard.content.substring(0, 50)}...`;
        //     return pinboard;
        // });
        return data
    },
    latestPinboard({pinboard: {data = []}}, getters, rootState) {
        const {application: {constants: {pinboard: pinboardConstants}}} = rootState;

        return data.map(pinboard => {
            pinboard.status_label = `models.pinboard.status.${pinboardConstants.status[pinboard.type]}`;
            pinboard.image_url = pinboard.media.length == 0 ? '' : pinboard.media[0].url;
            return pinboard;
        });
    },
    pinboardMeta({pinboard}) {
        return _.omit(pinboard, 'data');
    }
}
