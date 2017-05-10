// @codekit-prepend "public/scrmhub.utils.js";
// @codekit-prepend "public/scrmhub.callback.js";
// @codekit-prepend "public/scrmhub.clientdata.js";
// @codekit-prepend "public/scrmhub.share.js";
// @codekit-prepend "public/scrmhub.connect.js";
// @codekit-prepend "public/scrmhub.analytics.js";

jQuery( document ).ready(function() {
    //setup the plugins    
    window.scrmhub.share.ready(); //share handler
    window.scrmhub.connect.ready(); //conenct handler
    window.scrmhub.analytics.ready(); //analytics handler
    window.scrmhub.utils.ready();
});