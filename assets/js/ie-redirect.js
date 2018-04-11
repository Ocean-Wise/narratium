/**
 * This function checks which version of IE the user is using.
 * Returns 12 if it is another browser type
 *
 * @returns {int} rv The version of IE used by the user
 *
 */
function checkIE() {
    var rv = -1; // Return value assumes failure

    if (navigator.appName == 'Microsoft Internet Explorer') {
        var ua = navigator.userAgent,
            re = new RegExp("MSIE ([0-9]{1,}[\\.0-9]{0,})");

        if (re.exec(ua) !== null) {
            rv = parseFloat( RegExp.$1 );
        }
    } else if (navigator.appName == "Netscape") {
        // in IE 11 the navigator.appVersion says 'trident'
        // in Edge the navigator.appVersion does not
        if (navigator.appVersion.indexOf('Trident') === -1) rv = 12;
        else rv = 11;
    }

    return rv;
}

/**
 *
 * This function will redirect the user to
 * the same URL with the parameter browser=ie
 * which causes the Vanenti theme to be used instead
 *
 */
function shouldRedirect() {
    var version = checkIE();
    if (version < 12) {
        window.location = window.location.href + '?browser=ie';
    }
}

// Run the shouldRedirect function before content loads
shouldRedirect();
