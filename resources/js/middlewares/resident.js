import {checkTenancy} from "helpers/checkTenancy";

export default function resident(ctx) {
    const {next, router, loggedInUser} = ctx;
    const isResident = checkTenancy(loggedInUser.roles);

    if (isResident) {
        return next();
    }

    if(loggedInUser.roles[0].name == 'service') {
        return router.push({name: 'adminRequests'});
    }

    return router.push({name: 'adminDashboard'});
}
