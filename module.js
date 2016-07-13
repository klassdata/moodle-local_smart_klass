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

M.local_smart_klass.refreshContent = function (Y, url) {
   window.top.location = url;
}

M.local_smart_klass.createContent = function (Y, content, target) {
    var docElem = document.getElementById(target);
    var iframe = document.createElement('iframe');
    iframe.width = '100%';
    iframe.minHeight = 400 + 'px';
    iframe.height = 1700 + 'px';
    iframe.scrolling = 'yes';
    iframe.frameBorder  = 0;

    docElem.appendChild(iframe);
    // iFrameResize ({checkOrigin:false,heightCalculationMethod:'documentElementScroll'});
    if (iframe.contentWindow){
        iframe = iframe.contentWindow;
    }else{
        if (iframe.contentDocument && iframe.contentDocument.document){
            iframe = iframe.contentDocument.document;
        }else{
            iframe = iframe.contentDocument;
        }
    }
    iframe.document.open();
    iframe.document.write(content);
    iframe.document.close();
}

M.local_smart_klass.loadContent = function (Y, url, target) {
    var docElem = document.getElementById(target);
    var iframe = document.createElement('iframe');
    iframe.width = '100%';
    iframe.minHeight = 400 + 'px';
    iframe.height = 1500 + 'px';
    iframe.scrolling = 'yes'; // previo 'no'
    iframe.frameBorder  = 0;
    iframe.src = url;

    docElem.appendChild(iframe);
    // iFrameResize ({checkOrigin:false,heightCalculationMethod:'documentElementScroll'});
    if (iframe.contentWindow){
        iframe = iframe.contentWindow;
    }else{
        if (iframe.contentDocument && iframe.contentDocument.document){
            iframe = iframe.contentDocument.document;
        }else{
            iframe = iframe.contentDocument;
        }
    }
}
