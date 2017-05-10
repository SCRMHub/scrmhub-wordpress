/**
 * Client Data functions
 */
window.scrmhub.clientdata = (function() {
    "use strict";

    //Variables
    var clientTokenName         = 'scrmhub_uuid',
        supportStorage          = true, //assumer the browser supports this
        safeCookies             = ['scrmhub'], //This is all the platform specific cookies for functionality
        commondomain            = false;

    /*
     * Functions to help with the token puuid handling
     */
    var token = function() {
            //Do we have a token
            return window.scrmhub.tokenTwo || false;
        },
        tokenSet = function(newtoken) {
            //Yeah, no...
            if(window.scrmhub.isbot === true) {
                return;
            }

            //Verify the format of the token
            if(!verifyValidUUID(newtoken)) {
                return;
            }

            //store the values
            window.scrmhub.tokenTwo = newtoken;

            //store it for later
            set(clientTokenName, newtoken, 365, 'all');

            //Any callbacks?
            window.scrmhub.callback.run('clientdata.tokenset');
        };

    //Get a value
    var get = function(name, type) {
            var value   = false; //What gets returned

            //tidy
            type    = (!type ? 'all' : type);
            name    = safeName(name);

            //Get it from local storage first
            if(type === 'local' || type === 'all') {
                value = getStorageValue(name);
            }

            //Get it from a cookie
            if((type === 'cookie' || type === 'all') && !value) {
                value = getCookie(name);
            }

            //Return the value
            return value;
        },

        //Set a value
        set = function(name, value, lifetime, type) {
            if(window.scrmhub.isbot === true) {
                return;
            }

            var expires = calculateExpires(lifetime);

            type    = (!type ? 'all' : type);
            name    = safeName(name);

            //If it's a long term cookie, check it's allowed
            if(lifetime > 0 && doNotTrackCheck(name)) {
                return;
            }

            //Fall back to cookies
            if('type' === 'local' && !supportStorage) {
                type = 'cookie';
            }

            //Set the local value
            if(type !== 'cookie') {
                setStorageValue(name, value, lifetime, expires);
            }

            //Set the cookie
            if(type !== 'local') {
                setCookie(name, value, lifetime, expires);
            }            
        },

        //Delete a value
        remove = function(name, type) {
            type    = (!type ? 'all' : type);
            name    = safeName(name);

            //Cookie only
            if(type === 'all' || type === 'cookie') {
                removeCookie(name);
            }

            //Get it from local
            if(type === 'all' || type === 'local') {
                removeStorageValue(name);
            }
        },

        //Timeframe
        timeframe = function(name, lifetime) {
            //Check for versions
            var CookieVersion   = getCookie(name),
                DataVersion     = getStorageValue(name, true),
                DataValue       = false,
                value           = false;

            if(DataVersion.value) {
                DataValue = DataVersion.value;
            }

            if(CookieVersion === false && DataVersion !== false) {
                value = DataValue;

            } else if(DataVersion === false && CookieVersion !== false) {
                value = CookieVersion;

            //Check the expiry
            } else if(DataVersion !== false) {
                if(DataVersion.expiresIn < (lifetime/2)) {
                    value = DataValue;
                }
            }

            //Got a value so update
            if(value) {
                set(name, value, lifetime);
            }
        };

    /*
     * Tidy the name for consistency
     */
    var doNotTrackCheck = function(name) {
            //Enabled
            if(navigator.doNotTrack || navigator.msDoNotTrack) {
                for (var i = 0; i < safeCookies.length; i++) {
                    if (safeCookies[i] === name) {
                        return false;
                    }
                }
                //Ok to track
                return true;
            }

            //Not enabled so ok to track
            return false;
        },
        safeName = function(name) {
            name = name.replace(/[^a-z0-9]/gi, "_");

            //Add in the base token name
            if(name.indexOf('_') > -1) {
                name = name;
            }

            return name;
        },
        calculateExpires = function(lifetime) {
            var expires = new Date();

            //Session based
            if(lifetime === 0) {
                return expires;
            }

            //Out new date
            expires.setDate(expires.getDate() + lifetime);

            //Return the UTC version
            return expires.toUTCString();
        },
        //Verify a uuid is in a valid format
        verifyValidUUID = function(value) {
            return /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/.test(value);
        };

    /*
     * Cookie Methods
     */
    var readCookie = function (name) {
            var c = document.cookie.split('; '),
                cookies = {};

            for(var i=c.length-1; i>=0; i--){
               var C = c[i].split('=');
               cookies[C[0]] = C[1];
            }

            if(name) {
               return cookies[name];
            }
            return cookies;
        },
        getCookie = function(name) {
            if(!readCookie(name)) {
                return null;
            } else {
                return readCookie(name);
            }
        },
        removeCookie = function(name) {
            var date = new Date();
                date.setTime(date.getTime()+(-1*24*60*60*1000));
            var expires = ";expires="+date.toGMTString();

            if(commondomain) {
                expires += "; domain=" + commondomain;
            }

            document.cookie = encodeURI(name)+"="+expires+"; path=/";
        },
        setCookie = function(name, value, lifetime, expires) {
            removeCookie(name);

            var c_value=encodeURI(value);

            if(lifetime !== null && lifetime !== 0) {
                c_value += c_value + "; expires=" + expires;
            }
            c_value = c_value + "; path=/";

            if(commondomain) {
                c_value += "; domain=" + commondomain;
            }

            //SSL?
            if (location.protocol === 'https:') {
                c_value += "; secure";
            }

            //Try and set the cookie
            document.cookie = encodeURI(name) + "=" + c_value;
        };



    /**
     *Browser storage methods
     **/
    var getStorageValue = function(name, returnData) {
            if(!supportStorage) {
                return null;
            }

            try {
                if (window.sessionStorage.getItem(name)) {
                    return window.sessionStorage.getItem(name);

                //Use local storage and check there's no cookie already
                } else if (window.localStorage.getItem(name)) {
                    //Get the value
                    var
                        data    = window.localStorage.getItem(name),
                        value   = null,
                        expiresIn, theAge, lifetime;

                    //Nothing found
                    if(!data) {
                        return null;
                    }

                    //Check the format
                    try {
                        //check the expiry on the item and update if required
                        data = JSON.parse(data);
                    } catch(err) {
                        //Failed so delete it
                        removeStorageValue(name);
                        return null;
                    }

                    //old structure so convert to new look data
                    if(!data.updated) {
                        value = data;

                        //Reset the value. Assume 1 month
                        set(name, value, 30);
                    } else {
                        //check the age of it
                        var today   = new Date(),
                            updated = new Date(data.updated),
                            expires = new Date(data.expires);

                        //It expired so honor removing it (good practice)
                        if(today > expires) {
                            removeStorageValue(name, 'local');
                        } else {
                            expiresIn   = (expires.getTime() - today.getTime()) / (1000*60*60*24);
                            theAge      = (today.getTime() - updated.getTime()) / (1000*60*60*24);
                            lifetime    = Math.ceil((expires.getTime() - updated.getTime()) / (1000*60*60*24));

                            //Get the value
                            value = data.value;
                        }
                    }                

                    //Do we want the full object?
                    if(returnData) {
                        var returnObject = {
                            'age'       : theAge,
                            'expiresIn' : expiresIn,
                            'value'     : value,
                            'expires'   : calculateExpires(lifetime)
                        };
                        return returnObject;
                    }

                    return value;
                }
            } catch(err) {}
            return null;
        },
        removeStorageValue = function(name) {
            if(!supportStorage) {
                return;
            }

            try {
                //Session storage
                if (window.sessionStorage.getItem(name)) {
                    window.sessionStorage.removeItem(name);

                //long term storage
                } else if (window.localStorage.getItem(name)) {
                    window.localStorage.removeItem(name);
                }
            } catch(err) {}
        },
        setStorageValue = function(name, value, lifetime, expires) {
            if(!supportStorage) {
                return;
            }

            try {            
                if(lifetime > 0) {
                    //Date and data
                    var updated = new Date(),
                        data = {
                            'updated'   : updated.toUTCString(), //calculateExpires(-2),
                            'value'     : value,
                            'expires'   : expires
                        };

                    //Check no cookie
                    window.localStorage.setItem(name, JSON.stringify(data));
                } else {
                    window.sessionStorage.setItem(name, value);
                }
            } catch(err) {}
        };

    //initialise
    (function() {    
        //Older browser so disable this
        supportStorage = (typeof(Storage) !== 'undefined');

        //Do we have a commondomain?
        if(typeof(window.scrmhub.commondomain) !== undefined && window.scrmhub.commondomain !== '') {
            commondomain = window.scrmhub.commondomain;
        }

        //Set the token
        if(window.scrmhub.isbot) {
            window.scrmhub.tokenTwo = null;

        } else {
            if(typeof(window.scrmhub.tokenTwo) !== undefined && window.scrmhub.tokenTwo) {
                tokenSet(window.scrmhub.tokenTwo);
            } else {
                window.scrmhub.tokenTwo = get(clientTokenName);
            }
        }        
    })();

    var ready = function() {
        //return the token
        return token;
    };

    //Expose what we want
    return {
        "ready"         : ready,
        "get"           : get,
        "set"           : set,
        "remove"        : remove,
        "checkInstances": timeframe,
        "token"         : token,
        "tokenset"      : tokenSet
    };
})();