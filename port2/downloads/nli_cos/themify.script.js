;// Themify Theme Scripts - http://themify.me/

(function($){

	$(document).ready(function(){


		/////////////////////////////////////////////
		// Scroll to top 							
		/////////////////////////////////////////////
		$('.back-top a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		
		/////////////////////////////////////////////
		// Toggle menu on mobile 							
		/////////////////////////////////////////////
		$("#menu-icon").click(function(){
			$("#headerwrap #main-nav").fadeToggle();
			$("#headerwrap #searchform").hide();
			$(this).toggleClass("active");
		});

		/////////////////////////////////////////////
		// Toggle searchform on mobile 							
		/////////////////////////////////////////////
		$("#search-icon").click(function(){
			$("#headerwrap #searchform").fadeToggle();
			$("#headerwrap #main-nav").hide();
			$('#headerwrap #s');
			$(this).toggleClass("active");
		});
		
		// Set path to audio player
		AudioPlayer.setup(themifyScript.audioPlayer, {
			width: '90%',
			transparentpagebg: 'yes'
		});

		// Lightbox / Fullscreen initialization ///////////
		if(typeof ThemifyGallery !== 'undefined'){ ThemifyGallery.init({'context': $(themifyScript.lightboxContext)}); }
	});

	// Initialize Audio Player
	function doAudio(context){
		$('.f-embed-audio', context).each(function(index){
			$this = $(this);
			f_id = $this.attr('id');
			
			if('yes' == $this.data('html5incompl')){
				up = $this.parent().parent();
				
				AudioPlayer.embed( f_id, { soundFile: $this.data('src') } );
				
				if($.browser.mozilla) {
					$('audio').remove();
					$("div.audio_wrap div").show()
				} else {
					$("div.audio_wrap div *").remove();
				}
			}
		});
	}

	function isotopeBuilder($container) {
		var isoCols = parseInt($container.width() / $('.post', $container).width()),
			isoColW = $container.width() / isoCols;

		$('.post', $container).wrap('<div class="new-items" />');

		$container.isotope({
			masonry: {
				columnWidth: isoColW
			},
			itemSelector : '.post',
			transformsEnabled : false,
			resizable: false,
			animationEngine: 'jquery',
		});
		$(window).smartresize(function(){
			isoColW = parseInt($container.width() / isoCols);
			$container.isotope({
				masonry: {
					columnWidth: isoColW
				}
			}).isotope('reLayout');
		});
	}

	$(window).load(function() {
		
		var $container = $('#loops-wrapper');
		
		// For audio player
		doAudio(document);

		if(typeof $.fn.isotope !== 'undefined'){
			
			// MAIN THEME LAYOUT
			if( (! $('body.list-post').length > 0) ){
				var isoCols = parseInt($container.width() / $('#loops-wrapper .post').width()),
					isoColW = $container.width() / isoCols;

				// Hack weird messing layout
				$('#loops-wrapper .post').wrap('<div class="new-items" />');

				// isotope init
				$container.isotope({
					masonry: {
						columnWidth: isoColW
					},
					itemSelector : '.post',
					transformsEnabled : false,
					resizable: false,
					animationEngine: 'jquery',
				});
				$(window).smartresize(function(){
					isoColW = parseInt($container.width() / isoCols);
					$container.isotope({
						masonry: {
							columnWidth: isoColW
						}
					}).isotope('reLayout');
				});
				$(window).resize();
			}

			// BUILDER ISOTOPE LAYOUT
			var builderContainer = ['.loops-wrapper.grid4', '.loops-wrapper.grid3', '.loops-wrapper.grid2'];
			$.each(builderContainer, function(i,v){
				$(v).each(function(){
					isotopeBuilder($(this));
				});
			});
			
			// Get max pages for regular category pages and home
			var scrollMaxPages = themifyScript.maxPages;
			// Get max pages for Query Category pages
			if( typeof qp_max_pages != 'undefined')
				scrollMaxPages = qp_max_pages;

			// infinite scroll
			$container.infinitescroll({
				navSelector  : '#load-more a', 		// selector for the paged navigation
				nextSelector : '#load-more a', 		// selector for the NEXT link (to page 2)
				itemSelector : '#content .post', 	// selector for all items you'll retrieve
				loadingText  : '',
				donetext     : '',
				loading 	 : { img: themifyScript.loadingImg },
				maxPage      : scrollMaxPages,
				behavior	 : 'auto' != themifyScript.autoInfinite? 'twitter' : '',
				pathParse 	 : function (path, nextPage) {
					return path.match(/^(.*?)\b2\b(?!.*\b2\b)(.*?$)/).slice(1);
				}
			}, function(newElements) {
				// call Isotope for new elements
				var $newElems = $(newElements).wrap('<div class="new-items" />').hide();
				$newElems.imagesLoaded(function(){
					$newElems.fadeIn();

					// For audio player
					doAudio($newElems);

					if( (! $('body.list-post').length > 0) ){
						$container.isotope('appended', $newElems );
					}

					$('#infscr-loading').fadeOut('normal');
					if( 1 == scrollMaxPages ){
						$('#load-more, #infscr-loading').remove();
					}
					
					// Apply lightbox/fullscreen gallery to new items
					if(typeof ThemifyGallery !== 'undefined'){ ThemifyGallery.init({'context': $(themifyScript.lightboxContext)}); }
				});
				//$container.isotope('insert', $(newElements)).delay(400).isotope('reLayout');
				scrollMaxPages = scrollMaxPages - 1;
				if( 1 < scrollMaxPages && 'auto' != themifyScript.autoInfinite)
					$('#load-more a').show();
			});

			// disable auto infinite scroll based on user selection
			if( 'auto' == themifyScript.autoInfinite ){
				$('#load-more, #load-more a').hide();
			}

		} // typeof fn.isotope
	});

})(jQuery);