/*
* JQuery Plugins - Separate from Core and Custom since these are updated in self-contained packages
*/

/* JQuery URL Parser Plugin 1.2
* https://github.com/allmarkedup/jQuery-URL-Parser
*
*/

(function ($, undefined) {

    var tag2attr = {
        a: 'href',
        img: 'src',
        form: 'action',
        base: 'href',
        script: 'src',
        iframe: 'src',
        link: 'href'
    },
	key = ["source", "protocol", "authority", "userInfo", "user", "password", "host", "port", "relative", "path", "directory", "file", "query", "fragment"], // keys available to query
	aliases = { "anchor": "fragment" }, // aliases for backwards compatability
	parser = {
	    strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,  //less intuitive, more accurate to the specs
	    loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // more intuitive, fails on relative paths and deviates from specs
	},
	querystring_parser = /(?:^|&|;)([^&=;]*)=?([^&;]*)/g, // supports both ampersand and semicolon-delimted query string key/value pairs
	fragment_parser = /(?:^|&|;)([^&=;]*)=?([^&;]*)/g; // supports both ampersand and semicolon-delimted fragment key/value pairs

    function parseUri(url, strictMode) {
        var str = decodeURI(url),
		    res = parser[strictMode || false ? "strict" : "loose"].exec(str),
		    uri = { attr: {}, param: {}, seg: {} },
		    i = 14;

        while (i--) {
            uri.attr[key[i]] = res[i] || "";
        }

        // build query and fragment parameters

        uri.param['query'] = {};
        uri.param['fragment'] = {};

        uri.attr['query'].replace(querystring_parser, function ($0, $1, $2) {
            if ($1) {
                uri.param['query'][$1] = $2;
            }
        });

        uri.attr['fragment'].replace(fragment_parser, function ($0, $1, $2) {
            if ($1) {
                uri.param['fragment'][$1] = $2;
            }
        });

        // split path and fragement into segments

        uri.seg['path'] = uri.attr.path.replace(/^\/+|\/+$/g, '').split('/');

        uri.seg['fragment'] = uri.attr.fragment.replace(/^\/+|\/+$/g, '').split('/');

        // compile a 'base' domain attribute

        uri.attr['base'] = uri.attr.host ? uri.attr.protocol + "://" + uri.attr.host + (uri.attr.port ? ":" + uri.attr.port : '') : '';

        return uri;
    };

    function getAttrName(elm) {
        var tn = elm.tagName;
        if (tn !== undefined) return tag2attr[tn.toLowerCase()];
        return tn;
    }

    $.fn.url = function (strictMode) {
        var url = '';

        if (this.length) {
            url = $(this).attr(getAttrName(this[0])) || '';
        }

        return $.url(url, strictMode);
    };

    $.url = function (url, strictMode) {
        if (arguments.length === 1 && url === true) {
            strictMode = true;
            url = undefined;
        }

        strictMode = strictMode || false;
        url = url || window.location.toString();

        return {

            data: parseUri(url, strictMode),

            // get various attributes from the URI
            attr: function (attr) {
                attr = aliases[attr] || attr;
                return attr !== undefined ? this.data.attr[attr] : this.data.attr;
            },

            // return query string parameters
            param: function (param) {
                return param !== undefined ? this.data.param.query[param] : this.data.param.query;
            },

            // return fragment parameters
            fparam: function (param) {
                return param !== undefined ? this.data.param.fragment[param] : this.data.param.fragment;
            },

            // return path segments
            segment: function (seg) {
                if (seg === undefined) {
                    return this.data.seg.path;
                }
                else {
                    seg = seg < 0 ? this.data.seg.path.length + seg : seg - 1; // negative segments count from the end
                    return this.data.seg.path[seg];
                }
            },

            // return fragment segments
            fsegment: function (seg) {
                if (seg === undefined) {
                    return this.data.seg.fragment;
                }
                else {
                    seg = seg < 0 ? this.data.seg.fragment.length + seg : seg - 1; // negative segments count from the end
                    return this.data.seg.fragment[seg];
                }
            }

        };

    };

})(jQuery);

