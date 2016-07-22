// predefine
var opConfig, optionsPrice, dConfig;
var skipTierPricePercentUpdate, bundle, taxCalcMethod, CACL_UNIT_BASE, CACL_ROW_BASE, CACL_TOTAL_BASE;

var debug = false, is_checkout = {}, isLoaded = null;

var isCheckout = window.location.href.match(/\/checkout\/cart\//),
    isPopup = !!window.opener && (typeof window.opener.setLocation != 'undefined'),
    isFrame = window.self !== window.top;

var log = function () {
    if (!debug) {
        return;
    }
    try{ console.log.apply(this, arguments) } catch(e) {};
};
var is_checkout_url = function(url, yes){
	return yes ? (is_checkout[url] = yes) : (url in is_checkout);
};
var ETA = (function () {
	var instance;
	var _options;
	function createInstance() {
		return new Object();
	}
	return {
		app: function () {
			if (!instance) {
				instance = createInstance();
			}
			return instance;
		},
		options: function(){
			if (!_options) {
				_options = Object.extend({
					modal_element: 'ajax-messagebox',
					loader_element: 'ajax-loading',
					
		        	is_ajax_compare_enabled: true,
		            is_ajax_checkout_enabled: true,
		            is_ajax_wishlist_enabled: true,
		            is_ajax_quickview_enabled: true,
		            
		            dim: {
		            	loading: {
		            		width: '260px',
		            		height: ''
		            	},
		            	confirm: {
		            		width: '320px',
		            		height: ''
		            	},
		            	options: {
		            		width: '360px',
		            		height: ''
		            	},
		            	iframe: {
		            		width: '800px',
		            		height: '488px'
		            	}
		            }
		            
		        }, window.ETA_Options || {});
			}
			return _options;
		},
		Loader: function(){
			var app = this.app();
			if (!('loader' in app)){
				app['loader'] = $(this.options().loader_element);
				if (!app['loader']) return false;
				var dialog = app['loader'].down('.dialog');
				dialog.setStyle(this.options().dim.loading);
			    var mousePrevent = function(ee){
			    	ee.preventDefault();
			    }
			    Event.observe(app['loader'], "mousewheel", mousePrevent, false);
			    Event.observe(app['loader'], "DOMMouseScroll", mousePrevent, false); // Firefox
			}
			return app['loader'];
		},
		Modal: function(cb){
			var app = this.app();
			if (!('modal' in app)){
				app['modal'] = $(this.options().modal_element);
				if (!cb && hideModal){
					cb = hideModal;
				}
				if (cb){
					Event.observe(app['modal'], "click", cb, false);
					Event.observe(app['modal'].down('.dialog'), "click", function(e){e.preventDefault()}, false);
					if (app['modal'].down('.xclose')){
						Event.observe(app['modal'].down('.xclose'), "click", cb.bind(app['modal']), false);
					}
				}
			}
			return app['modal'];
		}
	};
})();
	


function whichTransitionEvent(){
    var t;
    var el = document.createElement('fakeelement');
    var transitions = {
      'transition':'transitionend',
      'OTransition':'oTransitionEnd',
      'MozTransition':'transitionend',
      'WebkitTransition':'webkitTransitionEnd'
    }

    for(t in transitions){
        if( el.style[t] !== undefined ){
            return transitions[t];
        }
    }
    return false;
}


function setLocation( url ) {	
    
    if ( /*!isCheckout &&*/ url.match(/\/checkout\/cart\/add\//) || is_checkout_url(url) ) {
    	url.match(/\/checkout\/cart\/add\//) ? checkout_cart_add(url, 'url') : checkout_cart_add(url, 'options');
    } else if ( url.match(/catalog\/product_compare/g) ) {
        (window.top||window).catalog_product_compare(url);
    } else if ( url.match(/wishlist\/index/g) ) {
    	(window.top||window).handleWishlist(url, 'url');
    } else if ( url.match(/quickview\/index\/product/g) ) {
        handleQuickview(url);
    } else {
        window.location.href = url;
    }
}

var popWinFn = window.popWin ? window.popWin : fasle;
window.popWin = function (url, win, para) {
    hideModal();
    if ( url.match(/catalog\/product_compare\/index/g) ) {
        compare_popup = window.open(url, win, para);
    } else if (typeof popWinFn == 'function') {
    	popWinFn(url, win, para);
    }
};

/* checkout cart */
/* add */
function checkout_response_process(transport){
	if (transport.responseJSON){
		var response = transport.responseJSON;
		if (response.error) {
			showModal({
                message: response.error,
                actions: response.actions || false,
            });
		} else if (response.success) {
			try {
    			if (response.updates){
    				for (var query in response.updates){
    					if ($(query)){
    						$(query).update(response.updates[query]);
    					}
    				}
    			}
                
                // handle new urls
                handleUrls();
                
                if (response.message || response.options){
                	showModal(response);
                } else {
                	hideLoading();
                }
			} catch(e) {
			}
		}
	} else {
		// html process
	}
}
function checkout_cart_add(url, type) {
	log('checkout_cart_add', url, type);
	if (type == 'options') {
        new Ajax.Request( url, {
            method: 'POST',
            parameters: {
            	options: 'cart'
            },
            onFailure: function (t) {},
            onCreate: showLoading,
            onComplete: checkout_response_process
        });
	} else if (type == 'form') {
    	if (typeof(url)=='string' && $('product_addtocart_form')){
    		var f = $('product_addtocart_form');
    	} else {
    		var f = url;
    	}
        url = f.action || location.href;
        url = url.replace('checkout/cart', 'ajax/checkout_cart');
        var _updates = [];
    	if ($('ajaxcart')) _updates.push('ajaxcart');
    	if ($('top_links')) _updates.push('top.links');
    	if ($('checkout_cart')) _updates.push('checkout_cart');
        new Ajax.Request( url, {
            method: 'post',
            postBody: f.serialize(),
            parameters: {
            	updates: _updates
            },
            onFailure: function (t) {},
            onCreate: showLoading,
            onComplete: checkout_response_process
        });

    } else if (type == 'url') {
        log('URL before: ', url);
        url = url.replace('checkout/cart', 'ajax/checkout_cart');
        var _updates = [];
    	if ($('ajaxcart')) _updates.push('ajaxcart');
    	if ($('top_links')) _updates.push('top.links');
    	if ($('checkout_cart')) _updates.push('checkout_cart');
        new Ajax.Request( url, {
            method: 'post',
            parameters: {
            	options: 'cart',
            	updates: _updates
            },
            onFailure: function (t) {},
            onCreate: showLoading,
            onComplete: checkout_response_process
        });
    }
}

function checkout_cart_delete(url) {
    log('Remove product: ', url);
    url = url.replace('checkout/cart/delete', 'ajax/checkout_cart/delete');
    new Ajax.Request( url, {
        method: 'post',
        parameters: {
        	in_cart: !!$('checkout_cart') ? 1 : 0
        },
        onFailure: function (t) {},
        onCreate: showLoading,
        onComplete: checkout_response_process
    });
}

function catalog_product_compare(url, pop) {
    log('catalog_product_compare("'+url+'")', pop);
    url = url.replace('catalog/product_compare', 'ajax/catalog_product_compare');
    log('Request: ' + url);
    new Ajax.Request( url, {
        method: 'post',
        parameters: {
            compare_sidebar: !!$('compare_sidebar') ? 1 : 0
        },
        onFailure: function (t) {},
        onCreate: showLoading,
        onComplete: function (transport) {
        	if (pop) pop.location.reload();
        	if (transport.responseJSON){
        		var response = transport.responseJSON;
        		if (response.error) {
        			showModal({
                        message: response.error,
                        actions: response.actions || false,
                    });
        		} else if (response.success) {
                    if (response.compare_sidebar) {
                        $('compare_sidebar').replace(response.compare_sidebar);
                        handleUrls();
                    }
                    
                    if (response.message || response.options){
                    	showModal(response);
                    } else {
                    	hideLoading();
                    }
        		}
        	} else {
        	}
        }
    });
    
}

function handleWishlist(url, type) {
    log('Request: ' + url);
    if (type == 'form') {
    	if (typeof(url)=='string' && $('product_addtocart_form')){
    		var f = $('product_addtocart_form');
    	} else {
    		var f = url;
    	}
        url = f.action || location.href;
        url = url.replace('wishlist/index', 'ajax/wishlist');
    	new Ajax.Request( url, {
            method: 'post',
            parameters: {
                wishlist_sidebar: !!$('wishlist_sidebar') ? 1 : 0,
                ajax: 1
            },
            onCreate: showLoading,
            onFailure: function (t) {},
            onComplete: function (transport) {
            	if (transport.responseJSON){
            		var response = transport.responseJSON;
            		if (response.error) {
            			showModal({
                            message: response.error,
                            actions: response.actions || false,
                        });
            		} else if (response.success) {
            			if (response.updates){
            				for (var query in response.updates){
            					if ($(query)) {
            						if (query=='wishlist_sidebar'){
            							$(query).replace(response.updates[query]);
            						} else {
            							$(query).update(response.updates[query]);
            						}
            					}
            				}
            			}
                        
                        if (response.message || response.options){
                        	showModal(response);
                        } else {
                        	hideLoading();
                        }
            		}
            	} else {
            	}
            }
        });
	} else {
		url = url.replace('wishlist/index', 'ajax/wishlist');
		new Ajax.Request( url, {
	        method: 'post',
	        parameters: {
	        	wishlist_sidebar: !!$('wishlist_sidebar') ? 1 : 0,
	            ajax: 1
	        },
	        onCreate: showLoading,
	        onFailure: function (t) {},
	        onComplete: function (transport) {
	        	if (transport.responseJSON){
	        		var response = transport.responseJSON;
	        		if (response.error) {
	        			showModal({
	                        message: response.error,
	                        actions: response.actions || false,
	                    });
	        		} else if (response.success) {
	        			if (response.updates){
            				for (var query in response.updates){
            					if ($(query)) {
            						if (query=='wishlist_sidebar'){
            							$(query).replace(response.updates[query]);
            						} else {
            							$(query).update(response.updates[query]);
            						}
            					}
            				}
            			}
	                    
	                    if (response.message || response.options){
	                    	showModal(response);
	                    } else {
	                    	hideLoading();
	                    }
	        		}
	        	} else {
	        	}
	        }
	    });
	}
    
}

function iFrameHeight(el) {
    log('iFrameHeight', el);
    try {  
        var doc = el.contentDocument;
        var win = el.contentWindow;
        el.style.height = win.document.body.scrollHeight + 'px';
        el.style.width = win.document.body.scrollWidth + 'px';
    } catch (e) { log(e); }
}

function handleQuickview(url, type) {
	log('handleQuickview('+url+')', type);
    if (isLoaded == url){
    	showModal({
            iframe: true,
        });
    	return true;
    } else if (isLoaded){
    	ETA.Modal().down('.iframe').update('');
    }
    try {
    	var QV_FRAME = new Element('iframe', { id: 'qvframe', 'class': 'qvframe', 'scrolling': 'no', 'seamless': 'seamless' });
        QV_FRAME.observe('load', function (e) {
        	ETA.Modal().removeClassName('invisible');
            showModal({
                iframe: true,
                close: true
            });
            isLoaded = url;
            iFrameHeight(this);
        });
        showLoading();
        QV_FRAME.src = url;
        ETA.Modal().addClassName('invisible');
        ETA.Modal().down('.iframe').insert(QV_FRAME);
    } catch (e) {
    }
}

function handleUrls() {
    log("handleUrls()");
    $$('a[href]').each(function (a) {
    	if (ETA.options().is_ajax_checkout_enabled
                && a.href.match(/\/checkout\/cart\/delete\//)
                && !a.href.match('javascript:checkout_cart_delete') ) {
            a.href='javascript:checkout_cart_delete("' + a.href + '");';
        }
   
        if (ETA.options().is_ajax_compare_enabled
                && a.href.match(/\/catalog\/product_compare\/remove\//g) 
                && !a.href.match('javascript:setLocation') ) {
            a.href='javascript:setLocation("' + a.href + '");';
        }
        
        if (ETA.options().is_ajax_compare_enabled
                && a.href.match(/\/catalog\/product_compare\/clear\//g) 
                && !a.href.match('javascript:setLocation') ) {
            a.href='javascript:setLocation("' + a.href + '");';
        }
        
        if (ETA.options().is_ajax_checkout_enabled
                && a.href.match(/\/checkout\/cart\/add\//)
                && !a.href.match('javascript:setLocation') ) {
            a.href='javascript:setLocation("' + a.href + '");';
        }
   
        if (ETA.options().is_ajax_compare_enabled
                && a.href.match(/\/catalog\/product_compare\/add\//g) 
                && !a.href.match('javascript:setLocation') ) {
            a.href='javascript:setLocation("' + a.href + '");';
        }
        
        if (ETA.options().is_ajax_wishlist_enabled
                && a.href.match(/\/wishlist\/index\/add\//g) 
                && !a.href.match('javascript:setLocation') ) {
            a.href='javascript:setLocation("' + a.href + '");';
            //log('handle2: ', a.href);
        }
        
        if (ETA.options().is_ajax_quickview_enabled
                && a.href.match(/\/quickview\/index\/product\//g) 
                && !a.href.match('javascript:setLocation') ) {
            a.href='javascript:setLocation("' + a.href + '");';
        }
        
    });
    
    if (!ETA.options().is_ajax_checkout_enabled) return;
    $$('button[onclick]').each(function (btn) {
    	if ( btn.match('.btn-cart') && btn.getAttribute('onclick').match('setLocation') ){
    		var purl = btn.getAttribute('onclick').replace(/setLocation\(['|"](.+)['|"]\)/, '$1');
    		if (purl) is_checkout_url(purl, 1);
    	}
    });
}
function handleComparePopup(){
	if (!isPopup) return;
	log('handleComparePopup()');
	
	window.removeItem = function(url) {
		window.opener.catalog_product_compare(url, window);
	}
	window.setPLocation = function (url, setFocus) {
		if (setFocus) {
	        window.opener.focus();
	    }
		window.opener.setLocation(url);	    
	};
	window.setLocation = function(url){
		window.opener.setLocation(url);
	}
}

function handleQuickviewFrame(){
	if (!isFrame) return;
	log('handleQuickviewFrame()');
}

function showLoading() {
	var loader = ETA.Loader();
	if (loader){
		hideModal();
		loader.addClassName('visible');
		loader.autohide = setTimeout(hideLoading, 15*1000);
		return true;
	}
	return false;
}

function hideLoading() {
	var loader = ETA.Loader();
	if (loader && loader.hasClassName('visible')) {
		loader.removeClassName('visible');
		if (loader.autohide){
			clearTimeout(loader.autohide);
			loader.autohide = null;
			delete loader.autohide;
		}
    	return true;
    }
	return false;
}

function updateModal(modal, mo){
	if (!modal || !mo) return false;
	log('updateModal()');
	try {
		var resized = false, dialog = modal.down('.dialog');
		if (mo.dimension){
			dialog.setStyle(mo.dimension);
			resized = true;
		}
		
		if (mo.actions) {
			modal.addClassName('has-actions');
			modal.removeClassName('no-actions');
			if (!resized) {
				dialog.setStyle(ETA.options().dim.confirm);
				resized = true;
			}
			mo.actions===true || modal.down('.actions').update(mo.actions);
		} else {
			modal.addClassName('no-actions');
			modal.removeClassName('has-actions');
		}
		
		if (mo.message) {
			modal.addClassName('has-message');
			modal.removeClassName('no-message');
			if (!resized) {
				dialog.setStyle(ETA.options().dim.confirm);
				resized = true;
			}
			mo.message===true || modal.down('.message').update(mo.message);
		} else {
			modal.addClassName('no-message');
			modal.removeClassName('has-message');
		}
		
		if (mo.options) {
			modal.addClassName('has-options');
			modal.removeClassName('no-options');
			if (!resized) {
				dialog.setStyle(ETA.options().dim.options);
				resized = true;
			}
			mo.options===true || modal.down('.options').update(mo.options);
		} else {
			modal.addClassName('no-options');
			modal.removeClassName('has-options');
		}
		
		if (mo.iframe) {
			modal.addClassName('has-iframe');
			modal.removeClassName('no-iframe');
			if (!resized) {
				dialog.setStyle(ETA.options().dim.iframe);
				resized = true;
			}
		} else {
			modal.addClassName('no-iframe');
			modal.removeClassName('has-iframe');
		}
		
		if (mo.close) {
			modal.removeClassName('no-close');
		} else {
			modal.addClassName('no-close');
		}
		
	} catch(e){
		log(e);
	}
	return true;
}

function hideModal(e){
	log('hideModal()');
	var modal = ETA.Modal();
	if (e) {
		if (e.originalTarget && e.originalTarget.up('.content')) return false;
		if (e.defaultPrevented) return false;
		e.preventDefault();
	}
    if (modal && modal.hasClassName('visible')) {
    	modal.removeClassName('visible');
    }
    return false;
}

function showModal(mo) {
    log('showModal()');
    mo = Object.extend({
    	close: true,
    	iframe: false,
    	options: false,
        message: false,
        actions: false,
        auto_close: false,
        auto_close_interval: 0
    }, mo);
    
    var modal = ETA.Modal();
    if (modal) {
    	updateModal(modal, mo);
    	hideLoading();
    	modal.addClassName('visible');
    	if (mo.auto_close && mo.auto_close_interval) {
    		setTimeout(hideModal, mo.auto_close_interval);
    	}
    }
}

/**
 * ERROR after ajax update.
 *  
 */
function validateDownloadableCallback(elmId, result) {
    var container = $('downloadable-links-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}

Product.Options = Class.create();
Product.Options.prototype = {
    initialize : function(config) {
        this.config = config;
        this.reloadPrice();
        document.observe("dom:loaded", this.reloadPrice.bind(this));
    },
    reloadPrice : function() {
        var config = this.config;
        var skipIds = [];
        $$('body .product-custom-option').each(function(element){
            var optionId = 0;
            element.name.sub(/[0-9]+/, function(match){
                optionId = parseInt(match[0], 10);
            });
            if (config[optionId]) {
                var configOptions = config[optionId];
                var curConfig = {price: 0};
                if (element.type == 'checkbox' || element.type == 'radio') {
                    if (element.checked) {
                        if (typeof configOptions[element.getValue()] != 'undefined') {
                            curConfig = configOptions[element.getValue()];
                        }
                    }
                } else if(element.hasClassName('datetime-picker') && !skipIds.include(optionId)) {
                    dateSelected = true;
                    $$('.product-custom-option[id^="options_' + optionId + '"]').each(function(dt){
                        if (dt.getValue() == '') {
                            dateSelected = false;
                        }
                    });
                    if (dateSelected) {
                        curConfig = configOptions;
                        skipIds[optionId] = optionId;
                    }
                } else if(element.type == 'select-one' || element.type == 'select-multiple') {
                    if ('options' in element) {
                        $A(element.options).each(function(selectOption){
                            if ('selected' in selectOption && selectOption.selected) {
                                if (typeof(configOptions[selectOption.value]) != 'undefined') {
                                    curConfig = configOptions[selectOption.value];
                                }
                            }
                        });
                    }
                } else {
                    if (element.getValue().strip() != '') {
                        curConfig = configOptions;
                    }
                }
                if(element.type == 'select-multiple' && ('options' in element)) {
                    $A(element.options).each(function(selectOption) {
                        if (('selected' in selectOption) && typeof(configOptions[selectOption.value]) != 'undefined') {
                            if (selectOption.selected) {
                                curConfig = configOptions[selectOption.value];
                            } else {
                                curConfig = {price: 0};
                            }
                            optionsPrice.addCustomPrices(optionId + '-' + selectOption.value, curConfig);
                            optionsPrice.reload();
                        }
                    });
                } else {
                    optionsPrice.addCustomPrices(element.id || optionId, curConfig);
                    optionsPrice.reload();
                }
            }
        });
    }
}
function validateOptionsCallback(elmId, result) {
    var container = $(elmId).up('ul.options-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}

document.observe("dom:loaded", function () {
    log("dom:loaded");
    handleUrls();
    handleComparePopup();
    handleQuickviewFrame();
});