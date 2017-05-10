/*!
 * Analytics Tracking library
 * http://scrmhub.com/
 *
 * Copyright 2015 SCRM Hub Pty Ltd
 * http://scrmhub.com/
 */
(function($) {
    "use strict";
    /*
     * Custom values for the request
     */
    var trackingValues      = {
            'refid'         : '',
            'hash'          : '',
            'referrer'      : '',
            'refdata'       : {},
            'utm_source'    : '',
            'utm_medium'    : '',
            'utm_term'      : '',
            'utm_content'   : '',
            'utm_campaign'  : '',
            'id'            : ''
        },
        allowerQueryStrings = ['id', 'p', 'tag', 'cat'],
        queryString         = null;      



    var checkForQueryData = function() {
            //Split it out
            var params          = window.location.search.substring(1).split('&');

            //Loop through
            for(var i in params){
                var pair = params[i].split('=');

                if(pair[0] === 'ref_a') {
                    trackingValues.refid = pair[1];
                } else if(pair[0] === 'hash') {
                    trackingValues.refdata.hash = pair[1];
                } else if(pair[0] === 'refdata') {
                    trackingValues.refdata.refdata = pair[1];
                }

                if(typeof(trackingValues[pair[0]]) !== 'undefined') {
                    trackingValues[pair[0]] = pair[1];
                }

                //A valid value to add back in
                for(var index in allowerQueryStrings) {
                    if(pair[0] === allowerQueryStrings[index]) {
                        queryString += (queryString !== '' ? '&' : '?') + pair[0] + '=' + pair[1];
                        continue;
                    }
                }
            }

            //Nothing specific so use this instead
            if(trackingValues.referrer === '' && document.referrer !== '') {
                trackingValues.referrer = document.referrer;
            }

            //Return the query string
            return queryString;
        };

    //Track the page load
    var pageLoadTrack = function() {
            //And track
            track_view({
                id:             getPageUrl()
                //target      :   getPageUrl(),
                //id          : (window.scrmhub.identifier ? window.scrmhub.identifier : null)
            });
        };    
        
    //Track a social action
    var track_social = function(args) {
            var trackArgs = extend( {
                    'gaqMethod'     : 'track_social',
                    'type'          : 'social',
                    'target'        : null,
                    'useraction'    : null,
                    'id'            : null,
                    'referrer'      : getPageUrl(),
                    'refdata'       : null
                }, args);

            doTrack(trackArgs);
        },

        //Track a page view
        track_view = function(args) {
            var trackArgs = extend( {                
                'gaqMethod'     : 'track_view',
                'type'          : 'page',
                'useraction'    : 'view',
                'target'        : '',
                'referrer'      : document.referrer,
                'id'            : null
            }, args);

            doTrack(trackArgs);
        },

        //Track a social action
        track_event = function(args) {
            var trackArgs = extend({
                'gaqMethod'     : 'track_event',
                'type'          : 'event',
                'target'        : null,
                'useraction'    : null,
                'id'            : null,
                'referrer'      : getPageUrl()
            }, args);

            doTrack(trackArgs);
        },

        //Track a download action
        track_download = function(args) {
            var trackArgs = extend( {
                'gaqMethod'     : 'track_download',
                'type'          : 'file',
                'target'        : null,
                'useraction'    : 'download',
                'id'            : null,
                'referrer'      : getPageUrl()
            }, args);

            doTrack(trackArgs);
        },

        //Track an outbound action
        track_outbound = function(args) {
            var trackArgs = extend( {
                'gaqMethod'     : 'track_outbound',
                'type'          : 'outbound',
                'target'        : null,
                'useraction'    : null,
                'id'            : null,
                'referrer'      : getPageUrl()
            }, args);

            doTrack(trackArgs);
        };

    /**
     * Get the page URL
     */
    var getPageUrl = function() {
        return window.scrmhub.pagePathUrl;
            // var url = window.location.href,
            //     protomatch = /^(https?|ftp):\/\//;

            // return url.replace(protomatch, '');
        };

    var doTrack = function(doArgs) {
            //No tracking enabled
            if(!window.scrmhub.analytics.enabled) {
                return;
            }

            //built in tracking
            sendRequest(doArgs);
        };

    //Tidy up the tracking values
    var getTrackingValues = function() {
            var thisTrackingValues = {};
            for(var i in trackingValues) {
                if(trackingValues[i] !== '') {
                    thisTrackingValues[i] = trackingValues[i];
                }
            }
            return thisTrackingValues;
        };

    //build the request
    var sendRequest = function(args) {
            //Append arguments and any custom data
            var sendData = {
                type        : '',
                id          : '',
                useraction  : '',
                target      : '',
                action      : 'create',
                appkey      : window.scrmhub.appkey,
                response    : true,
                agent       : navigator.userAgent,
                referrer    : document.referrer,
                refdata     : '',
                refid       : ''
            };

            //Merge it
            sendData = arraymerge(sendData, getTrackingValues(), args);

            //Add client token
            sendData.puuid = window.scrmhub.clientdata.token();
            if(sendData.puuid === false) {
                sendData.createpuuid    = true;
            }

            //Send an ajax request
            $.ajax({
                url     : window.scrmhub.apiUrl + 'activity/',
                data    : sendData,
                method  : 'POST'
            }).done(function(response) {
                if(response.puuid) {
                    window.scrmhub.clientdata.tokenset(response.puuid);
                }
            });
        };

    var arraymerge = function() {
        var arrays = arguments,
            arraysLength = arguments.length,
            arrayReturn = arrays[0];

        if(arrays.length === 1) {
            return arrayReturn;
        }

        //The mergining magic
        var merge = function(array1, array2) {
            //remove anything empty
            for(var index in array1) {
                if(array2[index]) {
                    array1[index] = array2[index];
                    
                    delete array2[index];
                }
            }

            //Anything left in array2?
            for(var index2 in array2) {
                array1[index2] = array2[index2];
            }

            //Return the new array
            return array1;
        };

        //Loop through the arrays
        for (var i=1; i < arraysLength; i++) {
            //Make a copy of the original
            var arrayToMerge = arrayClone(arrays[i]);

            //What we get back
            arrayReturn = merge(arrayReturn, arrayToMerge);
        }

        //Return the array
        return arrayReturn;
    };

    /*
     * Clone an array into a new object
     */
    var arrayClone = function(arrayToClone) {
        var newArray = {};

        for(var i in arrayToClone) {
            newArray[i] = arrayToClone[i];
        }

        //Return the copy
        return newArray;
    };

    /*
     * Replicating Jquery's .extend() function
     */
    var extend = function(base, args) {
        //loop through the items
        for(var i in args) {
            base[i] = args[i];
        }

        //send it back
        return base;
    };    

    /**
     * Plugin Ready
     */
    var ready = function() {
            //Get the token
            var clientToken = window.scrmhub.clientdata.token();

            //No token so add a callback
            if(!clientToken || clientToken === '') {
                window.scrmhub.callback.register('clientdata.tokenset', function() {
                    window.scrmhub.analytics.ready();
                });
            } else {
                pageLoadTrack(); //analytics handler
            }     
        };

    var checkPreviewMode = function() {
        return location.search.indexOf('preview=') >= 0;
    };

    /*
     * Initialise
     */
    (function() {
        //Don't even bother
        if(window.scrmhub.isbot) {
            window.scrmhub.analytics.enabled = false;
            return false;
        }

        if(checkPreviewMode()) {
            window.scrmhub.analytics.enabled = false;
            return false;
        }

        if(window.scrmhub.analytics.enabled === false || typeof(window.scrmhub.appkey) === undefined || window.scrmhub.appkey === '') {
            return false;
        }

        //Resolution information
        trackingValues.refdata.viewport = {
            width : window.innerWidth,
            height : window.innerHeight
        };
        trackingValues.refdata.screen   = {
            width : screen.width,
            height : screen.height,
            colour : screen.colorDepth
        };

        //Anything we need to use?
        checkForQueryData();  
    })();

    //public functions
    var analyticsFunctions  =  {
            ready           : ready,
            pageurl         : getPageUrl,
            track_social    : track_social,
            track_view      : track_view,
            track_event     : track_event,
            track_download  : track_download,
            track_outbound  : track_outbound
        };

    //Extend analytics declaration as it (should) already exist
    window.scrmhub.analytics = window.scrmhub.utils.extend(window.scrmhub.analytics, analyticsFunctions);
})(jQuery);