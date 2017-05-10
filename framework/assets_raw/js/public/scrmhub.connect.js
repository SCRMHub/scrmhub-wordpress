/*!
 * SCRM Hub Connect library
 * http://scrmhub.com/
 *
 * Copyright 2015 SCRM Hub Pty Ltd
 * http://scrmhub.com/
 */
(function($) {
    "use strict"; 

    var scrmhubHookConnect = function(thisTarget) {
            if(!thisTarget) {
                thisTarget = document;
            }

            //find all instances
            $(thisTarget).find('a.scrmhub-button-connect').each(function() {
                //Check we haven't checked it already
                if($(this).data('scrmhub-connect-bound') === undefined) {
                    //Add the click event
                    $(this).on('click', function(e) {
                        doConnect(e, this);
                    }).data('scrmhub-connect-bound', true); //Stop re-binding
                }
            });
        },
        doConnect = function(e, thisObject) {
            //Stop default. We'll handle it from here.
            e.preventDefault();

            //Mobile does a redirect
            if (window.scrmhub.isMobile) {
                document.location.href = $(thisObject).attr('href') + "&referrer=" + encodeURIComponent(window.location.href);
            //Desktop does a popup
            } else {
                window.scrmhub.utils.openWindow($(thisObject).attr('href'), $(thisObject));
            }
        };

    var scrmhubHookLogout = function(thisTarget) {
        if(!thisTarget) {
            thisTarget = document;
        }

    	$(thisTarget).find('a.scrmhub-logout').unbind().on('click', function() {
	        
	    });
    };

    //Connect complete functions
    var windowConnectComplete = function() {
    	var connectSuccess = window.scrmhub.connect_complete.success,
            connectSuccessStatus = false;

        if(connectSuccess === true) {
            connectSuccessStatus = true;
        }

        /**
         * The callback function
         * @return {[type]} [description]
         */
        var loginComplete = function() {


            if(window.scrmhub.connect_complete.parent_function) {
                //Parent Window function
                try{
                    window.opener[window.scrmhub.connect_complete.parent_function](window.scrmhub.connect_complete.success, window.scrmhub.connect_complete.message);
                } catch(err) {
                    window.console.log('Unable to call the parent window function: ', window.scrmhub.connect_complete.parent_function);
                }
            } else if(window.scrmhub.connect_complete.redirect) {
                if(connectSuccess) {
                    window.opener.location.href = window.scrmhub.connect_complete.redirect;
                }
            //Reload the opener as a last resort
            } else {
                window.opener.location.reload(true);
            }

            window.close();
            window.top.close();  
        };

        //Mobile will always use the redirect value
        if(window.scrmhub.isMobile) {
            document.location.href = window.scrmhub.connect_complete.redirect;
        } else {
            //Setup the timer
            window.loginComplete = setTimeout(function() {
                loginComplete();
            }, 1000); 
        }  
    };

    /**
     * 
     */
    var connectStatus = null,
        checkingConnectStatus = false,
        getConnectStatus = function() {
            return connectStatus;
        },
        setConnectStatus = function(status) {
            connectStatus = status;
            window.scrmhub.callback.run('connect.statuschanged');
        },
        checkConnectStatus = function() {
            if(!checkingConnectStatus) {
                checkingConnectStatus = true; 
                $.post(window.scrmhub.ajaxurl, {action: 'scrmhub_connected'}, function(response) {
                    checkingConnectStatus = false;
                    checkedConnectStatus(response);
                });
            }
        },
        checkedConnectStatus = function(response) {
            setConnectStatus(response.connected);
            isConnected();
            if(response.uuid && response.uuid !== '') {
                window.scrmhub.clientdata.tokenset(response.uuid);
            }
        },
        isConnected = function() {
            var connectStatus = getConnectStatus();
            if(connectStatus === true || connectStatus === 'true') {
                $('.scrmhub-connect-ajax-panel .scrmhub-connected').show().siblings().remove();
            } else {
                $('.scrmhub-connect-ajax-panel .scrmhub-loggedout').show().siblings().remove();
            }
        };

    var bindAll = function(thisTarget) {
            if(!thisTarget) {
                thisTarget = document;
            }
            scrmhubHookConnect(thisTarget);
            scrmhubHookLogout(thisTarget);
        };

    //Initialise
    (function() {
        setConnectStatus(window.scrmhub.clientdata.get('scrmhub_connected'));
    })();

    //Function ready
    var ready = function() {
    	isConnected();

    	if(typeof(window.scrmhub.connect_complete) !== 'undefined') {
    		windowConnectComplete();
    	}

    	if(getConnectStatus() === null) {
            checkConnectStatus();
	    } else {
	        isConnected();
	    }
    };

    window.scrmhub.connect = {
    	ready:      ready,
        hook:       scrmhubHookConnect,
        connect:    getConnectStatus,
        bind:       bindAll
    };
})(jQuery);

//Global callback
window.scrmhub_login_callback = function(success) {
    if(success) {
        //Get the Wordpress redirect
        var redirect = jQuery("#loginform").find("input[name=\"redirect_to\"]").val();

        //And redirect
        window.location.href = redirect;
    }
};