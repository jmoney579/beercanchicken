
if (document.all) {
    document.onkeydown = MicrosoftEventHandler_KeyDown;
}
else {
    window.captureEvents(Event.KEYDOWN);
    window.onkeydown = NetscapeEventHandler_KeyDown;
}

function NetscapeEventHandler_KeyDown(e) {
    if (myDeflt.length > 0) {
        if (e.which == 13 && e.target.type != 'textarea' && e.target.type != 'submit') {
            __doPostBack(myDeflt, '');
        }
    }
    return true;
}

function MicrosoftEventHandler_KeyDown() {
    if (myDeflt.length > 0) {
        if (event.keyCode == 13 && event.srcElement.type != 'textarea' && event.srcElement.type != 'submit') {
            __doPostBack(myDeflt, '');
            return false;
        }
    }
    return true;
}