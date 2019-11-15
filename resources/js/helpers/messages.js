import Vue from 'boot';

export const errorMessage = (defaultMessage, status, err = {}) => {
    let message = {
        message: defaultMessage,
        success: false
    };    
    if (status == 422) {        
        message.message = 'validation_error';
        message.status = 422;
        message.error = err;
    }

    else if (status == 404) {
        message.message = defaultMessage;
    }

    else if (status == 401) {
        const {$store} = Vue;

        $store.dispatch("forceLogout");
    }
    else if (status != 401) {
        message.message = 'server_error';
    }
    return message;
};

export const displayError = (err) => {
    const {$swal, $i18n, $route} = Vue;

    const isAdmin = $route.path.includes('/admin');
    // console.error(err);

    if (err && err.message) {
        if (err.status && err.error) {
            _.each(err.error.response.data.errors, (errorObj) => {
                if (_.isArray(errorObj)) {
                    _.each(errorObj, (er) => {
                        if (isAdmin) {
                            Vue.$snotify.error($i18n.t(er));                            
                        }
                        else {
                            $swal({
                                toast: true,
                                position: 'bottom-end',
                                showConfirmButton: false,
                                timer: 3000,
                                type: 'error',
                                width: 'auto',
                                title: $i18n.t(er)
                            });
                        }
                    })
                }
            });
        } else {            
            let msg = err.message;
            if (isAdmin) {
                msg = $i18n.t((typeof msg === 'string') ? msg : ((typeof msg === 'object') ? msg[Object.keys(msg)[0]][0] : 'general.server_error'));
                Vue.$snotify.error(msg);               
            }
            else {
                $swal({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 3000,
                    type: 'error',
                    /*width: 'auto',*/
                    title: $i18n.t((typeof msg === 'string') ? msg : ((typeof msg === 'object') ? msg[Object.keys(msg)[0]][0] : 'general.server_error'))
                });
            }
        }

    }
};

export const displaySuccess = (resp) => {
    if (resp && resp.message) {
        const {$i18n, $swal, $route} = Vue;        

        if ($route.path.includes('/admin')) {            
            Vue.$snotify.success(resp.message);
        }
        else {
            $swal({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                type: 'success',
                title: $i18n.t(resp.message)
            });
        }


        if (resp.redirect) {
            $router.push({name: resp.redirect});
        }
    }
};

export default {
    errorMessage
}