/*
* jQuery Cycle Plugin (core engine)
* Examples and documentation at: http://jquery.malsup.com/cycle/
* Copyright (c) 2007-2010 M. Alsup
* Version: 2.88 (08-JUN-2010)
* Dual licensed under the MIT and GPL licenses.
* http://jquery.malsup.com/license.html
* Requires: jQuery v1.2.6 or later
*/
(function ($) { var ver = "2.88"; if ($.support == undefined) { $.support = { opacity: !($.browser.msie) }; } function debug(s) { if ($.fn.cycle.debug) { log(s); } } function log() { if (window.console && window.console.log) { window.console.log("[cycle] " + Array.prototype.join.call(arguments, " ")); } } $.fn.cycle = function (options, arg2) { var o = { s: this.selector, c: this.context }; if (this.length === 0 && options != "stop") { if (!$.isReady && o.s) { log("DOM not ready, queuing slideshow"); $(function () { $(o.s, o.c).cycle(options, arg2); }); return this; } log("terminating; zero elements found by selector" + ($.isReady ? "" : " (DOM not ready)")); return this; } return this.each(function () { var opts = handleArguments(this, options, arg2); if (opts === false) { return; } opts.updateActivePagerLink = opts.updateActivePagerLink || $.fn.cycle.updateActivePagerLink; if (this.cycleTimeout) { clearTimeout(this.cycleTimeout); } this.cycleTimeout = this.cyclePause = 0; var $cont = $(this); var $slides = opts.slideExpr ? $(opts.slideExpr, this) : $cont.children(); var els = $slides.get(); if (els.length < 2) { log("terminating; too few slides: " + els.length); return; } var opts2 = buildOptions($cont, $slides, els, opts, o); if (opts2 === false) { return; } var startTime = opts2.continuous ? 10 : getTimeout(els[opts2.currSlide], els[opts2.nextSlide], opts2, !opts2.rev); if (startTime) { startTime += (opts2.delay || 0); if (startTime < 10) { startTime = 10; } debug("first timeout: " + startTime); this.cycleTimeout = setTimeout(function () { go(els, opts2, 0, (!opts2.rev && !opts.backwards)); }, startTime); } }); }; function handleArguments(cont, options, arg2) { if (cont.cycleStop == undefined) { cont.cycleStop = 0; } if (options === undefined || options === null) { options = {}; } if (options.constructor == String) { switch (options) { case "destroy": case "stop": var opts = $(cont).data("cycle.opts"); if (!opts) { return false; } cont.cycleStop++; if (cont.cycleTimeout) { clearTimeout(cont.cycleTimeout); } cont.cycleTimeout = 0; $(cont).removeData("cycle.opts"); if (options == "destroy") { destroy(opts); } return false; case "toggle": cont.cyclePause = (cont.cyclePause === 1) ? 0 : 1; checkInstantResume(cont.cyclePause, arg2, cont); return false; case "pause": cont.cyclePause = 1; return false; case "resume": cont.cyclePause = 0; checkInstantResume(false, arg2, cont); return false; case "prev": case "next": var opts = $(cont).data("cycle.opts"); if (!opts) { log('options not found, "prev/next" ignored'); return false; } $.fn.cycle[options](opts); return false; default: options = { fx: options }; } return options; } else { if (options.constructor == Number) { var num = options; options = $(cont).data("cycle.opts"); if (!options) { log("options not found, can not advance slide"); return false; } if (num < 0 || num >= options.elements.length) { log("invalid slide index: " + num); return false; } options.nextSlide = num; if (cont.cycleTimeout) { clearTimeout(cont.cycleTimeout); cont.cycleTimeout = 0; } if (typeof arg2 == "string") { options.oneTimeFx = arg2; } go(options.elements, options, 1, num >= options.currSlide); return false; } } return options; function checkInstantResume(isPaused, arg2, cont) { if (!isPaused && arg2 === true) { var options = $(cont).data("cycle.opts"); if (!options) { log("options not found, can not resume"); return false; } if (cont.cycleTimeout) { clearTimeout(cont.cycleTimeout); cont.cycleTimeout = 0; } go(options.elements, options, 1, (!opts.rev && !opts.backwards)); } } } function removeFilter(el, opts) { if (!$.support.opacity && opts.cleartype && el.style.filter) { try { el.style.removeAttribute("filter"); } catch (smother) { } } } function destroy(opts) { if (opts.next) { $(opts.next).unbind(opts.prevNextEvent); } if (opts.prev) { $(opts.prev).unbind(opts.prevNextEvent); } if (opts.pager || opts.pagerAnchorBuilder) { $.each(opts.pagerAnchors || [], function () { this.unbind().remove(); }); } opts.pagerAnchors = null; if (opts.destroy) { opts.destroy(opts); } } function buildOptions($cont, $slides, els, options, o) { var opts = $.extend({}, $.fn.cycle.defaults, options || {}, $.metadata ? $cont.metadata() : $.meta ? $cont.data() : {}); if (opts.autostop) { opts.countdown = opts.autostopCount || els.length; } var cont = $cont[0]; $cont.data("cycle.opts", opts); opts.$cont = $cont; opts.stopCount = cont.cycleStop; opts.elements = els; opts.before = opts.before ? [opts.before] : []; opts.after = opts.after ? [opts.after] : []; opts.after.unshift(function () { opts.busy = 0; }); if (!$.support.opacity && opts.cleartype) { opts.after.push(function () { removeFilter(this, opts); }); } if (opts.continuous) { opts.after.push(function () { go(els, opts, 0, (!opts.rev && !opts.backwards)); }); } saveOriginalOpts(opts); if (!$.support.opacity && opts.cleartype && !opts.cleartypeNoBg) { clearTypeFix($slides); } if ($cont.css("position") == "static") { $cont.css("position", "relative"); } if (opts.width) { $cont.width(opts.width); } if (opts.height && opts.height != "auto") { $cont.height(opts.height); } if (opts.startingSlide) { opts.startingSlide = parseInt(opts.startingSlide); } else { if (opts.backwards) { opts.startingSlide = els.length - 1; } } if (opts.random) { opts.randomMap = []; for (var i = 0; i < els.length; i++) { opts.randomMap.push(i); } opts.randomMap.sort(function (a, b) { return Math.random() - 0.5; }); opts.randomIndex = 1; opts.startingSlide = opts.randomMap[1]; } else { if (opts.startingSlide >= els.length) { opts.startingSlide = 0; } } opts.currSlide = opts.startingSlide || 0; var first = opts.startingSlide; $slides.css({ position: "absolute", top: 0, left: 0 }).hide().each(function (i) { var z; if (opts.backwards) { z = first ? i <= first ? els.length + (i - first) : first - i : els.length - i; } else { z = first ? i >= first ? els.length - (i - first) : first - i : els.length - i; } $(this).css("z-index", z); }); $(els[first]).css("opacity", 1).show(); removeFilter(els[first], opts); if (opts.fit && opts.width) { $slides.width(opts.width); } if (opts.fit && opts.height && opts.height != "auto") { $slides.height(opts.height); } var reshape = opts.containerResize && !$cont.innerHeight(); if (reshape) { var maxw = 0, maxh = 0; for (var j = 0; j < els.length; j++) { var $e = $(els[j]), e = $e[0], w = $e.outerWidth(), h = $e.outerHeight(); if (!w) { w = e.offsetWidth || e.width || $e.attr("width"); } if (!h) { h = e.offsetHeight || e.height || $e.attr("height"); } maxw = w > maxw ? w : maxw; maxh = h > maxh ? h : maxh; } if (maxw > 0 && maxh > 0) { $cont.css({ width: maxw + "px", height: maxh + "px" }); } } if (opts.pause) { $cont.hover(function () { this.cyclePause++; }, function () { this.cyclePause--; }); } if (supportMultiTransitions(opts) === false) { return false; } var requeue = false; options.requeueAttempts = options.requeueAttempts || 0; $slides.each(function () { var $el = $(this); this.cycleH = (opts.fit && opts.height) ? opts.height : ($el.height() || this.offsetHeight || this.height || $el.attr("height") || 0); this.cycleW = (opts.fit && opts.width) ? opts.width : ($el.width() || this.offsetWidth || this.width || $el.attr("width") || 0); if ($el.is("img")) { var loadingIE = ($.browser.msie && this.cycleW == 28 && this.cycleH == 30 && !this.complete); var loadingFF = ($.browser.mozilla && this.cycleW == 34 && this.cycleH == 19 && !this.complete); var loadingOp = ($.browser.opera && ((this.cycleW == 42 && this.cycleH == 19) || (this.cycleW == 37 && this.cycleH == 17)) && !this.complete); var loadingOther = (this.cycleH == 0 && this.cycleW == 0 && !this.complete); if (loadingIE || loadingFF || loadingOp || loadingOther) { if (o.s && opts.requeueOnImageNotLoaded && ++options.requeueAttempts < 100) { log(options.requeueAttempts, " - img slide not loaded, requeuing slideshow: ", this.src, this.cycleW, this.cycleH); setTimeout(function () { $(o.s, o.c).cycle(options); }, opts.requeueTimeout); requeue = true; return false; } else { log("could not determine size of image: " + this.src, this.cycleW, this.cycleH); } } } return true; }); if (requeue) { return false; } opts.cssBefore = opts.cssBefore || {}; opts.animIn = opts.animIn || {}; opts.animOut = opts.animOut || {}; $slides.not(":eq(" + first + ")").css(opts.cssBefore); if (opts.cssFirst) { $($slides[first]).css(opts.cssFirst); } if (opts.timeout) { opts.timeout = parseInt(opts.timeout); if (opts.speed.constructor == String) { opts.speed = $.fx.speeds[opts.speed] || parseInt(opts.speed); } if (!opts.sync) { opts.speed = opts.speed / 2; } var buffer = opts.fx == "shuffle" ? 500 : 250; while ((opts.timeout - opts.speed) < buffer) { opts.timeout += opts.speed; } } if (opts.easing) { opts.easeIn = opts.easeOut = opts.easing; } if (!opts.speedIn) { opts.speedIn = opts.speed; } if (!opts.speedOut) { opts.speedOut = opts.speed; } opts.slideCount = els.length; opts.currSlide = opts.lastSlide = first; if (opts.random) { if (++opts.randomIndex == els.length) { opts.randomIndex = 0; } opts.nextSlide = opts.randomMap[opts.randomIndex]; } else { if (opts.backwards) { opts.nextSlide = opts.startingSlide == 0 ? (els.length - 1) : opts.startingSlide - 1; } else { opts.nextSlide = opts.startingSlide >= (els.length - 1) ? 0 : opts.startingSlide + 1; } } if (!opts.multiFx) { var init = $.fn.cycle.transitions[opts.fx]; if ($.isFunction(init)) { init($cont, $slides, opts); } else { if (opts.fx != "custom" && !opts.multiFx) { log("unknown transition: " + opts.fx, "; slideshow terminating"); return false; } } } var e0 = $slides[first]; if (opts.before.length) { opts.before[0].apply(e0, [e0, e0, opts, true]); } if (opts.after.length > 1) { opts.after[1].apply(e0, [e0, e0, opts, true]); } if (opts.next) { $(opts.next).bind(opts.prevNextEvent, function () { return advance(opts, opts.rev ? -1 : 1); }); } if (opts.prev) { $(opts.prev).bind(opts.prevNextEvent, function () { return advance(opts, opts.rev ? 1 : -1); }); } if (opts.pager || opts.pagerAnchorBuilder) { buildPager(els, opts); } exposeAddSlide(opts, els); return opts; } function saveOriginalOpts(opts) { opts.original = { before: [], after: [] }; opts.original.cssBefore = $.extend({}, opts.cssBefore); opts.original.cssAfter = $.extend({}, opts.cssAfter); opts.original.animIn = $.extend({}, opts.animIn); opts.original.animOut = $.extend({}, opts.animOut); $.each(opts.before, function () { opts.original.before.push(this); }); $.each(opts.after, function () { opts.original.after.push(this); }); } function supportMultiTransitions(opts) { var i, tx, txs = $.fn.cycle.transitions; if (opts.fx.indexOf(",") > 0) { opts.multiFx = true; opts.fxs = opts.fx.replace(/\s*/g, "").split(","); for (i = 0; i < opts.fxs.length; i++) { var fx = opts.fxs[i]; tx = txs[fx]; if (!tx || !txs.hasOwnProperty(fx) || !$.isFunction(tx)) { log("discarding unknown transition: ", fx); opts.fxs.splice(i, 1); i--; } } if (!opts.fxs.length) { log("No valid transitions named; slideshow terminating."); return false; } } else { if (opts.fx == "all") { opts.multiFx = true; opts.fxs = []; for (p in txs) { tx = txs[p]; if (txs.hasOwnProperty(p) && $.isFunction(tx)) { opts.fxs.push(p); } } } } if (opts.multiFx && opts.randomizeEffects) { var r1 = Math.floor(Math.random() * 20) + 30; for (i = 0; i < r1; i++) { var r2 = Math.floor(Math.random() * opts.fxs.length); opts.fxs.push(opts.fxs.splice(r2, 1)[0]); } debug("randomized fx sequence: ", opts.fxs); } return true; } function exposeAddSlide(opts, els) { opts.addSlide = function (newSlide, prepend) { var $s = $(newSlide), s = $s[0]; if (!opts.autostopCount) { opts.countdown++; } els[prepend ? "unshift" : "push"](s); if (opts.els) { opts.els[prepend ? "unshift" : "push"](s); } opts.slideCount = els.length; $s.css("position", "absolute"); $s[prepend ? "prependTo" : "appendTo"](opts.$cont); if (prepend) { opts.currSlide++; opts.nextSlide++; } if (!$.support.opacity && opts.cleartype && !opts.cleartypeNoBg) { clearTypeFix($s); } if (opts.fit && opts.width) { $s.width(opts.width); } if (opts.fit && opts.height && opts.height != "auto") { $slides.height(opts.height); } s.cycleH = (opts.fit && opts.height) ? opts.height : $s.height(); s.cycleW = (opts.fit && opts.width) ? opts.width : $s.width(); $s.css(opts.cssBefore); if (opts.pager || opts.pagerAnchorBuilder) { $.fn.cycle.createPagerAnchor(els.length - 1, s, $(opts.pager), els, opts); } if ($.isFunction(opts.onAddSlide)) { opts.onAddSlide($s); } else { $s.hide(); } }; } $.fn.cycle.resetState = function (opts, fx) { fx = fx || opts.fx; opts.before = []; opts.after = []; opts.cssBefore = $.extend({}, opts.original.cssBefore); opts.cssAfter = $.extend({}, opts.original.cssAfter); opts.animIn = $.extend({}, opts.original.animIn); opts.animOut = $.extend({}, opts.original.animOut); opts.fxFn = null; $.each(opts.original.before, function () { opts.before.push(this); }); $.each(opts.original.after, function () { opts.after.push(this); }); var init = $.fn.cycle.transitions[fx]; if ($.isFunction(init)) { init(opts.$cont, $(opts.elements), opts); } }; function go(els, opts, manual, fwd) { if (manual && opts.busy && opts.manualTrump) { debug("manualTrump in go(), stopping active transition"); $(els).stop(true, true); opts.busy = false; } if (opts.busy) { debug("transition active, ignoring new tx request"); return; } var p = opts.$cont[0], curr = els[opts.currSlide], next = els[opts.nextSlide]; if (p.cycleStop != opts.stopCount || p.cycleTimeout === 0 && !manual) { return; } if (!manual && !p.cyclePause && !opts.bounce && ((opts.autostop && (--opts.countdown <= 0)) || (opts.nowrap && !opts.random && opts.nextSlide < opts.currSlide))) { if (opts.end) { opts.end(opts); } return; } var changed = false; if ((manual || !p.cyclePause) && (opts.nextSlide != opts.currSlide)) { changed = true; var fx = opts.fx; curr.cycleH = curr.cycleH || $(curr).height(); curr.cycleW = curr.cycleW || $(curr).width(); next.cycleH = next.cycleH || $(next).height(); next.cycleW = next.cycleW || $(next).width(); if (opts.multiFx) { if (opts.lastFx == undefined || ++opts.lastFx >= opts.fxs.length) { opts.lastFx = 0; } fx = opts.fxs[opts.lastFx]; opts.currFx = fx; } if (opts.oneTimeFx) { fx = opts.oneTimeFx; opts.oneTimeFx = null; } $.fn.cycle.resetState(opts, fx); if (opts.before.length) { $.each(opts.before, function (i, o) { if (p.cycleStop != opts.stopCount) { return; } o.apply(next, [curr, next, opts, fwd]); }); } var after = function () { $.each(opts.after, function (i, o) { if (p.cycleStop != opts.stopCount) { return; } o.apply(next, [curr, next, opts, fwd]); }); }; debug("tx firing; currSlide: " + opts.currSlide + "; nextSlide: " + opts.nextSlide); opts.busy = 1; if (opts.fxFn) { opts.fxFn(curr, next, opts, after, fwd, manual && opts.fastOnEvent); } else { if ($.isFunction($.fn.cycle[opts.fx])) { $.fn.cycle[opts.fx](curr, next, opts, after, fwd, manual && opts.fastOnEvent); } else { $.fn.cycle.custom(curr, next, opts, after, fwd, manual && opts.fastOnEvent); } } } if (changed || opts.nextSlide == opts.currSlide) { opts.lastSlide = opts.currSlide; if (opts.random) { opts.currSlide = opts.nextSlide; if (++opts.randomIndex == els.length) { opts.randomIndex = 0; } opts.nextSlide = opts.randomMap[opts.randomIndex]; if (opts.nextSlide == opts.currSlide) { opts.nextSlide = (opts.currSlide == opts.slideCount - 1) ? 0 : opts.currSlide + 1; } } else { if (opts.backwards) { var roll = (opts.nextSlide - 1) < 0; if (roll && opts.bounce) { opts.backwards = !opts.backwards; opts.nextSlide = 1; opts.currSlide = 0; } else { opts.nextSlide = roll ? (els.length - 1) : opts.nextSlide - 1; opts.currSlide = roll ? 0 : opts.nextSlide + 1; } } else { var roll = (opts.nextSlide + 1) == els.length; if (roll && opts.bounce) { opts.backwards = !opts.backwards; opts.nextSlide = els.length - 2; opts.currSlide = els.length - 1; } else { opts.nextSlide = roll ? 0 : opts.nextSlide + 1; opts.currSlide = roll ? els.length - 1 : opts.nextSlide - 1; } } } } if (changed && opts.pager) { opts.updateActivePagerLink(opts.pager, opts.currSlide, opts.activePagerClass); } var ms = 0; if (opts.timeout && !opts.continuous) { ms = getTimeout(els[opts.currSlide], els[opts.nextSlide], opts, fwd); } else { if (opts.continuous && p.cyclePause) { ms = 10; } } if (ms > 0) { p.cycleTimeout = setTimeout(function () { go(els, opts, 0, (!opts.rev && !opts.backwards)); }, ms); } } $.fn.cycle.updateActivePagerLink = function (pager, currSlide, clsName) { $(pager).each(function () { $(this).children().removeClass(clsName).eq(currSlide).addClass(clsName); }); }; function getTimeout(curr, next, opts, fwd) { if (opts.timeoutFn) { var t = opts.timeoutFn.call(curr, curr, next, opts, fwd); while ((t - opts.speed) < 250) { t += opts.speed; } debug("calculated timeout: " + t + "; speed: " + opts.speed); if (t !== false) { return t; } } return opts.timeout; } $.fn.cycle.next = function (opts) { advance(opts, opts.rev ? -1 : 1); }; $.fn.cycle.prev = function (opts) { advance(opts, opts.rev ? 1 : -1); }; function advance(opts, val) { var els = opts.elements; var p = opts.$cont[0], timeout = p.cycleTimeout; if (timeout) { clearTimeout(timeout); p.cycleTimeout = 0; } if (opts.random && val < 0) { opts.randomIndex--; if (--opts.randomIndex == -2) { opts.randomIndex = els.length - 2; } else { if (opts.randomIndex == -1) { opts.randomIndex = els.length - 1; } } opts.nextSlide = opts.randomMap[opts.randomIndex]; } else { if (opts.random) { opts.nextSlide = opts.randomMap[opts.randomIndex]; } else { opts.nextSlide = opts.currSlide + val; if (opts.nextSlide < 0) { if (opts.nowrap) { return false; } opts.nextSlide = els.length - 1; } else { if (opts.nextSlide >= els.length) { if (opts.nowrap) { return false; } opts.nextSlide = 0; } } } } var cb = opts.onPrevNextEvent || opts.prevNextClick; if ($.isFunction(cb)) { cb(val > 0, opts.nextSlide, els[opts.nextSlide]); } go(els, opts, 1, val >= 0); return false; } function buildPager(els, opts) { var $p = $(opts.pager); $.each(els, function (i, o) { $.fn.cycle.createPagerAnchor(i, o, $p, els, opts); }); opts.updateActivePagerLink(opts.pager, opts.startingSlide, opts.activePagerClass); } $.fn.cycle.createPagerAnchor = function (i, el, $p, els, opts) { var a; if ($.isFunction(opts.pagerAnchorBuilder)) { a = opts.pagerAnchorBuilder(i, el); debug("pagerAnchorBuilder(" + i + ", el) returned: " + a); } else { a = '<a href="#">' + (i + 1) + "</a>"; } if (!a) { return; } var $a = $(a); if ($a.parents("body").length === 0) { var arr = []; if ($p.length > 1) { $p.each(function () { var $clone = $a.clone(true); $(this).append($clone); arr.push($clone[0]); }); $a = $(arr); } else { $a.appendTo($p); } } opts.pagerAnchors = opts.pagerAnchors || []; opts.pagerAnchors.push($a); $a.bind(opts.pagerEvent, function (e) { e.preventDefault(); opts.nextSlide = i; var p = opts.$cont[0], timeout = p.cycleTimeout; if (timeout) { clearTimeout(timeout); p.cycleTimeout = 0; } var cb = opts.onPagerEvent || opts.pagerClick; if ($.isFunction(cb)) { cb(opts.nextSlide, els[opts.nextSlide]); } go(els, opts, 1, opts.currSlide < i); }); if (!/^click/.test(opts.pagerEvent) && !opts.allowPagerClickBubble) { $a.bind("click.cycle", function () { return false; }); } if (opts.pauseOnPagerHover) { $a.hover(function () { opts.$cont[0].cyclePause++; }, function () { opts.$cont[0].cyclePause--; }); } }; $.fn.cycle.hopsFromLast = function (opts, fwd) { var hops, l = opts.lastSlide, c = opts.currSlide; if (fwd) { hops = c > l ? c - l : opts.slideCount - l; } else { hops = c < l ? l - c : l + opts.slideCount - c; } return hops; }; function clearTypeFix($slides) { debug("applying clearType background-color hack"); function hex(s) { s = parseInt(s).toString(16); return s.length < 2 ? "0" + s : s; } function getBg(e) { for (; e && e.nodeName.toLowerCase() != "html"; e = e.parentNode) { var v = $.css(e, "background-color"); if (v.indexOf("rgb") >= 0) { var rgb = v.match(/\d+/g); return "#" + hex(rgb[0]) + hex(rgb[1]) + hex(rgb[2]); } if (v && v != "transparent") { return v; } } return "#ffffff"; } $slides.each(function () { $(this).css("background-color", getBg(this)); }); } $.fn.cycle.commonReset = function (curr, next, opts, w, h, rev) { $(opts.elements).not(curr).hide(); opts.cssBefore.opacity = 1; opts.cssBefore.display = "block"; if (w !== false && next.cycleW > 0) { opts.cssBefore.width = next.cycleW; } if (h !== false && next.cycleH > 0) { opts.cssBefore.height = next.cycleH; } opts.cssAfter = opts.cssAfter || {}; opts.cssAfter.display = "none"; $(curr).css("zIndex", opts.slideCount + (rev === true ? 1 : 0)); $(next).css("zIndex", opts.slideCount + (rev === true ? 0 : 1)); }; $.fn.cycle.custom = function (curr, next, opts, cb, fwd, speedOverride) { var $l = $(curr), $n = $(next); var speedIn = opts.speedIn, speedOut = opts.speedOut, easeIn = opts.easeIn, easeOut = opts.easeOut; $n.css(opts.cssBefore); if (speedOverride) { if (typeof speedOverride == "number") { speedIn = speedOut = speedOverride; } else { speedIn = speedOut = 1; } easeIn = easeOut = null; } var fn = function () { $n.animate(opts.animIn, speedIn, easeIn, cb); }; $l.animate(opts.animOut, speedOut, easeOut, function () { if (opts.cssAfter) { $l.css(opts.cssAfter); } if (!opts.sync) { fn(); } }); if (opts.sync) { fn(); } }; $.fn.cycle.transitions = { fade: function ($cont, $slides, opts) { $slides.not(":eq(" + opts.currSlide + ")").css("opacity", 0); opts.before.push(function (curr, next, opts) { $.fn.cycle.commonReset(curr, next, opts); opts.cssBefore.opacity = 0; }); opts.animIn = { opacity: 1 }; opts.animOut = { opacity: 0 }; opts.cssBefore = { top: 0, left: 0 }; } }; $.fn.cycle.ver = function () { return ver; }; $.fn.cycle.defaults = { fx: "fade", timeout: 4000, timeoutFn: null, continuous: 0, speed: 1000, speedIn: null, speedOut: null, next: null, prev: null, onPrevNextEvent: null, prevNextEvent: "click.cycle", pager: null, onPagerEvent: null, pagerEvent: "click.cycle", allowPagerClickBubble: false, pagerAnchorBuilder: null, before: null, after: null, end: null, easing: null, easeIn: null, easeOut: null, shuffle: null, animIn: null, animOut: null, cssBefore: null, cssAfter: null, fxFn: null, height: "auto", startingSlide: 0, sync: 1, random: 0, fit: 0, containerResize: 1, pause: 0, pauseOnPagerHover: 0, autostop: 0, autostopCount: 0, delay: 0, slideExpr: null, cleartype: !$.support.opacity, cleartypeNoBg: false, nowrap: 0, fastOnEvent: 0, randomizeEffects: 1, rev: 0, manualTrump: true, requeueOnImageNotLoaded: true, requeueTimeout: 250, activePagerClass: "activeSlide", updateActivePagerLink: null, backwards: false }; })(jQuery);



