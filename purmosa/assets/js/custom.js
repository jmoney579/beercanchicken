$("#main_logo").delay(1000).fadeOut(1500,function(){
    $("#main_logo").load(function () { //avoiding blinking, wait until loaded
        $("#main_logo").fadeIn(1500);
    });
    $("#main_logo").attr("src","assets/img/purmosa_logo_home.png");
});
