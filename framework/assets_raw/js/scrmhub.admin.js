// @codekit-prepend "vendor/highlight/highlight.pack.js"
// @codekit-prepend "public/scrmhub.utils.js";
// @codekit-prepend "admin/core.js";

jQuery( document ).ready(function() {
    "use strict";

    window.scrmhub.admin.setup();

    jQuery('pre code').each(function(i, block) {
		hljs.highlightBlock(block);
	});
});