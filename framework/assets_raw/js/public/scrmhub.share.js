/*!
 * SCRM Hub Share library
 * http://scrmhub.com/
 *
 * Copyright 2015 SCRM Hub Pty Ltd
 * http://scrmhub.com/
 */
(function($) {
    "use strict";

    var self = false;

     
    var bindShareButtons = function(thisTarget) {
            if(!thisTarget) {
                thisTarget = document;
            }

            //Make connect and sharing open betterer
            $(thisTarget).find('a.scrmhub-button-share').each(function() {
                //Check we haven't checked it already
                if($(this).data('scrmhub-share-bound') === undefined) {
                    //Add the click event
                    $(this).on('click', function(e) {
                        doShare(e, this);
                    });
                }
                setupButton(this);
            }).data('scrmhub-share-bound', true); //Stop re-binding
        },
        doShare = function(e, thisObject) {
            e.preventDefault();

    		var thisHref = $(thisObject).attr('href');   

            window.scrmhub.utils.openWindow(thisHref, thisObject);
        };

    var ready = function() {
        //Nothing to do
    };

    var setupButton = function(thisObject) {
        if($(thisObject).attr('target') === '_blank') {
            $(thisObject).on('click', function(e) {
                doShare(e, this);
            });
        }
    };

    //Initialise
    (function() {
        self = window.scrmhub.share;
    })();

    window.scrmhub.share = {
        ready           : ready,
        bind            : bindShareButtons
    };
})(jQuery);