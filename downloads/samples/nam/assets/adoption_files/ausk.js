
$.extend({ // creates a pair of jQuery functions allowing for reading querystring params in javascript
    getUrlVars: function () {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    getUrlVar: function (name) {
        return $.getUrlVars()[name];
    }
});

function HandleOther(wid, chkOther) {
    if (wid.value.length > 0) {
        $(chkOther).prop("checked", true);
    }
    else {
        $(chkOther).prop("checked", false);
    }
}

function SetRadioButton(controlName) {
	var control = document.getElementById(controlName);
	control.checked = true;
}

function confirmation(){
	var closeWindow = confirm("If you cancel now, changes made to this point will not be saved. Are you sure you want to cancel?");
	if (closeWindow) window.location = "/_app/MyHome.aspx";
}

function ShowHide(wid, toggledElement) {
	if (document.getElementById(wid).checked) {
		$(toggledElement).hide();
	}
	else {
		$(toggledElement).slideDown("slow");
	}
}

/* if the wid.value is in strMatch then show toggled element, 'wid' is passed as this */
function ShowHideRadGrp(wid, strMatch, toggledElement) {
    if (wid.value.match(strMatch) != null) {
        $(toggledElement).slideDown("slow");
    }
    else {
        $(toggledElement).hide();
    }
}

function ToggleDiv(wid) {
	var toggledElement = document.getElementById(wid);
	if (toggledElement.style.display == "block") {
		toggledElement.style.display = "none";
	}
	else {
		toggledElement.style.display = "block";
	}

	return false;
}

function ToggleStatusDiv(myWid) {
    var selVal = myWid.options[myWid.selectedIndex].value;
    //alert("selVal is "+selVal);
    if (selVal == 10 || selVal == 11) {
        $("#WithdrawnForm").hide();
        $("#PlacedForm").slideDown("slow");
    }
    else if (selVal == 9) {
        $("#PlacedForm").hide();
        $("#WithdrawnForm").slideDown("slow");
    }
    else {
        $("#PlacedForm").hide();
        $("#WithdrawnForm").hide();
    }

    return false;
}

function ToggleInners2(partial) {
    var ulID = '#ul' + partial;
    var aWid = document.getElementById('a' + partial);
    var liID = '#li' + partial;

    if ($(ulID).is(":hidden")) {
        // JQuery effects do not work on "relative" positioned elements in IE7
        if ($.browser.msie && parseInt($.browser.version, 10) == 7) {
            //alert($.browser.version + " Version: " + parseInt($.browser.version, 10));
            $(ulID).show("slow");
        } else {
            $(ulID).slideDown("slow");
        }

        aWid.innerHTML = 'Collapse<span class="icon">&#160;</span>';
        $(liID).addClass('on');
    }
    else {
        $(ulID).hide();
        aWid.innerHTML = 'Expand<span class="icon">&#160;</span>';
        $(liID).removeClass('on');
    }

    return false;
}

function ToggleInners(partial) {
    var ulID = '#ul' + partial;
    var liID = '#li' + partial;
    var aWid = document.getElementById('a' + partial);

    if ($(aWid).length) {
        if ($(ulID).is(":hidden")) {
            // JQuery effects do not work on "relative" positioned elements in IE7
            if ($.browser.msie && parseInt($.browser.version, 10) == 7) {
                //alert($.browser.version + " Version: " + parseInt($.browser.version, 10));
                $(ulID).show("slow");
            } else {
                $(ulID).slideDown("slow");
            }

            aWid.innerHTML = 'Collapse<span class="icon">&#160;</span>';
            $(liID).addClass('on');
            AddtoCkArray(partial);
        }
        else {
            $(ulID).hide();
            aWid.innerHTML = 'Expand<span class="icon">&#160;</span>';
            $(liID).removeClass('on');
            RemoveFrmCkArray(partial);
        }
    }

    return false;
}

function GoToStateInfo(widID) {
    var stWid = document.getElementById(widID);
    if (stWid.selectedIndex > 0) {
        window.location = stWid.options[stWid.selectedIndex].value;
    }
    else {
        alert('Please make a selection');
    }
}

function DoCaseNum() {
    if ($('#qCaseNum').length) {
        window.location = '/_app/child/singleLookup.aspx?cn=' + $('#qCaseNum').val();
    }
}

function DoSiteSearch() {
    var strSearch = document.getElementById('main_search').value;
    if (strSearch.length > 0) {
        window.location = '/_app/common/sitesearch.aspx?q=' + encodeURIComponent(strSearch) + '&cx=001079527646157333373%3A3gbm939ol5i&cof=FORID%3A11#1650';
    }
}

function ClearTextInput(wid, dflt) {
    if (wid.value == dflt) {
        wid.value = '';
        wid.style.color = 'black';
    }
}

function GoToPage(widName, url, maxPage) {
    widName = widName;

    if ($('#' + widName).length) {
        var newPage = document.getElementById(widName).value;

        if (newPage.length > 0) {
            var isNonNumeric = newPage.match(/\D/);
            if (isNonNumeric || newPage > maxPage || newPage < 1) {
                alert('Please enter a page number between 1 and ' + maxPage);
            }
            else {
                window.location = url + newPage;
            }
        }
    }
}

function MCCchkall(wID,tWid) {
    /* chkbxlstStatus_ */
    var wid;
    var chk = tWid.checked;

    for (var i = 0; i < 100; i++) {
        wid = document.getElementById(wID + '_' + i);
        if (wid != null) {
            wid.checked = chk;
        }
        else { break; }
    }

    return false;
}

function UpListLink(wID) {
    $(wID).html('Removed from list');
    $(wID).removeClass('other').addClass('go');
    $(wID).click(function () { return false; });
}

function ToggleAlerts(idx) {
    var orgAlerts = document.getElementById('dvOrgAlerts');
    var myAlerts = document.getElementById('dvMyAlerts');
    var myLink = document.getElementById('spAlertToggle');
    var altClass = ['localerts', 'orgalerts'];

    if (orgAlerts.style.display == "none") {
        orgAlerts.style.display = "block";
        myAlerts.style.display = "none";
        myLink.innerHTML = toolsOnOrg;
        $('#altbnr').removeClass('myalerts');
        $('#altbnr').addClass(altClass[idx]);
    }
    else {
        orgAlerts.style.display = "none";
        myAlerts.style.display = "block";
        myLink.innerHTML = toolsOnMy;
        $('#altbnr').removeClass(altClass[idx]);
        $('#altbnr').addClass('myalerts');
    }
    return false;
}

function ShowAllAlerts(alertType, myID) {
    var thisID = '#' + myID;
    if ($(thisID).text() == 'Expand All') {
        //alert(' calling OpenAllAlerts');
        OpenAllAlerts(alertType);
        $(thisID).text('Collapse All');
    }
    else {
        //alert('calling CloseAllAlerts');
        CloseAllAlerts(alertType);
        $(thisID).text('Expand All');
    }
}

function OpenAllAlerts(alertType) {
    //alert('in OpenAll');
    var curID;
    var myIDs = ["Inq", "Act", "Pht"];

    if (alertType == "0") {
        curID = 'ulRC0';
        if (document.getElementById(curID)) {
            curID = '#' + curID;
            if ($(curID).is(":hidden")) {
                ToggleInners('RC0');
            }
        }
    }
    else { /* need to find out if it's 1 or 2 */
        if (document.getElementById('liInq2'))
            alertType = '2';
    }

    for (var i = 0; i < 3; i++) {
        curID = 'ul' + myIDs[i] + alertType

        if (document.getElementById(curID)) {
            curID = '#' + curID;
            if ($(curID).is(":hidden")) {
                ToggleInners(myIDs[i] + alertType);
            }
        }
    }
    return false;
}

function CloseAllAlerts(alertType) {
    //alert('in CloseAll');
    var curID;
    var myIDs = ["Inq", "Act", "Pht"];

    if (alertType == "0") {
        curID = 'ulRC0';
        if (document.getElementById(curID)) {
            curID = '#' + curID;
            if (!$(curID).is(":hidden")) {
                ToggleInners('RC0');
            }
        }
    }
    else { /* need to find out if it's 1 or 2 */
        if (document.getElementById('liInq2'))
            alertType = '2';
    }

    for (var i = 0; i < 3; i++) {
        curID = 'ul' + myIDs[i] + alertType

        if (document.getElementById(curID)) {
            curID = '#' + curID;
            if (!$(curID).is(":hidden")) {
                ToggleInners(myIDs[i] + alertType);
            }
        }
    }
    return false;
}

function SimpleDialogueOnLoad(myText, myTitle, modal) {
    var isModal = false;
    if (modal == 1) { isModal = true; }

    var $dialog = $('<div></div>')
		.html(myText)
		.dialog({
		    title: myTitle,
		    width: 600,
		    modal: isModal,
		    buttons: { "Close": function (ev, ui) { $(this).remove() } },
		    close: function (ev, ui) { $(this).remove(); }
		});

	$dialog.dialog('open');
}

function AjaxDialogueOnLoad(myKey, myTitle, modal) {
    AjaxOnLoad(myKey, myTitle, modal, '/_app/gAlert.aspx?k=')
}

function AjaxOnLoad(myKey,myTitle,modal,source) {
    var $dialog = get_ajax_dialog();
    //myTitle = myTitle + '<span style="display: none">Editing key is ' + myKey + '</span>';

    var isModal = false;
    if (modal == 1) { isModal = true; }

    $dialog.load(
        source + myKey,
        {},
        function (responseText, textStatus, XMLHttpRequest) {
            if (textStatus == 'error') {
                $dialog.html(responseText);
            }

            $dialog.dialog({
                title: myTitle,
                width: 600,
                modal: isModal,
                buttons: { "Close": function (ev, ui) { $(this).remove() } },
                close: function (ev, ui) { $(this).remove(); }
            });
        });
}

function AKDialogue(myKey) {
    var $dialog = get_ajax_dialog();
    //myTitle = myTitle + '<span style="display: none">Editing key is ' + myKey + '</span>';

    $dialog.load(
        '/_app/gAlert.aspx?em=b&k=' + myKey,
        {},
        function (responseText, textStatus, XMLHttpRequest) {
            if (responseText.indexOf('KEYNOTFOUND') >= 0) {
                $dialog.html(responseText);
            }
            else {
                $dialog.dialog({
                    width: 600,
                    modal: true,
                    buttons: { "Close": function (ev, ui) { $(this).remove() } },
                    close: function (ev, ui) { $(this).remove(); }
                });
            }
        });
}

function get_ajax_dialog() {
    var $dialog = $("#ajax-dialog");
    //No DOM element with this ID exists - create it
    if (!$dialog.size()) {
        $dialog = $('<div id="ajax_dialog" style="display:none;"></div>').appendTo('body');
    }
    return $dialog;
}

function LaunchChat() {
    var chatURL = '/_chat/client.php?locale=en&url=' + encodeURIComponent(document.location.href) + '&referrer=' + encodeURIComponent(document.referrer);
    
    if (navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) {
       window.event.preventDefault();
    }
    var newWindow = window.open(chatURL, 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');
    newWindow.focus();
    newWindow.opener = window;
    return false;
}

function SetUpMarquee() {
  $(function () {
    $("#marquee").number_slideshow({
        slideshow_autoplay: 'enable', //enable disable
        slideshow_loopOnce: 'true', // If true loop once and stop on last slide. Autoplay needs to be enabled.
        slideshow_time_interval: '7000',
        slideshow_window_background_color: "#b2c335",  // #f8f9f4 AUSK Body Color
        slideshow_window_padding: '0',
        slideshow_window_width: '960',
        slideshow_window_height: '294',
        slideshow_border_size: '0',
        slideshow_border_color: 'fff',
        slideshow_show_button: 'enable', //enable disable
        slideshow_show_title: 'enable',  //enable disable
        slideshow_button_text_color: '#4f7f8e',
        slideshow_button_background_color: '#a2d5e6',
        slideshow_button_current_background_color: '#fff',
        slideshow_button_border_color: '#a2d5e6',
        slideshow_loading_gif: '/_inc/css/images/loading.gif', //loading pic position, you can replace it.
        slideshow_button_border_size: '0'
    });
  });
}

function IsLocalTrackableLink(linkUrl) {
    // Check that a link is not null, local, and contains our file extensions we want to track via Analytics
    if (linkUrl != null &&
        linkUrl.indexOf("http://") < 0 && linkUrl.indexOf("https://") < 0 &&
        linkUrl.match(/\.(?:pdf|doc|docx|xls|xlsx|zip|rar|ppt|pptx|rtf|txt|flv|f4v|mp3|mp4)($|\&|\?)/)) {
        return true;
    }
    return false;
}

function featuredImageResizer(FeatureImageID) {

    var imgWidth = $(FeatureImageID).width();
    var imgHeight = $(FeatureImageID).height();

    //    alert(FeatureImageID + " " + imgWidth + " " + imgHeight);
    if (imgWidth > imgHeight) {
        $(FeatureImageID).css({ 'width': '108px', 'height': '85px' });
    }
    else {
        $(FeatureImageID).css({ 'width': '85px', 'height': '108px' });
    }
    $(FeatureImageID).show();
}

function checkPopUp(win) {
    if (win == null || typeof (win) == "undefined" || (win == null && win.outerWidth == 0) || (win != null && win.outerHeight == 0) || win.test == "undefined") {
        isBlocked = true;
        AjaxDialogueOnLoad('alt_PopupsBlocked', 'Popup Blocked', 0);
    }
    else if (win) { // in Chrome the popup window is always created so we have to check its size
        win.onload = function () {
            if (win.screenX === 0) {
                isBlocked = true;
                AjaxDialogueOnLoad('alt_PopupsBlocked', 'Popup Blocked', 0);
            }
        };
    }
}

function display_unsurported_browser(divID) {
	var newCode = "<div class='ie8'>"
		+ "<table>"
		+ "<tr>"
		+ "<td><h4>AdoptUSKids provides limited support for Internet Explorer versions 7 and 8.</h4></td>"
		+ "<td><a href='#' class='button ignore' onclick=\"$('div#unsupported-browser').hide();\">Ignore</a></td>"
		+ "</tr>"
		+ "</table>"
		+ "<p>We recommend upgrading your browser to the latest "
		+ "<a href='https://mozilla.org/firefox'target='_blank'>Firefox</a>, "
		+ "<a href='https://chrome.google.com' target='_blank'>Google Chrome</a>, "
		+ "or <a href='http://windows.microsoft.com/ie' target='_blank'>Internet Explorer</a>. "
		+ "It will improve your experience on the Web and make your computer safer."
		+ "</p>"
		+ "<p><a href='/technical-support/trouble-using-our-website#browser' target='_blank' >Learn more</a></p>"
		+ "</div>";

	$("#" + divID).empty().append(newCode);
}

function ShowOldIE() {
    if ($.cookie('OldIE') == 'yes')
    { }
    else {
    	$.cookie('OldIE', 'yes', { path: '/' });
    	display_unsurported_browser("unsupported-browser");
        $('div#unsupported-browser').show();
    }
}

$(document).ready(function () {

    if ($('#main_search').length) {
        $('#main_search').keyup(function (e) {
            if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
                DoSiteSearch();
            }
            return false;
        });
    }

    $('a.ajax-message, a.linkarrow').each(function () {
        var classes = $(this).attr('class').split(' ');
        var spanClassPre = '<span class="icon ';
        var spanClassPost = '">&#160;</span>';
        var spanClass = "";
        var noAppend = 0;
        if (classes.length > 1) {
            for (var i = 1; i < classes.length; i++) {
                switch (classes[i].toLowerCase()) {
                    case "withquestion":
                        spanClass = spanClass + '<span class="icon questionmark"></span>';
                        break;
                    case "witharrow":
                        spanClass = spanClass + '<span class="icon arrow">&#160;</span>';
                        break;
                    case "withespanol":
                        spanClass = spanClass + '<span class="icon espanol">&#160;</span>';
                        break;
                    case "withcheck":
                        spanClass = spanClass + '<span class="icon check">&#160;</span>';
                        break;
                    case "withexclamation":
                        spanClass = spanClass + '<span class="icon exclamation">&#160;</span>';
                        break;
                    case "noicon":
                        noAppend = 1;
                        break;
                }
            }
        }
        if (spanClass == "") { spanClass = spanClassPre + "arrow" + spanClassPost; }
        if (noAppend == 0) { $(this).append(spanClass); }
    });

    $('a.ajax-message').live('click', function () {
        var $dialog = get_ajax_dialog();
        var $parent = $(this);
        $(this).addClass('visited');
        //load remote content
        $dialog.load(
			$parent.attr('href'),
			{},
			function (responseText, textStatus, XMLHttpRequest) {
			    if (textStatus == 'error') {
			        $dialog.html(responseText);
			    }
			    var myWidth = 600;
			    if ($parent.attr('tabindex') != '') {
			        myWidth = $parent.attr('tabindex');
			    }
			    $dialog.dialog({
			        title: $parent.attr('title'),
			        width: myWidth,
			        buttons: { "Close": function (ev, ui) {
			            $(this).remove()
			        }
			        },
			        //Destroy on close. Required for some stacked modal functionality
			        close: function (ev, ui) {
			            $(this).remove();
			        }
			    }).data('parent', $parent); //Let the dialog know what opened it
			}
		);

        return false;
    });

    $('div#content a').each(function () {

        // Adds External Link Icon to Anchor
        if (this.hostname && this.hostname !== location.hostname) { 		                            // Compare the anchor tag's host name with location's host name
            if (!$(this).is('.noicon')) {
                $(this).append('<span class="icon window">&#160;</span>');
            }
        } else {
            if (this.hostname && this.hostname == location.hostname && $(this).is('.more')) { 		// Compare the anchor tag's host name with location's host name
                if (!$(this).is('.noicon')) {
                    $(this).append('<span class="icon arrow">&#160;</span>');
                }
            }
        }
    });

    // Adds Top ^ to <a name> anchors
    $('h2.goToTop').append('<span class="note"><a class="top" href="#">Top<span class="icon top">&160;</span></a></span>');

    // Adds Top ^ to <a name> anchors
    $('h2.goToTopES').append('<span class="note"><a class="top" href="#">Arriba<span class="icon top">&160;</span></a></span>');

    // back to top link
    var offset = 220;
    var duration = 500;
    $(window).scroll(function () {
        if ($(this).scrollTop() > offset) {
            $('a#back-to-top').fadeIn(duration);
        } else {
            $('a#back-to-top').fadeOut(duration);
        }
    });

    $('a#back-to-top').click(function (event) {
        event.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, duration);
        return false;
    })

    // Outline Rich Content Edit Blocks on Edit Button Hover
    $('a.rcEdit').hover(function () {
        $(this).parent().toggleClass('highlightEdit');
    });

    $(".status").click(function () {
        $('a.rcEdit').toggleClass('rcEditButtonHide');
    });

    // Colors number of OL LI
    $('ol > li').each(function () {
        $(this).prepend("<span style='color:#9fac3b;margin-right:4px;'>" + ($(this).index() + 1) + ".</span>");
    });

    // Add transparent .png to broken images - should auto size to fill and just expose background.
    $("img").error(function () {
        $(this).unbind("error").attr("src", "/_img/no-image.png");
    });

    // Bring up dialogue based on presence of qstring "ak"
    var qAK = $.getUrlVar('ak');
    if (qAK != undefined) {
        AKDialogue(qAK);
    }

    // initialize Shadowbox
    Shadowbox.init({
        modal: false,
        overlayOpacity: 0.85,
        handleOversize: "resize"
    });
});