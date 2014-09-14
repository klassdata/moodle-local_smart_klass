/**
 * @namespace
 */
M.local_klap = M.local_klap || {
    api: M.cfg.wwwroot+'/local/klap/ajax.php',
};

M.local_klap.save_access_token = function (Y, code, refresh, email, rol, user_id) {
    var send_data = {
        code: code,
        refresh: refresh,
        email: email,
        rol: rol,
        user_id: user_id
    }; 
    
    Y.io(M.local_klap.api, {
        method : 'POST',
        data : build_querystring({
            action: 'save_access_token',
            data: Y.JSON.stringify(send_data)            
        }),
        on : {
            success : M.local_klap.save_access_token_callback
        },
        context : M.local_klap
    });
    
}


M.local_klap.save_access_token_callback =  function(tid, outcome) {
    try {
        var success = outcome.success;
        var data = Y.JSON.parse(outcome.data);
    } catch (ex) {
        return;
    }    
}

