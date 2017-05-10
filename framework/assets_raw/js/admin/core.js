/*!
 * Analytics Tracking library
 * http://scrmhub.com/
 *
 * Copyright 2016 SCRM Hub Pty Ltd
 * http://scrmhub.com/
 */
window.scrmhub.admin = (function($) {
    "use strict";

    /**
     * Show or hide a panel
     */
    var showhide = function(thisTarget, thisState) {
    	if(thisState === '1' || thisState === true || thisState === 'true') {
    		$(thisTarget).show();
    	} else if(thisState === '0' || thisState === false  || thisState === 'false') {
    		$(thisTarget).hide();
    	}
    };

    /**
     * Show or hide a panel
     */
    var showhidevalue = function(thisWrapper, thisState) {
    	var thisTarget = $(thisWrapper).find('.panel-' + thisState);

    	$(thisWrapper).children().hide();

    	if(thisTarget.length > 0) {
    		thisTarget.show();
    	}
    };



    /**
     * Adds the hooks
     * @return {[type]} [description]
     */
    var setup = function() {
	    // Show and hide panels based on a select box
	    $('.scrmhubpanel').each(function() {
	    	$(this).change(function() {
	    		var thisTarget 	= $(this).data('scrmhubtarget'),
	    			thisValue 	= $(this).val();

	    		showhide(thisTarget, thisValue);
			});

	    	showhide($(this).data('scrmhubtarget'), $(this).val());
	    });

	    $('.scrmhubpanelvalue').each(function() {
	    	$(this).change(function() {
	    		var thisTarget 	= $(this).data('scrmhubpanelvalue'),
	    			thisValue 	= $(this).val();

	    		showhidevalue(thisTarget, thisValue);
			});

	    	showhidevalue($(this).data('scrmhubpanelvalue'), $(this).val());
	    });

	    //Select all for list of checkboxes
	    $('.scrmhub-checkbox-list').each(function() {
	        var thisObject = $(this);

	        thisObject.find('a.scrmhub-select-all').on('click', function() {
	            thisObject.find('input[type="checkbox"]').each(function() {
	                $(this).attr('checked', 'checked');
	            });
	        });
	    });

	    $('.scrmhub-network-update').submit(function() {
	        var c = window.confirm("This will update the settings for all sites in this network. Are you sure you want to do this?");
	        return c; //you can just return c because it will be true or false
	    });	    
    };

    return {
    	setup: 		setup,
    	showhide: 	showhide
    };
})(jQuery);