var ASSETS_PATH = (ASSETS_PATH || '').replace(/^\/|\/$/g, '');

if (ASSETS_PATH.charAt(0) !== '/' && ASSETS_PATH.length > 1) {
    ASSETS_PATH = '/' + ASSETS_PATH;
}

function loadJs(script, callback) {
	script = script || '';
    var pos = script.indexOf(ASSETS_PATH);
    if (pos !== -1) {
        script = script.substr(ASSETS_PATH.length);
    }

    script = '/' + (script.replace(/^\/|\/$/g, ''));
    jQuery.getScript(ASSETS_PATH + script, callback);
}