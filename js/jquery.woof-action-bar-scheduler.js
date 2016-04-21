/*!
 * jQuery Woof Action Bar
 * Copyright (c) 2016 Jacob Vega Canote @ WEBDOGS.COM
 * Version: 1.0
 * Requires: jQuery v1.7 or later
 */
;(function($)
{
	$.fn.WoofActionBar = function(options){

		// Find out about the website itself
		var origHtmlMargin = parseFloat($('html').css('margin-top')); // Get the original margin-top of the HTML element so we can take that into account
		var origHtmlBkColor = $('html').css('background-color'); 
		var origBodyBkColor = $('body').css('background-color'); 
		var origHtmlImage = $('html').css('background-image'); 
		var origBodyImage = $('body').css('background-image'); 

		var HtmlMargin;
		var HtmlBkColor;
		var BodyBkColor;
		var HtmlImage;
		var BodyImage;
		var HtmlBk;
		var BodyBk;

		var DefaultBkColor = ( WABS_setting.WABS_backgroundColor == "" ) ? false : WABS_setting.WABS_backgroundColor;
		var HtmlBackground;

		var bannerState = [];
		var bannerHeight;
		var bannerDistance;
		var scrollTop;
		var cookieName = 'WABS_action_bar_closed_' + WABS_setting.WABS_uniqueID;

		if(typeof options == 'string'){ // If they specified a command (like "show" or "hide")
			bannerHeight = $(WABS_setting.WABS_ID).height(); // Accomodate different sized banners
			if(typeof opts == 'undefined')
				var opts = $.fn.WoofActionBar.defaults;
			switch(options){
				case 'show':
					if(!$(WABS_setting.WABS_ID).hasClass('shown')){
						showBanner();
					}
					return false;
				case 'hide':
					if($(WABS_setting.WABS_ID).hasClass('shown')){
						origHtmlMargin = origHtmlMargin-bannerHeight; // The "original" value actually includes the banner's added margin when this is called so we need to take it out
						closeBanner();
					}
					return false;
			}
		}else{ // Check for options
			var opts = $.extend({}, $.fn.WoofActionBar.defaults, options);
		}


		function logBannerState( args ){
			if ( ! opts.debug ) return;
			console.log(args,bannerState);
		}
		function isBannerState( match ){
			return ( bannerState.indexOf( match ) !== -1 );
		}
		function createBanner(){

			$('html').append( WABS_setting.WABS_HTML );	
			$('body').prepend( WABS_setting.WABS_topSpacer );

			$(WABS_setting.WABS_ID).css( 'zIndex', opts.zIndex );

			HtmlBackground = bannerBackgroundColor();

			bannerState = ['live'];

			logBannerState({createBanner: 'showBanner ' + showBanner() });

			// dropBannerTimer = setTimeout( showBanner, 800 );

			$('.wabs_close_bar').on('click',function(){
				bannerState = [];
				closeBanner();
				return false;
			});
			$(window).on('scroll',function(){

				if( ! isBannerState('live') ){ return false; }

				scrollTop = parseFloat($(document).scrollTop());
				bannerHeight = parseFloat($(WABS_setting.WABS_ID).height());
				// logBannerState({ scrollTop: scrollTop, bannerHeight: bannerHeight });

			    if ( scrollTop >= bannerHeight ) {
			    	toggleBanner('out');
			    } else {
			        toggleBanner('in');
			    }
			}).on('resize', function(){
				// logBannerState({ isBannerStateLive: isBannerState('live'), isBannerStateOut: isBannerState('out'), bannerState: bannerState.indexOf( 'out' ) });

				if( ! isBannerState('live') || isBannerState('out') ){ return false; }

				bannerHeight = $(WABS_setting.WABS_ID).height();
				HtmlMargin = parseFloat($('html').css('margin-top'));
				bannerDistance = String( bannerHeight - HtmlMargin );

				$('.wabs_top_spacer').height( bannerHeight ).hide();
				$(WABS_setting.WABS_ID).transition({ y: "-" + bannerHeight + 'px', duration: 1 }).parents('html').transition({ y: bannerDistance + 'px', duration: 1  });
			}).on('load', function(){
				$(window).trigger('resize');
			});
		}
		function showBanner(){

			logBannerState({ showBanner: 'indexOf "live" ' + isBannerState('live') });

			if( ! isBannerState('live') ){ return false; }

			bannerState = ['live','in'];

			bannerHeight   = $(WABS_setting.WABS_ID).height();
			HtmlMargin = parseFloat($('html').css('margin-top'));
			bannerDistance = String( bannerHeight - HtmlMargin );

			logBannerState({ showBanner: 'HtmlMargin ' + HtmlMargin + ", HtmlColor " + origHtmlBkColor });

			$(WABS_setting.WABS_ID).fadeIn().stop().transition({ y: "-" + bannerHeight + 'px', easing: 'snap', duration: opts.speedIn }).addClass('shown');
			$('html').css( 'background', HtmlBackground ).transition({ y:  bannerDistance  + 'px', easing: 'snap', duration: opts.speedIn  });
			// return true;
		}
		function bannerBackgroundColor(){
			origHtmlBkColor = $('html').css('background-color'); 
			origBodyBkColor = $('body').css('background-color'); 
			origHtmlImage = $('html').css('background-image'); 
			origBodyImage = $('body').css('background-image'); 

			BodyBkColor = ( origBodyBkColor == 'rgba(0, 0, 0, 0)' ) ? false : origBodyBkColor;
			HtmlBkColor = ( origHtmlBkColor == 'rgba(0, 0, 0, 0)' ) ? false : origHtmlBkColor;
			BodyImage   = ( origBodyImage == 'none' ) ? false : origBodyImage;
			HtmlImage   = ( origHtmlImage == 'none' ) ? false : origHtmlImage;
			BodyBK      = ( BodyBkColor || BodyImage ) ? $('body').css('background') : false; 
			HtmlBK      = ( HtmlBkColor || HtmlImage ) ? $('html').css('background') : false; 
			
			return ( ! HtmlBK && BodyBK && DefaultBkColor ) ? DefaultBkColor : ( ( BodyBK ) ? BodyBK : origHtmlBkColor );
		}
		function toggleBanner( state ){
			if( ! isBannerState('live') ){ return false; }

			// logBannerState({toggleBanner:'live'});

			bannerHeight   = $(WABS_setting.WABS_ID).height();
			HtmlMargin = parseFloat($('html').css('margin-top'));
			bannerDistance = String( bannerHeight - HtmlMargin );

			if( isBannerState('in') && state == 'out' ){

				bannerState = ['live','out'];

				// logBannerState({toggleBanner:'in'});

				$('html').css({transform:'', background: '' });
				$('.wabs_top_spacer').height( bannerHeight ).show();
				$(WABS_setting.WABS_ID).stop().css('transform','translate(0px, -100%)');

			} else if( isBannerState('out') && state == 'in'  ){

				bannerState = ['live','in'];

				// logBannerState({toggleBanner:'out'});

				$('html').css( 'background', HtmlBackground ).transition({ y: bannerDistance + 'px', duration: 0 });
				$('.wabs_top_spacer').height( bannerHeight ).hide();
				$(WABS_setting.WABS_ID).stop().transition({ y: "-" + bannerHeight + 'px', duration: 0 });
			}
		}
		function closeBanner(){
			bannerState = [];
			$(WABS_setting.WABS_ID).stop().removeClass('shown');
			$('html').transition({ y: '0px', easing: 'snap', duration: opts.speedOut }, function(){ $('html').css('transform',''); $(WABS_setting.WABS_ID).fadeOut(); });
			
			if(opts.behavior=='close'){
				setCookie( cookieName,'true', opts.daysHidden );
			}
		}
		function setCookie(name,value,exdays){
			var exdate = new Date();
			exdate.setDate(exdate.getDate()+exdays);
			var value=escape(value)+((exdays==null)?'':'; expires='+exdate.toUTCString());
			document.cookie=name+'='+value+'; path=/;';
		}
		function getCookie(name){
			var i,x,y,ARRcookies = document.cookie.split(";");
			for(i=0;i<ARRcookies.length;i++){
				x = ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
				y = ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
				x = x.replace(/^\s+|\s+$/g,"");
				if(x==name){
					return unescape(y);
				}
			}
		}
		// bannerState = ['live'];
		if( typeof getCookie( cookieName) == 'undefined' || opts.behavior=='toggle' ){ // Show if debug. Show if iPhone/iPad in Mobile Safari & don't have cookies already.			
			bannerState = ['live'];

			// logBannerState({init:'noCookie'});

			createBanner();
		}

	},

	// override these globally if you like (they are all optional)
	$.fn.WoofActionBar.defaults = WABS_setting.WABS_options;
	/*{
		speedIn: 600, // Show animation speed of the banner
		speedOut: 400, // Close animation speed of the banner
		daysHidden: 15, // Duration to hide the banner after being closed (0 = always show banner)
		daysReminder: 90, // Duration to hide the banner after "Save" is clicked *separate from when the close button is clicked* (0 = always show banner)
		debug: false // Whether or not it should always be shown (even for non-iOS devices & if cookies have previously been set) *This is helpful for testing and/or previewing
	};*/
})(jQuery);

jQuery().WoofActionBar();