/*
*  JqueryAsynchImageLoader (JAIL) : plugin for jQuery
*
* Developed by
* Sebastiano Armeli-Battana (@sebarmeli) - http://www.sebastianoarmelibattana.com | http://blog.sebarmeli.com
*
* Licensed under MIT
* @version 0.7
*/
(function (a) { var b = a(window); a.fn.asynchImageLoader = function (d) { d = a.extend({ timeout: 10, effect: false, speed: 400, selector: null, event: "load+scroll", callback: jQuery.noop, offset: 0, placeholder: false }, d); var c = this; this.data("triggerEl", (d.selector) ? a(d.selector) : b); if (d.placeholder !== false) { c.each(function () { a(this).attr("src", d.placeholder); }); } if (/^load/.test(d.event)) { a.asynchImageLoader.later.call(this, d); } else { a.asynchImageLoader.onEvent.call(this, d, c); } return this; }; a.asynchImageLoader = { _purgeStack: function (c) { var d = 0; while (true) { if (d === c.length) { break; } else { if (c[d].getAttribute("data-href")) { d++; } else { c.splice(d, 1); } } } }, _loadOnEvent: function (g) { var f = a(this), d = g.data.options, c = g.data.images; a.asynchImageLoader._loadImage(d, f); f.unbind(d.event, a.asynchImageLoader._loadOnEvent); d.callback.call(this, d); a.asynchImageLoader._purgeStack(c); }, _bufferedEventListener: function (g) { var c = g.data.images, d = g.data.options, f = c.data("triggerEl"); clearTimeout(c.data("poller")); c.data("poller", setTimeout(function () { c.each(function e() { a.asynchImageLoader._loadImageIfVisible(d, this, f); }); a.asynchImageLoader._purgeStack(c); d.callback.call(this, d, c); }, d.timeout)); }, onEvent: function (d, c) { c = c || this; if (d.event === "scroll" || d.selector) { var e = c.data("triggerEl"); if (c.length > 0) { e.bind(d.event, { images: c, options: d }, a.asynchImageLoader._bufferedEventListener); } else { var f = (d.selector) ? a(d.selector) : b; f.unbind(d.event, a.asynchImageLoader._bufferedEventListener); } } else { c.bind(d.event, { options: d, images: c }, a.asynchImageLoader._loadOnEvent); } }, later: function (d) { var c = this; if (d.event === "load") { c.each(function () { a.asynchImageLoader._loadImageIfVisible(d, this, c.data("triggerEl")); }); } a.asynchImageLoader._purgeStack(c); setTimeout(function () { if (d.event === "load") { c.each(function () { a.asynchImageLoader._loadImage(d, a(this)); }); } else { c.each(function () { a.asynchImageLoader._loadImageIfVisible(d, this, c.data("triggerEl")); }); } a.asynchImageLoader._purgeStack(c); if (/^load\+/.test(d.event)) { d.event = d.event.substring(d.event.indexOf("+") + 1, d.event.length); a.asynchImageLoader.onEvent(d, c); } }, d.timeout); }, _loadImageIfVisible: function (d, g, f) { var e = a(g), c = (d.event === "scroll" ? f : b); if (a.asynchImageLoader._isInTheScreen(c, e, d.offset)) { a.asynchImageLoader._loadImage(d, e); } }, _isInTheScreen: function (i, c, g) { var e = i[0] === window, l = i.offset() || { top: 0, left: 0 }, f = l.top + (e ? i.scrollTop() : 0), h = l.left + (e ? i.scrollLeft() : 0), d = h + i.width(), j = f + i.height(), k = c.offset(); return (f - g) <= k.top && (j + g) >= k.top && (h - g) <= k.left && (d + g) >= k.left; }, _loadImage: function (c, d) { d.hide(); d.attr("src", d.attr("data-href")); d.removeAttr("data-href"); if (c.effect) { if (c.speed) { d[c.effect](c.speed); } else { d[c.effect](); } } else { d.show(); } } }; } (jQuery));


