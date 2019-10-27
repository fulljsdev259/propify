export default {
    listings(state, getters, rootState) {
        return state.listings.data

        // const {application: {constants: {listings: listingConstants}}} = rootState;

        // const listings = state.listings.data ? state.listings.data : [];

        // return listings.map(listing => {
        //     listing.status_label = listingConstants.status[listing.status];
        //     listing.visibility_label = listingConstants.visibility[listing.visibility];
        //     listing.type_label = listingConstants.type[listing.type];
        //     return listing;
        // });
    },
    latestListings(state, getters, rootState) {
        const {application: {constants: {listings: listingConstants}}} = rootState;

        const listings = state.listings.data ? state.listings.data : [];

        return listings.map(listing => {
            listing.status_label = listingConstants.status[listing.status];
            listing.visibility_label = listingConstants.visibility[listing.visibility];
            listing.type_label = listingConstants.type[listing.type];
            listing.price = '$' + listing.price;
            listing.image_url = listing.media.length == 0 ? '' : listing.media[0].url;
            return listing;
        });
    },
    listingsMeta({listings}) {
        return _.omit(listings, 'data');
    }
}
