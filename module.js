/**
 * @namespace
 */
M.local_smart_klass = M.local_smart_klass || {
    api: M.cfg.wwwroot+'/local/smart_klass/ajax.php',
};

M.local_smart_klass.save_access_token = function (Y, code, refresh, email, rol, user_id) {
    var send_data = {
        code: code,
        refresh: refresh,
        email: email,
        rol: rol,
        user_id: user_id
    }; 
    
    Y.io(M.local_smart_klass.api, {
        method : 'POST',
        data : build_querystring({
            action: 'save_access_token',
            data: Y.JSON.stringify(send_data)            
        }),
        on : {
            success : M.local_smart_klass.save_access_token_callback
        },
        context : M.local_smart_klass
    });
    
}


M.local_smart_klass.save_access_token_callback =  function(tid, outcome) {
    try {
        var success = outcome.success;
        var data = Y.JSON.parse(outcome.data);
    } catch (ex) {
        return;
    }    
}

