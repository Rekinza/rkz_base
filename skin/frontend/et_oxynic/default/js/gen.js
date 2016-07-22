jQuery(document).ready(function(){
var menu_top =jQuery('.menu-nav').offset().top;
var product_height=jQuery('.products-grid .item').height();
var isLoadingMoreProducts=false;
var iniPageLoadedurl="";
jQuery(document).scroll(function(){

	if (jQuery(document).scrollTop()>menu_top){
	jQuery('.menu-nav').addClass("hang");
	}else{
	jQuery('.menu-nav').removeClass("hang");
	}
	if (jQuery('body').hasClass('cms-sellers')){
		
		if (jQuery('.footer').offset().top-jQuery(document).scrollTop()<2*product_height){
		iniPageLoadedUrl=jQuery('.next_page_url').last().attr('href');
		if (!isLoadingMoreProducts && iniPageLoadedUrl!="-1"){
			jQuery('#scrolling_loder').show();
			isLoadingMoreProducts=true;
			jQuery.ajax({url:iniPageLoadedUrl}).done(function(resp){
			var data=jQuery(resp);
			var nextPageBlock=data.find('.next_page_url');
			data=data.find('.products-grid').html();
			jQuery('#scrolling_loder').hide();
			jQuery('.products-grid').append(data);
			jQuery('.next_page_url').replaceWith(nextPageBlock);
			isLoadingMoreProducts=false;
			});;
		
		}
	
		}
		return;
	}
	if (jQuery('body').hasClass('cartmart-vendor-profile')){
		
		if (jQuery('.footer').offset().top-jQuery(document).scrollTop()<2*product_height){
		iniPageLoadedUrl=jQuery('.next_page_url').last().attr('href');
		if (!isLoadingMoreProducts && iniPageLoadedUrl!="-1"){
			jQuery('#scrolling_loder').show();
			isLoadingMoreProducts=true;
			jQuery.ajax({url:iniPageLoadedUrl}).done(function(resp){
			var data=jQuery(resp);
			var nextPageBlock=data.find('.next_page_url');
			data=data.find('.products-grid').html();
			jQuery('#scrolling_loder').hide();
			jQuery('.products-grid').append(data);
			jQuery('.next_page_url').replaceWith(nextPageBlock);
			isLoadingMoreProducts=false;
			});;
		
		}
	
		}
		return;
	}
	
	if (jQuery('#category-products-wrap').length==0){
	return;
	}
	if (jQuery('.footer').offset().top-jQuery(document).scrollTop()<2*product_height){
		iniPageLoadedUrl=jQuery('.next_page_url').last().attr('href');
		if (!isLoadingMoreProducts && iniPageLoadedUrl!="-1"){
			jQuery('#scrolling_loder').show();
			isLoadingMoreProducts=true;
			jQuery.ajax({url:iniPageLoadedUrl}).done(function(resp){
			var data=jQuery(resp.listing);
			var nextPageBlock=data.find('.next_page_url');
			data=data.find('.products-grid').html();
			jQuery('#scrolling_loder').hide();
			jQuery('.products-grid').append(data);
			jQuery('.next_page_url').replaceWith(nextPageBlock);
			isLoadingMoreProducts=false;
			});;
		
		}
	
	}
});
jQuery('input[name="billing[postcode]"]').on('change',function(){
if (!/^[0-9]*$/.test(jQuery(this).val())){
jQuery('input[name="billing[postcode]"]').val("");
alert("Please enter a valid pin code");
}
});
});
jQuery(document).ready(function(){
jQuery('#product_tab_nav li').click(function(){
if (jQuery('#product_tab_nav li').index(this)==2){
jQuery('.tab-content').addClass('no-border');
}else{
jQuery('.tab-content').removeClass('no-border');
}
});
});
