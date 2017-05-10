!function(e){var t="object"==typeof window&&window||"object"==typeof self&&self;"undefined"!=typeof exports?e(exports):t&&(t.hljs=e({}),"function"==typeof define&&define.amd&&define([],function(){return t.hljs}))}(function(e){function t(e){return e.replace(/[&<>]/gm,function(e){return A[e]})}function n(e){return e.nodeName.toLowerCase()}function r(e,t){var n=e&&e.exec(t);return n&&0===n.index}function i(e){return M.test(e)}function a(e){var t,n,r,a,c=e.className+" ";if(c+=e.parentNode?e.parentNode.className:"",n=_.exec(c))return N(n[1])?n[1]:"no-highlight";for(c=c.split(/\s+/),t=0,r=c.length;r>t;t++)if(a=c[t],i(a)||N(a))return a}function c(e,t){var n,r={};for(n in e)r[n]=e[n];if(t)for(n in t)r[n]=t[n];return r}function o(e){var t=[];return function e(r,i){for(var a=r.firstChild;a;a=a.nextSibling)3===a.nodeType?i+=a.nodeValue.length:1===a.nodeType&&(t.push({event:"start",offset:i,node:a}),i=e(a,i),n(a).match(/br|hr|img|input/)||t.push({event:"stop",offset:i,node:a}));return i}(e,0),t}function s(e,r,i){function a(){return e.length&&r.length?e[0].offset!==r[0].offset?e[0].offset<r[0].offset?e:r:"start"===r[0].event?e:r:e.length?e:r}function c(e){function r(e){return" "+e.nodeName+'="'+t(e.value)+'"'}l+="<"+n(e)+k.map.call(e.attributes,r).join("")+">"}function o(e){l+="</"+n(e)+">"}function s(e){("start"===e.event?c:o)(e.node)}for(var u=0,l="",b=[];e.length||r.length;){var f=a();if(l+=t(i.substr(u,f[0].offset-u)),u=f[0].offset,f===e){b.reverse().forEach(o);do{s(f.splice(0,1)[0]),f=a()}while(f===e&&f.length&&f[0].offset===u);b.reverse().forEach(c)}else"start"===f[0].event?b.push(f[0].node):b.pop(),s(f.splice(0,1)[0])}return l+t(i.substr(u))}function u(e){function t(e){return e&&e.source||e}function n(n,r){return new RegExp(t(n),"m"+(e.cI?"i":"")+(r?"g":""))}function r(i,a){if(!i.compiled){if(i.compiled=!0,i.k=i.k||i.bK,i.k){var o={},s=function(t,n){e.cI&&(n=n.toLowerCase()),n.split(" ").forEach(function(e){var n=e.split("|");o[n[0]]=[t,n[1]?Number(n[1]):1]})};"string"==typeof i.k?s("keyword",i.k):y(i.k).forEach(function(e){s(e,i.k[e])}),i.k=o}i.lR=n(i.l||/\w+/,!0),a&&(i.bK&&(i.b="\\b("+i.bK.split(" ").join("|")+")\\b"),i.b||(i.b=/\B|\b/),i.bR=n(i.b),i.e||i.eW||(i.e=/\B|\b/),i.e&&(i.eR=n(i.e)),i.tE=t(i.e)||"",i.eW&&a.tE&&(i.tE+=(i.e?"|":"")+a.tE)),i.i&&(i.iR=n(i.i)),null==i.r&&(i.r=1),i.c||(i.c=[]);var u=[];i.c.forEach(function(e){e.v?e.v.forEach(function(t){u.push(c(e,t))}):u.push("self"===e?i:e)}),i.c=u,i.c.forEach(function(e){r(e,i)}),i.starts&&r(i.starts,a);var l=i.c.map(function(e){return e.bK?"\\.?("+e.b+")\\.?":e.b}).concat([i.tE,i.i]).map(t).filter(Boolean);i.t=l.length?n(l.join("|"),!0):{exec:function(){return null}}}}r(e)}function l(e,n,i,a){function c(e,t){var n,i;for(n=0,i=t.c.length;i>n;n++)if(r(t.c[n].bR,e))return t.c[n]}function o(e,t){if(r(e.eR,t)){for(;e.endsParent&&e.parent;)e=e.parent;return e}return e.eW?o(e.parent,t):void 0}function s(e,t){return!i&&r(t.iR,e)}function f(e,t){var n=v.cI?t[0].toLowerCase():t[0];return e.k.hasOwnProperty(n)&&e.k[n]}function d(e,t,n,r){var i=r?"":B.classPrefix,a='<span class="'+i,c=n?"":R;return(a+=e+'">')+t+c}function p(){var e,n,r,i;if(!y.k)return t(_);for(i="",n=0,y.lR.lastIndex=0,r=y.lR.exec(_);r;)i+=t(_.substr(n,r.index-n)),e=f(y,r),e?(C+=e[1],i+=d(e[0],t(r[0]))):i+=t(r[0]),n=y.lR.lastIndex,r=y.lR.exec(_);return i+t(_.substr(n))}function h(){var e="string"==typeof y.sL;if(e&&!x[y.sL])return t(_);var n=e?l(y.sL,_,!0,E[y.sL]):b(_,y.sL.length?y.sL:void 0);return y.r>0&&(C+=n.r),e&&(E[y.sL]=n.top),d(n.language,n.value,!1,!0)}function m(){M+=null!=y.sL?h():p(),_=""}function g(e){M+=e.cN?d(e.cN,"",!0):"",y=Object.create(e,{parent:{value:y}})}function w(e,t){if(_+=e,null==t)return m(),0;var n=c(t,y);if(n)return n.skip?_+=t:(n.eB&&(_+=t),m(),n.rB||n.eB||(_=t)),g(n,t),n.rB?0:t.length;var r=o(y,t);if(r){var i=y;i.skip?_+=t:(i.rE||i.eE||(_+=t),m(),i.eE&&(_=t));do{y.cN&&(M+=R),y.skip||(C+=y.r),y=y.parent}while(y!==r.parent);return r.starts&&g(r.starts,""),i.rE?0:t.length}if(s(t,y))throw new Error('Illegal lexeme "'+t+'" for mode "'+(y.cN||"<unnamed>")+'"');return _+=t,t.length||1}var v=N(e);if(!v)throw new Error('Unknown language: "'+e+'"');u(v);var k,y=a||v,E={},M="";for(k=y;k!==v;k=k.parent)k.cN&&(M=d(k.cN,"",!0)+M);var _="",C=0;try{for(var A,S,L=0;y.t.lastIndex=L,A=y.t.exec(n);)S=w(n.substr(L,A.index-L),A[0]),L=A.index+S;for(w(n.substr(L)),k=y;k.parent;k=k.parent)k.cN&&(M+=R);return{r:C,value:M,language:e,top:y}}catch(e){if(e.message&&-1!==e.message.indexOf("Illegal"))return{r:0,value:t(n)};throw e}}function b(e,n){n=n||B.languages||y(x);var r={r:0,value:t(e)},i=r;return n.filter(N).forEach(function(t){var n=l(t,e,!1);n.language=t,n.r>i.r&&(i=n),n.r>r.r&&(i=r,r=n)}),i.language&&(r.second_best=i),r}function f(e){return B.tabReplace||B.useBR?e.replace(C,function(e,t){return B.useBR&&"\n"===e?"<br>":B.tabReplace?t.replace(/\t/g,B.tabReplace):void 0}):e}function d(e,t,n){var r=t?E[t]:n,i=[e.trim()];return e.match(/\bhljs\b/)||i.push("hljs"),-1===e.indexOf(r)&&i.push(r),i.join(" ").trim()}function p(e){var t,n,r,c,u,p=a(e);i(p)||(B.useBR?(t=document.createElementNS("http://www.w3.org/1999/xhtml","div"),t.innerHTML=e.innerHTML.replace(/\n/g,"").replace(/<br[ \/]*>/g,"\n")):t=e,u=t.textContent,r=p?l(p,u,!0):b(u),n=o(t),n.length&&(c=document.createElementNS("http://www.w3.org/1999/xhtml","div"),c.innerHTML=r.value,r.value=s(n,o(c),u)),r.value=f(r.value),e.innerHTML=r.value,e.className=d(e.className,p,r.language),e.result={language:r.language,re:r.r},r.second_best&&(e.second_best={language:r.second_best.language,re:r.second_best.r}))}function h(e){B=c(B,e)}function m(){if(!m.called){m.called=!0;var e=document.querySelectorAll("pre code");k.forEach.call(e,p)}}function g(){addEventListener("DOMContentLoaded",m,!1),addEventListener("load",m,!1)}function w(t,n){var r=x[t]=n(e);r.aliases&&r.aliases.forEach(function(e){E[e]=t})}function v(){return y(x)}function N(e){return e=(e||"").toLowerCase(),x[e]||x[E[e]]}var k=[],y=Object.keys,x={},E={},M=/^(no-?highlight|plain|text)$/i,_=/\blang(?:uage)?-([\w-]+)\b/i,C=/((^(<[^>]+>|\t|)+|(?:\n)))/gm,R="</span>",B={classPrefix:"hljs-",tabReplace:null,useBR:!1,languages:void 0},A={"&":"&amp;","<":"&lt;",">":"&gt;"};return e.highlight=l,e.highlightAuto=b,e.fixMarkup=f,e.highlightBlock=p,e.configure=h,e.initHighlighting=m,e.initHighlightingOnLoad=g,e.registerLanguage=w,e.listLanguages=v,e.getLanguage=N,e.inherit=c,e.IR="[a-zA-Z]\\w*",e.UIR="[a-zA-Z_]\\w*",e.NR="\\b\\d+(\\.\\d+)?",e.CNR="(-?)(\\b0[xX][a-fA-F0-9]+|(\\b\\d+(\\.\\d*)?|\\.\\d+)([eE][-+]?\\d+)?)",e.BNR="\\b(0b[01]+)",e.RSR="!|!=|!==|%|%=|&|&&|&=|\\*|\\*=|\\+|\\+=|,|-|-=|/=|/|:|;|<<|<<=|<=|<|===|==|=|>>>=|>>=|>=|>>>|>>|>|\\?|\\[|\\{|\\(|\\^|\\^=|\\||\\|=|\\|\\||~",e.BE={b:"\\\\[\\s\\S]",r:0},e.ASM={cN:"string",b:"'",e:"'",i:"\\n",c:[e.BE]},e.QSM={cN:"string",b:'"',e:'"',i:"\\n",c:[e.BE]},e.PWM={b:/\b(a|an|the|are|I'm|isn't|don't|doesn't|won't|but|just|should|pretty|simply|enough|gonna|going|wtf|so|such|will|you|your|like)\b/},e.C=function(t,n,r){var i=e.inherit({cN:"comment",b:t,e:n,c:[]},r||{});return i.c.push(e.PWM),i.c.push({cN:"doctag",b:"(?:TODO|FIXME|NOTE|BUG|XXX):",r:0}),i},e.CLCM=e.C("//","$"),e.CBCM=e.C("/\\*","\\*/"),e.HCM=e.C("#","$"),e.NM={cN:"number",b:e.NR,r:0},e.CNM={cN:"number",b:e.CNR,r:0},e.BNM={cN:"number",b:e.BNR,r:0},e.CSSNM={cN:"number",b:e.NR+"(%|em|ex|ch|rem|vw|vh|vmin|vmax|cm|mm|in|pt|pc|px|deg|grad|rad|turn|s|ms|Hz|kHz|dpi|dpcm|dppx)?",r:0},e.RM={cN:"regexp",b:/\//,e:/\/[gimuy]*/,i:/\n/,c:[e.BE,{b:/\[/,e:/\]/,r:0,c:[e.BE]}]},e.TM={cN:"title",b:e.IR,r:0},e.UTM={cN:"title",b:e.UIR,r:0},e.METHOD_GUARD={b:"\\.\\s*"+e.UIR,r:0},e}),hljs.registerLanguage("php",function(e){var t={b:"\\$+[a-zA-Z_-ÿ][a-zA-Z0-9_-ÿ]*"},n={cN:"meta",b:/<\?(php)?|\?>/},r={cN:"string",c:[e.BE,n],v:[{b:'b"',e:'"'},{b:"b'",e:"'"},e.inherit(e.ASM,{i:null}),e.inherit(e.QSM,{i:null})]},i={v:[e.BNM,e.CNM]};return{aliases:["php3","php4","php5","php6"],cI:!0,k:"and include_once list abstract global private echo interface as static endswitch array null if endwhile or const for endforeach self var while isset public protected exit foreach throw elseif include __FILE__ empty require_once do xor return parent clone use __CLASS__ __LINE__ else break print eval new catch __METHOD__ case exception default die require __FUNCTION__ enddeclare final try switch continue endfor endif declare unset true false trait goto instanceof insteadof __DIR__ __NAMESPACE__ yield finally",c:[e.HCM,e.C("//","$",{c:[n]}),e.C("/\\*","\\*/",{c:[{cN:"doctag",b:"@[A-Za-z]+"}]}),e.C("__halt_compiler.+?;",!1,{eW:!0,k:"__halt_compiler",l:e.UIR}),{cN:"string",b:/<<<['"]?\w+['"]?$/,e:/^\w+;?$/,c:[e.BE,{cN:"subst",v:[{b:/\$\w+/},{b:/\{\$/,e:/\}/}]}]},n,{cN:"keyword",b:/\$this\b/},t,{b:/(::|->)+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/},{cN:"function",bK:"function",e:/[;{]/,eE:!0,i:"\\$|\\[|%",c:[e.UTM,{cN:"params",b:"\\(",e:"\\)",c:["self",t,e.CBCM,r,i]}]},{cN:"class",bK:"class interface",e:"{",eE:!0,i:/[:\(\$"]/,c:[{bK:"extends implements"},e.UTM]},{bK:"namespace",e:";",i:/[\.']/,c:[e.UTM]},{bK:"use",e:";",c:[e.UTM]},{b:"=>"},r,i]}}),hljs.registerLanguage("json",function(e){var t={literal:"true false null"},n=[e.QSM,e.CNM],r={e:",",eW:!0,eE:!0,c:n,k:t},i={b:"{",e:"}",c:[{cN:"attr",b:/"/,e:/"/,c:[e.BE],i:"\\n"},e.inherit(r,{b:/:/})],i:"\\S"},a={b:"\\[",e:"\\]",c:[e.inherit(r)],i:"\\S"};return n.splice(n.length,0,i,a),{c:n,k:t,i:"\\S"}}),hljs.registerLanguage("css",function(e){var t="[a-zA-Z-][a-zA-Z0-9_-]*",n={b:/[A-Z\_\.\-]+\s*:/,rB:!0,e:";",eW:!0,c:[{cN:"attribute",b:/\S/,e:":",eE:!0,starts:{eW:!0,eE:!0,c:[{b:/[\w-]+\(/,rB:!0,c:[{cN:"built_in",b:/[\w-]+/},{b:/\(/,e:/\)/,c:[e.ASM,e.QSM]}]},e.CSSNM,e.QSM,e.ASM,e.CBCM,{cN:"number",b:"#[0-9A-Fa-f]+"},{cN:"meta",b:"!important"}]}}]};return{cI:!0,i:/[=\/|'\$]/,c:[e.CBCM,{cN:"selector-id",b:/#[A-Za-z0-9_-]+/},{cN:"selector-class",b:/\.[A-Za-z0-9_-]+/},{cN:"selector-attr",b:/\[/,e:/\]/,i:"$"},{cN:"selector-pseudo",b:/:(:)?[a-zA-Z0-9\_\-\+\(\)"'.]+/},{b:"@(font-face|page)",l:"[a-z-]+",k:"font-face page"},{b:"@",e:"[{;]",i:/:/,c:[{cN:"keyword",b:/\w+/},{b:/\s/,eW:!0,eE:!0,r:0,c:[e.ASM,e.QSM,e.CSSNM]}]},{cN:"selector-tag",b:"[a-zA-Z-][a-zA-Z0-9_-]*",r:0},{b:"{",e:"}",i:/\S/,c:[e.CBCM,n]}]}}),hljs.registerLanguage("xml",function(e){var t="[A-Za-z0-9\\._:-]+",n={eW:!0,i:/</,r:0,c:[{cN:"attr",b:"[A-Za-z0-9\\._:-]+",r:0},{b:/=\s*/,r:0,c:[{cN:"string",endsParent:!0,v:[{b:/"/,e:/"/},{b:/'/,e:/'/},{b:/[^\s"'=<>`]+/}]}]}]};return{aliases:["html","xhtml","rss","atom","xjb","xsd","xsl","plist"],cI:!0,c:[{cN:"meta",b:"<!DOCTYPE",e:">",r:10,c:[{b:"\\[",e:"\\]"}]},e.C("<!--","-->",{r:10}),{b:"<\\!\\[CDATA\\[",e:"\\]\\]>",r:10},{b:/<\?(php)?/,e:/\?>/,sL:"php",c:[{b:"/\\*",e:"\\*/",skip:!0}]},{cN:"tag",b:"<style(?=\\s|>|$)",e:">",k:{name:"style"},c:[n],starts:{e:"</style>",rE:!0,sL:["css","xml"]}},{cN:"tag",b:"<script(?=\\s|>|$)",e:">",k:{name:"script"},c:[n],starts:{e:"</script>",rE:!0,sL:["actionscript","javascript","handlebars","xml"]}},{cN:"meta",v:[{b:/<\?xml/,e:/\?>/,r:10},{b:/<\?\w+/,e:/\?>/}]},{cN:"tag",b:"</?",e:"/?>",c:[{cN:"name",b:/[^\/><\s]+/,r:0},n]}]}}),hljs.registerLanguage("markdown",function(e){return{aliases:["md","mkdown","mkd"],c:[{cN:"section",v:[{b:"^#{1,6}",e:"$"},{b:"^.+?\\n[=-]{2,}$"}]},{b:"<",e:">",sL:"xml",r:0},{cN:"bullet",b:"^([*+-]|(\\d+\\.))\\s+"},{cN:"strong",b:"[*_]{2}.+?[*_]{2}"},{cN:"emphasis",v:[{b:"\\*.+?\\*"},{b:"_.+?_",r:0}]},{cN:"quote",b:"^>\\s+",e:"$"},{cN:"code",v:[{b:"^```w*s*$",e:"^```s*$"},{b:"`.+?`"},{b:"^( {4}|\t)",e:"$",r:0}]},{b:"^[-\\*]{3,}",e:"$"},{b:"\\[.+?\\][\\(\\[].*?[\\)\\]]",rB:!0,c:[{cN:"string",b:"\\[",e:"\\]",eB:!0,rE:!0,r:0},{cN:"link",b:"\\]\\(",e:"\\)",eB:!0,eE:!0},{cN:"symbol",b:"\\]\\[",e:"\\]",eB:!0,eE:!0}],r:10},{b:/^\[[^\n]+\]:/,rB:!0,c:[{cN:"symbol",b:/\[/,e:/\]/,eB:!0,eE:!0},{cN:"link",b:/:\s*/,e:/$/,eB:!0}]}]}}),hljs.registerLanguage("javascript",function(e){var t="[A-Za-z$_][0-9A-Za-z$_]*",n={keyword:"in of if for while finally var new function do return void else break catch instanceof with throw case default try this switch continue typeof delete let yield const export super debugger as async await static import from as",literal:"true false null undefined NaN Infinity",built_in:"eval isFinite isNaN parseFloat parseInt decodeURI decodeURIComponent encodeURI encodeURIComponent escape unescape Object Function Boolean Error EvalError InternalError RangeError ReferenceError StopIteration SyntaxError TypeError URIError Number Math Date String RegExp Array Float32Array Float64Array Int16Array Int32Array Int8Array Uint16Array Uint32Array Uint8Array Uint8ClampedArray ArrayBuffer DataView JSON Intl arguments require module console window document Symbol Set Map WeakSet WeakMap Proxy Reflect Promise"},r={cN:"number",v:[{b:"\\b(0[bB][01]+)"},{b:"\\b(0[oO][0-7]+)"},{b:e.CNR}],r:0},i={cN:"subst",b:"\\$\\{",e:"\\}",k:n,c:[]},a={cN:"string",b:"`",e:"`",c:[e.BE,i]};i.c=[e.ASM,e.QSM,a,r,e.RM];var c=i.c.concat([e.CBCM,e.CLCM]);return{aliases:["js","jsx"],k:n,c:[{cN:"meta",r:10,b:/^\s*['"]use (strict|asm)['"]/},{cN:"meta",b:/^#!/,e:/$/},e.ASM,e.QSM,a,e.CLCM,e.CBCM,r,{b:/[{,]\s*/,r:0,c:[{b:t+"\\s*:",rB:!0,r:0,c:[{cN:"attr",b:t,r:0}]}]},{b:"("+e.RSR+"|\\b(case|return|throw)\\b)\\s*",k:"return throw case",c:[e.CLCM,e.CBCM,e.RM,{cN:"function",b:"(\\(.*?\\)|"+t+")\\s*=>",rB:!0,e:"\\s*=>",c:[{cN:"params",v:[{b:t},{b:/\(\s*\)/},{b:/\(/,e:/\)/,eB:!0,eE:!0,k:n,c:c}]}]},{b:/</,e:/(\/\w+|\w+\/)>/,sL:"xml",c:[{b:/<\w+\s*\/>/,skip:!0},{b:/<\w+/,e:/(\/\w+|\w+\/)>/,skip:!0,c:[{b:/<\w+\s*\/>/,skip:!0},"self"]}]}],r:0},{cN:"function",bK:"function",e:/\{/,eE:!0,c:[e.inherit(e.TM,{b:t}),{cN:"params",b:/\(/,e:/\)/,eB:!0,eE:!0,c:c}],i:/\[|%/},{b:/\$[(.]/},e.METHOD_GUARD,{cN:"class",bK:"class",e:/[{;=]/,eE:!0,i:/[:"\[\]]/,c:[{bK:"extends"},e.UTM]},{bK:"constructor",e:/\{/,eE:!0}],i:/#(?!!)/}}),void 0===window.scrmhub&&(window.scrmhub={}),window.scrmhub.utils=function($){"use strict";var e=null,t=function(){if(null===e){var t=/bot|googlebot|crawler|spider|robot|crawling|aolbuild|baidu|bing|msn|duckduckgo|teoma|slurp|yandex/i,n=navigator.userAgent.toLowerCase();e=t.test(n)}return e},n=function(){var e=!1;return function(t){(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(t)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(t.substr(0,4)))&&(e=!0)}(navigator.userAgent||navigator.vendor||window.opera),e},r=function(e,t,n,r){n||(n=1025),r||(r=560);var i=screen.width/2-n/2,a=screen.height/2-r/2;return window.scrmhub.popupWindow=window.open(e,"scrmloginWindow","toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width="+n+",height="+r+",left="+i+",top="+a),window.scrmhub.popupWindow.sourceObject=t,window.scrmhub.popupWindow},i=function(e){e||(e=document),$(e).find(".button-close-window").each(function(){void 0===$(this).data("scrmhub-close-bound")&&$(this).on("click",function(e){a(e)}).data("scrmhub-close-bound",!0)})},a=function(e){e.preventDefault(),window.close(),window.top.close()},c=function(e){e=e.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]");var t=new RegExp("[\\?&]"+e+"=([^&#]*)"),n=t.exec(location.search);return null===n?"":decodeURIComponent(n[1].replace(/\+/g," "))},o=function(e){e||(e=document),window.scrmhub.share.bind(e),window.scrmhub.connect.bind(e),i()},s=function(e,t){for(var n in t)e[n]=t[n];return e};!function(){window.scrmhub.isbot=t(),window.scrmhub.isMobile=n()}();var u=function(){o(document)};return window.scrmhub.bind=o,{bind:o,extend:s,ready:u,openWindow:r,closeWindow:a,getParameterByName:c}}(jQuery),window.scrmhub.admin=function($){"use strict";var e=function(e,t){"1"===t||!0===t||"true"===t?$(e).show():"0"!==t&&!1!==t&&"false"!==t||$(e).hide()},t=function(e,t){var n=$(e).find(".panel-"+t);$(e).children().hide(),n.length>0&&n.show()};return{setup:function(){$(".scrmhubpanel").each(function(){$(this).change(function(){var t=$(this).data("scrmhubtarget"),n=$(this).val();e(t,n)}),e($(this).data("scrmhubtarget"),$(this).val())}),$(".scrmhubpanelvalue").each(function(){$(this).change(function(){var e=$(this).data("scrmhubpanelvalue"),n=$(this).val();t(e,n)}),t($(this).data("scrmhubpanelvalue"),$(this).val())}),$(".scrmhub-checkbox-list").each(function(){var e=$(this);e.find("a.scrmhub-select-all").on("click",function(){e.find('input[type="checkbox"]').each(function(){$(this).attr("checked","checked")})})}),$(".scrmhub-network-update").submit(function(){return window.confirm("This will update the settings for all sites in this network. Are you sure you want to do this?")})},showhide:e}}(jQuery),jQuery(document).ready(function(){"use strict";window.scrmhub.admin.setup(),jQuery("pre code").each(function(e,t){hljs.highlightBlock(t)})});