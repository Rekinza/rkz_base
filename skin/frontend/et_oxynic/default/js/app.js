jQuery(function($){
	var isBootstrapEvent = false;
    if (window.jQuery) {
        var all = jQuery('*');
        jQuery.each(['hide.bs.dropdown', 
            'hide.bs.collapse', 
            'hide.bs.modal', 
            'hide.bs.tooltip'], function(index, eventName) {
            all.on(eventName, function( event ) {
                isBootstrapEvent = true;
            });
        });
    }
    
    var originalHide = Element.hide;
    Element.addMethods({
        hide: function(element) {
            if(isBootstrapEvent) {
                isBootstrapEvent = false;
                return element;
            }
            return originalHide(element);
        }
    });

	//home slide bar menu left
	$('.sidebar-menu-showmore i').click(function(){
		if( $('.left-sidebar-menu').hasClass('menu-full')){
			$('.left-sidebar-menu').removeClass('menu-full');
		} else {
			$('.left-sidebar-menu').addClass('menu-full');
		}		
	});
	
	//slide about us	
	jQuery('.slider-happy-client').owlCarousel({
		pagination: false,
		center: false,
		nav: true,
		loop: true,
		margin: 0,
		navText: [ '', '' ],
		slideBy: 1,
		autoplay: false,
		autoplayTimeout: 2500,
		autoplayHoverPause: true,
		autoplaySpeed: 800,
		startPosition: 0, 
		responsive:{
			0:{
				items:1
			},
			480:{
				items:1
			},
			768:{
				items:1
			},
			1200:{
				items:1
			}
		}
	});	
	
	
try{
	
	//slide home-happy-client	
	jQuery('.home-happy-client').owlCarousel({
		pagination: false,
		center: false,
		nav: true,
		loop: true,
		margin: 0,
		navText: [ '', '' ],
		slideBy: 1,
		autoplay: false,
		autoplayTimeout: 2500,
		autoplayHoverPause: true,
		autoplaySpeed: 800,
		startPosition: 0, 
		responsive:{
			0:{
				items:1
			},
			480:{
				items:1
			},
			768:{
				items:1
			},
			1200:{
				items:1
			}
		}
	});	
	
}catch(eee){console.log(eee);}
try{
	//slide brand
	$('.block-brand-content').owlCarousel({
		pagination: false,
		center: false,
		nav: true,
		loop: true,
		margin: 20,
		navText: [ '', '' ],
		slideBy: 1,
		autoplay: false,
		autoplayTimeout: 2500,
		autoplayHoverPause: true,
		autoplaySpeed: 800,
		startPosition: 0, 
		responsive:{
			0:{
				items:1
			},
			480:{
				items:1
			},
			768:{
				items:4
			},
			1200:{
				items:6
			}
		}
	});	
	
}catch(eee){console.log(eee);}

try{
//releated products	 
	$('.releated-product-list').owlCarousel({
		pagination: false,
		center: false,
		nav: true,
		navRewind: false,
		loop: false,
		margin: 30,
		navText: [ '', '' ],
		slideBy: 1,
		autoplay: false,
		autoplayTimeout: 2500,
		autoplayHoverPause: true,
		autoplaySpeed: 800,
		startPosition: 0, 
		responsive:{
			0:{
				items:1
			},
			480:{
				items:1
			},
			768:{
				items:3
			},
			1200:{
				items:4
			}
		}
	});
	
}catch(eee){console.log(eee);}
try{
//upsell products 	
	$('.up-sell-list').owlCarousel({
		pagination: false,
		center: false,
		nav: true,
		navRewind: false,
		loop: false,
		margin: 30,
		navText: [ '', '' ],
		slideBy: 1,
		autoplay: false,
		autoplayTimeout: 2500,
		autoplayHoverPause: true,
		autoplaySpeed: 800,
		startPosition: 0, 
		responsive:{
			0:{
				items:1
			},
			480:{
				items:1
			},
			768:{
				items:3
			},
			1200:{
				items:4
			}
		}
	});
}catch(eee){
	console.log(eee);
}	

	if ( $('#meganav').length ){
		var dropdowns = $('#meganav .level-1>.dropdown-menu');
		var cont = $('#meganav').parents('.header-menu').find('>.container');
		var reposTimeout, locking = false;
		var repos = function(){
			var cl = cont.offset().left, cw = cont.outerWidth(), num = dropdowns.length;
			var ow0 = cont.data('ow0'), ow1 = cont.data('ow1');
			if (typeof ow0 == 'undefined') {
				cont.data('ow0', cw);
			} else {
				if (typeof ow1 == 'undefined') {
					ow1 = ow0;
				}
				if ( ow1 == cw ){
					return;
				}
				cont.data('ow1', cw);
			}

			dropdowns.each(function(){
				var dn = $(this);
				if (dn.parent().hasClass('dropdown-full')) return;
				// restore left, width 
				if (dn.data('cssleft')) {
					dn.css('left', dn.data('cssleft'));
				} else {
					dn.data('cssleft', dn.css('left'));
				}
				if (dn.data('csswidth')) {
					dn.css('width', dn.data('csswidth'));
				} else {
					dn.data('csswidth', dn.css('width'));
				}

				var dnoLeft = dn.offset().left;
				if (dn.outerWidth()>cw){
					dn.css('width', cw);
				}

				if ( dn.outerWidth() >= cw ){
					var toleft = (cl-dnoLeft)-(dn.outerWidth() - cw)/2;
				} else if (dnoLeft < cl) {
					var toleft = cl-dnoLeft;
				} else if (dnoLeft+dn.outerWidth()>cl+cw){
					var toleft = cl - (0-cw) - dnoLeft - dn.outerWidth();
				}
				if (typeof toleft != 'undefined'){
					dn.css('left', toleft);
				}
				locking == --num == 0;
			});
		}
		$(repos);
		$(window).on('resize orientationchange', function(){
			if (reposTimeout){
				clearTimeout(reposTimeout);
				reposTimeout = null;
			}
			reposTimeout = setTimeout(repos, 30);
		});
	}
	
	jQuery(window).load(function(){
		jQuery(document).trigger('product-media-loaded');
	});
});	