$(document).ready(function() {
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        var currentArea = $('.area').filter(function() {
            return scroll <= $(this).offset().top + $(this).height();
        });
        $('nav a').removeClass('active');
        $('nav a[href=#' + currentArea.attr('id') +']').addClass('active');
        //console.debug('nav a[href=#' + currentArea.attr('id') +']');
    });
});
