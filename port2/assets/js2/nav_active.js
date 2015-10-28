   $(document).ready(function () {
        $(function () {
            $('li a').click(function (e) {
                $('a').removeClass('active_nav');
                $(this).addClass('active_nav');
            });
        });
    });

/*$(document).ready(function setActive() {
  aObj = document.getElementById('nav').getElementsByTagName('a');
  for(i=0;i<aObj.length;i++) {
    if(document.location.href.indexOf(aObj[i].href)>=0) {
      aObj[i].className='active';
    }
  }
});

$(document).ready(function() {
	if(location.pathname != "index.html" || location.pathname !="index.html#home") {
		$('#nav a[href^="/' + location.pathname.split("/")[1] + '"]').addClass('active');
	} else $('#nav a:eq(0)').addClass('active');
});*/