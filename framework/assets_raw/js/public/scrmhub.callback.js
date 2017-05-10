/*
 * Callback handler
 */
window.scrmhub.callback = (function() {
    "use strict";

    //What callbacks we have registered
    var thisCallbacks = [];

        /*
         * Register a callback
         */

    var register = function(eventName, functionName, args) {
            //Tidy it up
            eventName = tidyEventName(eventName);

            //New element
            if(!thisCallbacks[eventName]) {
                thisCallbacks[eventName] = {
                    'shortlived' : [],
                    'fixed' : []
                };
            }

            //Push the new action in
            thisCallbacks[eventName].shortlived.push({
                callback    : functionName,
                data        : args
            });
        },
        registerSticky = function(eventName, functionName, args) {
            //Tidy it up
            eventName = tidyEventName(eventName);

            //New element
            if(!thisCallbacks[eventName]) {
                thisCallbacks[eventName] = {
                    'shortlived' : [],
                    'fixed' : []
                };
            }

            //Push the new action in
            thisCallbacks[eventName].fixed.push({
                callback    : functionName,
                data        : args
            });
        },

        /*
         * Run callbacks for the section
         */
        run = function(eventName, $args) {
            //Tidy it up
            eventName = tidyEventName(eventName);
            
            //No callbacks
            if(typeof(thisCallbacks) === undefined || typeof(thisCallbacks[eventName]) === undefined || !thisCallbacks[eventName] || thisCallbacks[eventName] === '') {
                return;
            }

            //Build the callbacks
            var callbacks = [];
            if(thisCallbacks[eventName].fixed) {
                callbacks = callbacks.concat(thisCallbacks[eventName].fixed);                
            }
            if(thisCallbacks[eventName].shortlived) {
                callbacks = callbacks.concat(thisCallbacks[eventName].shortlived);                
            }

            //Empty the short items
            thisCallbacks[eventName].shortlived = [];

            //No items
            if(callbacks.length === 0) {
                return;
            }

            //Loop
            for(var index in callbacks) {
                var thisCallback    = callbacks[index].callback,
                    thisCallbackData;

                if($args) {
                    thisCallbackData        = $args;
                } else {
                    thisCallbackData        = callbacks[index].data;
                }

                //No callback name
                if(!thisCallback) {
                    continue;
                }

                callbackRun(thisCallback, thisCallbackData);
            }
        },

        /*
         * Clear all callbacks
         */
        clear = function(eventName) {
            //Tidy it up
            eventName = tidyEventName(eventName);

            //Clear out all the short stay events
            if(eventName && thisCallbacks[eventName]) {
                thisCallbacks[eventName].shortlived = {};
            }           
        },

        /*
         * Execute a a callback and arguments (this allows an override)
         */
        callbackRun = function(thisCallback, thisData) {
            //An internal function
            if(typeof(thisCallback) === 'function') {
                thisCallback(thisData);

            } else if(typeof($.fn[thisCallback]) === 'function') {
                $.fn[thisCallback](thisData);

            //A global function
            } else if(typeof(window[thisCallback]) === 'function') {
                window[thisCallback](thisData);

            //It was a function
            }
        };

    /*
     *
     */
    function tidyEventName(eventName) {
        return eventName.replace(/[\s\.]/g, "_");
    }

    return {
        register        : register,
        registerSticky  : registerSticky,
        run             : run,
        clear           : clear
    };
})();