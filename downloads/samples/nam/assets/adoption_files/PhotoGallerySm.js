$(document).ready(function () {
    $('.photo-gallery').each(function (i) {
        $(this).cycle({
            fx: 'fade',
            //		nowrap:		1,	// set to 1 to prevent photo index wrap to first or last
            speed: 500,
            timeout: 0,
            prev: $('.prev').eq(i),
            next: $('.next').eq(i),
            width: 143,
            height: 177,
            after: function (curr, next, opts) {
                // Use of &nbsp; for photo-name table cell value prevents shifting of content below summary anchor
                if ($('.photo-name').eq(i).html(this.alt) != '') {
                    $('.photo-name').eq(i).html(this.alt);
                } else if ($(this).children("img").attr("alt") != '') {
                    $('.photo-name').eq(i).html($(this).children("img").attr("alt"));
                } else if ($(this).children("a").attr("title") != '') {
                    $('.photo-name').eq(i).html($(this).children("a").attr("title"));
                } else {
                    $('.photo-name').eq(i).html('&nbsp;');
                }
                if (opts.slideCount > 0) {
                    $('.photo-count').eq(i).html((opts.currSlide + 1) + ' of ' + opts.slideCount);
                } else {
                    $('.photo-count').eq(i).html('&nbsp;');
                    // Need to figure out a way to hide <- -> slide nav icons for 1 up photos or videos
                }
            }
        });
    });

    $('.photo-gallery').fadeIn();
});