$("#main_logo").delay(1000).fadeOut(1500,function(){
    $("#main_logo").load(function () { //avoiding blinking, wait until loaded
        $("#main_logo").fadeIn(1500);
    });
    $("#main_logo").attr("src","assets/img/purmosa_logo_home.png");
});

$('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') 
        || location.hostname == this.hostname) {

        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
           if (target.length) {
             $('html,body').animate({
                 scrollTop: target.offset().top
            }, 1000);
            return false;
        }
    }
});