/*
* jQuery OnDemand Image Loader
*
* Developed by
* Copyright 2011 - Anthony McLin
* http://anthonymclin.com
* Version 1.0
* Licensed under GPLv2
*/
(function ($) {
    $.fn.onDemandLoader = function (options) {

        // Configuration
        options = $.extend({
            selector: null,
            spinner: null,
            callback: jQuery.noop
        }, options);

        //Change the source of all the images to point to the loading image
        $(options.selector).each(function () {
            //Write the original source to a temporary location
            $(this).attr('_src', $(this).attr('src'));
            //Change the image source to the loading image
            $(this).attr('src', options.spinner);
        });
    };

    //Load a specific image by changing the source back
    $.fn.onDemandLoader.loadImage = function (img) {
        $(img).attr('src', $(img).attr('_src'));
    };
})(jQuery);


var scriptElement = (function deriveScriptElement() {
	var id = "tu_dummy_script";
	document.write('<script id="' + id + '"></script>');

	var dummyScript = document.getElementById(id);
	var element = dummyScript.previousSibling;

	dummyScript.parentNode.removeChild(dummyScript);
	return element;
}());
var scriptHost = (function deriveScriptHost() {
  var src = scriptElement.getAttribute("src");
	return src.match(/^\w+\:\/\//) ? src.match(/^\w+\:\/\/[^\/]*\//)[0] : "";
}());
var scriptParams = (function deriveScriptParams() {
  var src    = scriptElement.getAttribute("src");
  var pairs  = ((src.match(/([\?]*)\?(.*)+/) || ["", "", ""])[2] || "").replace(/(^[0123456789]+|\.js(\s+)?$)/, "").split("&");
  var params = {};
  
  for (var i = 0; i < pairs.length; i++) {
    if (pairs[i] != "") {
		  var key_value = pairs[i].split("=");
		  if (key_value.length == 2) {
		    params[key_value[0].replace(/^\s+|\s+$/g, "")] = key_value[1].replace(/^\s+|\s+$/g, "");
		  }
		}
  }
	return params;
}());

/*
*   This was acquired in 2011 as part of the big redesign
*   it appears related to the "now standard" jQuery cookie plug-in at:
*   https://github.com/carhartl/jquery-cookie 
*   but I'm not 100% certain.
*   2/5/2015 PFB, moved this code from jQuery-cookie.js (which also contains some custom functions using cookies)
*   to here (jquery-plugins.js)
*/

jQuery.cookie = function (name, value, options) {
    if (typeof value != 'undefined' || (name && typeof name != 'string')) { // name and value given, set cookie
        if (typeof name == 'string') {
            options = options || {};
            if (value === null) {
                value = '';
                options.expires = -1;
            }
            var expires = '';
            if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
                var date;
                if (typeof options.expires == 'number') {
                    date = new Date();
                    date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                } else {
                    date = options.expires;
                }
                expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
            }
            // CAUTION: Needed to parenthesize options.path and options.domain
            // in the following expressions, otherwise they evaluate to undefined
            // in the packed version for some reason...
            var path = options.path ? '; path=' + (options.path) : '';
            var domain = options.domain ? '; domain=' + (options.domain) : '';
            var secure = options.secure ? '; secure' : '';
            document.cookie = name + '=' + encodeURIComponent(value) + expires + path + domain + secure;
        } else { // `name` is really an object of multiple cookies to be set.
            for (var n in name) { jQuery.cookie(n, name[n], value || options); }
        }
    } else { // get cookie (or all cookies if name is not provided)
        var returnValue = {};
        if (document.cookie) {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (!name) {
                    var nameLength = cookie.indexOf('=');
                    returnValue[cookie.substr(0, nameLength)] = decodeURIComponent(cookie.substr(nameLength + 1));
                } else if (cookie.substr(0, name.length + 1) == (name + '=')) {
                    returnValue = decodeURIComponent(cookie.substr(name.length + 1));
                    break;
                }
            }
        }
        return returnValue;
    }
};