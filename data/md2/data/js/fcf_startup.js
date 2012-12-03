/**

FCF>V>SITE

**/

$(function(){
	if("undefined" == typeof(fcf)) fcf = {};
	if("undefined" == typeof(fcf.s)) fcf.s = {};
});

/**
* hoverIntent is similar to jQuery's built-in "hover" function except that
* instead of firing the onMouseOver event immediately, hoverIntent checks
* to see if the user's mouse has slowed down (beneath the sensitivity
* threshold) before firing the onMouseOver event.
* 
* hoverIntent r6 // 2011.02.26 // jQuery 1.5.1+
* <http://cherne.net/brian/resources/jquery.hoverIntent.html>
* 
* hoverIntent is currently available for use in all personal or commercial 
* projects under both MIT and GPL licenses. This means that you can choose 
* the license that best suits your project, and use it accordingly.
* 
* // basic usage (just like .hover) receives onMouseOver and onMouseOut functions
* $("ul li").hoverIntent( showNav , hideNav );
* 
* // advanced usage receives configuration object only
* $("ul li").hoverIntent({
*	sensitivity: 7, // number = sensitivity threshold (must be 1 or higher)
*	interval: 100,   // number = milliseconds of polling interval
*	over: showNav,  // function = onMouseOver callback (required)
*	timeout: 0,   // number = milliseconds delay before onMouseOut function call
*	out: hideNav    // function = onMouseOut callback (required)
* });
* 
* @param  f  onMouseOver function || An object with configuration options
* @param  g  onMouseOut function  || Nothing (use configuration options object)
* @author    Brian Cherne brian(at)cherne(dot)net
*/
(function($) {
	$.fn.hoverIntent = function(f,g) {
		// default configuration options
		var cfg = {
			sensitivity: 7,
			interval: 100,
			timeout: 0
		};
		// override configuration options with user supplied object
		cfg = $.extend(cfg, g ? { over: f, out: g } : f );

		// instantiate variables
		// cX, cY = current X and Y position of mouse, updated by mousemove event
		// pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
		var cX, cY, pX, pY;

		// A private function for getting mouse position
		var track = function(ev) {
			cX = ev.pageX;
			cY = ev.pageY;
		};

		// A private function for comparing current and previous mouse position
		var compare = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			// compare mouse positions to see if they've crossed the threshold
			if ( ( Math.abs(pX-cX) + Math.abs(pY-cY) ) < cfg.sensitivity ) {
				$(ob).unbind("mousemove",track);
				// set hoverIntent state to true (so mouseOut can be called)
				ob.hoverIntent_s = 1;
				return cfg.over.apply(ob,[ev]);
			} else {
				// set previous coordinates for next time
				pX = cX; pY = cY;
				// use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
				ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
			}
		};

		// A private function for delaying the mouseOut function
		var delay = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			ob.hoverIntent_s = 0;
			return cfg.out.apply(ob,[ev]);
		};

		// A private function for handling mouse 'hovering'
		var handleHover = function(e) {
			// copy objects to be passed into t (required for event object to be passed in IE)
			var ev = jQuery.extend({},e);
			var ob = this;

			// cancel hoverIntent timer if it exists
			if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

			// if e.type == "mouseenter"
			if (e.type == "mouseenter") {
				// set "previous" X and Y position based on initial entry point
				pX = ev.pageX; pY = ev.pageY;
				// update "current" X and Y position based on mousemove
				$(ob).bind("mousemove",track);
				// start polling interval (self-calling timeout) to compare mouse coordinates over time
				if (ob.hoverIntent_s != 1) { ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

			// else e.type == "mouseleave"
			} else {
				// unbind expensive mousemove event
				$(ob).unbind("mousemove",track);
				// if hoverIntent state is true, then call the mouseOut function after the specified delay
				if (ob.hoverIntent_s == 1) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
			}
		};

		// bind the function to the two event listeners
		return this.bind('mouseenter',handleHover).bind('mouseleave',handleHover);
	};
})(jQuery);
/*
 * Supersubs v0.2b - jQuery plugin
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 *
 * This plugin automatically adjusts submenu widths of suckerfish-style menus to that of
 * their longest list item children. If you use this, please expect bugs and report them
 * to the jQuery Google Group with the word 'Superfish' in the subject line.
 *
 */

;(function($){ // $ will refer to jQuery within this closure

	$.fn.supersubs = function(options){
		var opts = $.extend({}, $.fn.supersubs.defaults, options);
		// return original object to support chaining
		return this.each(function() {
			// cache selections
			var $$ = $(this);
			// support metadata
			var o = $.meta ? $.extend({}, opts, $$.data()) : opts;
			// get the font size of menu.
			// .css('fontSize') returns various results cross-browser, so measure an em dash instead
			var fontsize = $('<li id="menu-fontsize">&#8212;</li>').css({
				'padding' : 0,
				'position' : 'absolute',
				'top' : '-999em',
				'width' : 'auto'
			}).appendTo($$).width(); //clientWidth is faster, but was incorrect here
			// remove em dash
			$('#menu-fontsize').remove();
			// cache all ul elements
			$ULs = $$.find('ul');
			// loop through each ul in menu
			$ULs.each(function(i) {	
				// cache this ul
				var $ul = $ULs.eq(i);
				// get all (li) children of this ul
				var $LIs = $ul.children();
				// get all anchor grand-children
				var $As = $LIs.children('a');
				// force content to one line and save current float property
				var liFloat = $LIs.css('white-space','nowrap').css('float');
				// remove width restrictions and floats so elements remain vertically stacked
				var emWidth = $ul.add($LIs).add($As).css({
					'float' : 'none',
					'width'	: 'auto'
				})
				// this ul will now be shrink-wrapped to longest li due to position:absolute
				// so save its width as ems. Clientwidth is 2 times faster than .width() - thanks Dan Switzer
				.end().end()[0].clientWidth / fontsize;
				// add more width to ensure lines don't turn over at certain sizes in various browsers
				emWidth += o.extraWidth;
				// restrict to at least minWidth and at most maxWidth
				if (emWidth > o.maxWidth)		{ emWidth = o.maxWidth; }
				else if (emWidth < o.minWidth)	{ emWidth = o.minWidth; }
				emWidth += 'em';
				// set ul to width in ems
				$ul.css('width',emWidth);
				// restore li floats to avoid IE bugs
				// set li width to full width of this ul
				// revert white-space to normal
				$LIs.css({
					'float' : liFloat,
					'width' : '100%',
					'white-space' : 'normal'
				})
				// update offset position of descendant ul to reflect new width of parent
				.each(function(){
					var $childUl = $('>ul',this);
					var offsetDirection = $childUl.css('left')!==undefined ? 'left' : 'right';
					$childUl.css(offsetDirection,emWidth);
				});
			});
			
		});
	};
	// expose defaults
	$.fn.supersubs.defaults = {
		minWidth		: 9,		// requires em unit.
		maxWidth		: 25,		// requires em unit.
		extraWidth		: 0			// extra width can ensure lines don't sometimes turn over due to slight browser differences in how they round-off values
	};
	
})(jQuery); // plugin code ends




/********************************
 * jQuery XML Helper
 * 
 * Created by Kamran Ayub (c)2011
 * https://github.com/kamranayub/jQuery-XML-Helper
 *
 * Makes working with XML using jQuery a bit easier
 * for Internet Explorer.
 *
 *
 */
 var $$ = $.sub();
 
$.xml = function (xml) {
    /// <summary>
    /// Makes working with XML a little more
    /// cross-browser compatible by overloading
	/// and extending some core jQuery functions
    /// </summary>

    "use strict";    
    	
	$$.parseXML = function (data, xml, tmp) {
		// Slightly modified to use
		// ActiveX for IE9 as well (reversed
		// statements
		if ( window.ActiveXObject ) { // IE6+
			xml = new ActiveXObject( "Microsoft.XMLDOM" );
			xml.async = "false";
			xml.loadXML( data );
		} else { // Standard						
			tmp = new DOMParser();
			xml = tmp.parseFromString( data , "text/xml" );
		}

		tmp = xml.documentElement;

		if ( ! tmp || ! tmp.nodeName || tmp.nodeName === "parsererror" ) {
			jQuery.error( "Invalid XML: " + data );
		}

		return xml;
	};
	
	$$.fn.append = function () {
		var target = arguments[0],
			nodes = [],
			numNodes,
            curDOM,
            fallback = false;
		
        this.each(function () {
            curDOM = this;
            
            if (curDOM !== null && $.find.isXML(curDOM)) {
                // XMLDOM?			
                if ($.find.isXML(target)) {		
                    nodes = target.childNodes;
                // $-wrapped XML?
                } else if (target instanceof $ && $.find.isXML(target[0])) {
                    nodes = target;
                // String?
                } else if (typeof target === "string") {
                    // Wrap in case multiple elements were requested
                    nodes = $$.parseXML("<jxmlroot>" + target + "</jxmlroot>").firstChild.childNodes;
                }
                
                // Nodes get removed from array when moved
                numNodes = nodes.length;			
                for (i = 0; i < numNodes; i++) {
                    if ($.browser.webkit) {					
                        curDOM.appendChild(curDOM.ownerDocument.importNode(nodes[i], true));
                    } else {
                        curDOM.appendChild(nodes[0]);
                    }
                }                                	
            } else {
                fallback = true;
            }
        });
        
        if (fallback === true) {
            return $.fn.append.apply(this, arguments);
        } else {
            return this;
        }
	};
    
    $$.fn.before = function () {
        if (typeof arguments[0] === "string") {
			if ($.browser.webkit) {
				arguments[0] = this[0].ownerDocument.importNode(
					$$.parseXML("<jxmlroot>" + arguments[0] + "</jxmlroot>"
				).firstChild, true).childNodes;
			} else {
				arguments[0] = $.xml(arguments[0]);
			}
			
            return $.fn.before.apply(this, arguments);
        } else {
            return $.fn.before.apply(this, arguments);
        }
    };
    
    $$.fn.text = function () {
        var text = arguments[0],
            curDOM,
            textNode, i, 
            curNodes, curNodeLength, node;
        
        if (text) {
            this.each(function () {
                curDOM = this;
                
                textNode = curDOM.ownerDocument.createTextNode(text);

                curNodes = curDOM.childNodes;
                curNodeLength = curNodes.length;
                
                // Remove all nodes as we're setting the value of the node to
                // a text node
                for (i = 0; i < curNodeLength; i++) {
                    node = curNodes[0];
                    
                    node.parentNode.removeChild(node);
                }
                
                curDOM.appendChild(textNode);               
            });
            
            return this;
        } else {
            return $.fn.text.apply(this, arguments);
        }                             
    };
    
    $$.fn.cdata = function (data) {
        var curDOM, i, node, cdata;
		
        // Set CDATA
        if (data) {	
            this.each(function () {
                curDOM = this;
                
                cdata = curDOM.ownerDocument.createCDATASection(data);
                
                // Remove existing CDATA, if any.
                for (i = 0; i < curDOM.childNodes.length; i++) {
                    node = curDOM.childNodes[i];
                    if (node.nodeType === 4) { // cdata
                        node.parentNode.removeChild(node);
                        break;
                    }
                }
                
                if ($.browser.webkit) {
                    curDOM.appendChild(curDOM.ownerDocument.importNode(cdata, true));
                } else {
                    curDOM.appendChild(cdata);
                }
            
            });
            
            return this;
        } else {
            // Get CDATA
            curDOM = this[0];
            for (i = 0; i < curDOM.childNodes.length; i++) {
                if (curDOM.childNodes[i].nodeType === 4) { // cdata
                    return curDOM.childNodes[i].nodeValue;
                }
            }
        }
        
        return null;
    };
    
    $$.fn.html = function () {
        // Redirect HTML w/ no args to .cdata()
        if ($.find.isXML(this[0]) && !arguments[0]) {
            return this.cdata();
        } else if ($.find.isXML(this[0]) && arguments[0]) {
            return this.cdata(arguments[0]);
        } else {
			return $.fn.html.apply(this, arguments);
		}
    };
	
	$$.fn.xml = function () {
		/// <summary>
		/// Gets outer XML. Expects $-wrapped XMLDOM.
		/// </summary>
        
		// for IE 
		if (window.ActiveXObject) {
			return this[0].xml;
		} else {
			// code for Mozilla, Firefox, Opera, etc.
		   return (new XMLSerializer()).serializeToString(this[0]);
		}
	};
    
    // Wrap in root tag so when creating new standalone markup, things
    // should still work.
    var parser = $$.parseXML("<jxmlroot>" + xml + "</jxmlroot>");
    
	return $$(parser).find("jxmlroot > *");
};



$(function(){
	if("undefined" == typeof(fcf.m)) fcf.m = {};
	if("undefined" == typeof(fcf.v)) fcf.v = {};
	if("undefined" == typeof(fcf.c)) fcf.c = {
		ignorePathChange : false,
		startup : function(){
			//alert("1 - startup");
			// bare no history redirect if they have no hashbang
			var base_url = fcf.s.config.base_url;
			var uri = window.location.href.substr(base_url.length);
			if(uri.substr(0,2) != "#!" && uri.length){
				window.location.replace(base_url + "#!/" + uri);
				return;
			}
			jQuery("#staticContent").hide();
			//alert("2 - static content hidden");
			if(!FlashDetect.installed) jQuery("#noFlash").show();
			//alert("3 - noFlash hidden");
			fcf.m.path.onPathChange = fcf.c.onPathChange;
			fcf.m.path.init();
			fcf.c.initFlash();
			fcf.c.initHtml();
		},
		initFlash: function(){
			if(typeof(fcf.s.main.swf.src) == "undefined") return;
			var swfVersionStr = "10.0.0";
			var xiSwfUrlStr = "";
			var flashvars = {base_url:fcf.s.config.base_url};
			var wmode = (jQuery.browser.mozilla) ? 'window' : 'transparent'; 
			var params = {allowScriptAccess:"sameDomain", quality:"high", scale:"noscale", salign:"tl", wmode:wmode};
			var attributes = {id:"AppSkeleton",name:"AppSkeleton"};
			swfobject.embedSWF(fcf.s.main.swf.src, "AppSkeleton", "100%", "100%", swfVersionStr, xiSwfUrlStr, flashvars, params, attributes);
			swfobject.createCSS("#AppSkeleton", "display:block;");
		},
		initFlashFooter1 : function(){
			var swfVersionStr = "10.0.0";
			var xiSwfUrlStr = "";
			var flashvars = {connectToTwitter:fcf.s.config.connect_to_twitter, base_url:fcf.s.config.base_url};
			var params = {quality:"high", scale:"noscale", salign:"tl"};
			var attributes = {id:"footerContent",name:"footerContent"};
			swfobject.embedSWF(fcf.s.footer.swf.src, "footerContent","100%", "80", swfVersionStr, xiSwfUrlStr, flashvars, params, attributes);
		},
		initFlashFooter2 : function(){
			$("#footerDiv").children().remove();
			$("#footerDiv").append("<div id='footerContent'></div>");
			$("#footerContent").append($("#footer_main_VIEW").render({}));
			fcf.c.animateTwitterFeed();
			fcf.c.animateFooterLogos();
		},
		initHtml : function(){
			jQuery(".langBtn").click(function(e){
				fcf.c.flash.changeLanguage(e.currentTarget.id);
			});
			if(fcf.v.t.browserIEbelow9()){
				jQuery("#standardBoxBG").css("background-color","white");
			}
			jQuery("#htmlBG").fadeTo(0, 0);
			jQuery("#htmlBG").fadeTo(1500, 0.6);
			fcf.c.animateLogoSubs();
		},
		animateLogoSubs : function(){
			jQuery("#logoSub").children().last().hide("slide",{direction:"left", easing:"easeOutCirc"},1000);
			jQuery("#logoSub").append(jQuery("#logoSub").children().first());
			jQuery("#logoSub").children().last().show("slide",{direction:"right", easing:"easeOutCirc"},1000,function(){
				setTimeout(function(){
					fcf.c.animateLogoSubs();
				}, 2000);
			});
		},
		shuffledLogos : null,
		logoCycleIndex : 0,
		renderFooterLogo : function(){
			if(!(fcf.c.shuffledLogos)){
				fcf.c.shuffledLogos = fcf.c.flash.getLogoUrls();
				fcf.v.t.shuffleArray(fcf.c.shuffledLogos);
			}
			if (fcf.c.logoCycleIndex == fcf.c.shuffledLogos.length) fcf.c.logoCycleIndex = 0;
			var src = {src : fcf.c.shuffledLogos[fcf.c.logoCycleIndex] };
			fcf.c.logoCycleIndex++;
			return $("#common_img_VIEW").render(src);
		},
		animateFooterLogos : function(){
			
			if($("#footerDiv #footerLogo1").children().length == 0){
				$("#footerDiv #footerLogo1").append(fcf.c.renderFooterLogo());
				$("#footerDiv #footerLogo2").append(fcf.c.renderFooterLogo());
				$("#footerDiv #footerLogo1").children().hide();
				$("#footerDiv #footerLogo2").children().hide();
				$("#footerDiv #footerLogo1").children().fadeIn(1000);
				$("#footerDiv #footerLogo2").children().fadeIn(1000,function(){
					setTimeout(function(){
						fcf.c.animateFooterLogos();
					}, 3000);
				});
			}
			else{
				$("#footerDiv #footerLogo1").children().fadeOut(1000);
				$("#footerDiv #footerLogo2").children().fadeOut(1000,function(){
					$("#footerDiv #footerLogo1").children().remove();
					$("#footerDiv #footerLogo2").children().remove();
					$("#footerDiv #footerLogo1").append(fcf.c.renderFooterLogo());
					$("#footerDiv #footerLogo2").append(fcf.c.renderFooterLogo());
					$("#footerDiv #footerLogo1").children().hide();
					$("#footerDiv #footerLogo2").children().hide();
					$("#footerDiv #footerLogo1").children().fadeIn(1000);
					$("#footerDiv #footerLogo2").children().fadeIn(1000,function(){
						setTimeout(function(){
							fcf.c.animateFooterLogos();
						}, 3000);
					});
				});
			}
		},
		twitterFeedCycleIndex : 0,
		$twitterFeedStatuses : null,
		renderTwitterFeed : function(){
			if(!(fcf.c.$twitterFeedStatuses)){
				fcf.c.$twitterFeedStatuses = $(fcf.proxytweet).children("twitfeed").children();
			}
			if (fcf.c.twitterFeedCycleIndex == fcf.c.$twitterFeedStatuses.length) fcf.c.twitterFeedCycleIndex = 0;
			var $status = fcf.c.$twitterFeedStatuses.eq(fcf.c.twitterFeedCycleIndex);
			fcf.c.twitterFeedCycleIndex++;
			
			var text = $status.children("text").text();
			var rnd = {
				text			: text
			};
			return $("#common_twittermessage_VIEW").render(rnd);
		},
		animateTwitterFeed : function(){
			if($("#footerDiv #twitterFeed").children().length == 0){
				$("#footerDiv #twitterFeed").append(fcf.c.renderTwitterFeed());
				$("#footerDiv #twitterFeed").children().hide();
				$("#footerDiv #twitterFeed").children().show("slide",{"direction":"down"},1000,function(){
					setTimeout(function(){
						fcf.c.animateTwitterFeed();
					}, 3000);
				});
			}
			else{
				$("#footerDiv #twitterFeed").children().hide("slide",{"direction":"down"},1000,function(){
					$("#footerDiv #twitterFeed").children().remove();
					$("#footerDiv #twitterFeed").append(fcf.c.renderTwitterFeed());
					$("#footerDiv #twitterFeed").children().hide();
					$("#footerDiv #twitterFeed").children().show("slide",{"direction":"down"},1000,function(){
						setTimeout(function(){
							fcf.c.animateTwitterFeed();
						}, 3000);
					});
				});
			}
		},
		execute : function(command){
			var args = command.split("/");
			command = args.splice(0,1)[0];
			if(!(fcf.c.hasOwnProperty(command))){
				alert("command does not exist: " + command);
				return;
			}
			fcf.c[command].apply(null,args);
		},
		login : function(name,password){
			fcf.v.cms.login(name,password);
		},
		onPathChange : function(input){
			if(fcf.c.ignorePathChange) return;
			var menuItem = fcf.m.ci.factor("");
			fcf.v.menu.render(menuItem);
			var path = (input.hasOwnProperty("path")) ? input.path : typeof(input)=='string' ? input : "";
			var ppath = fcf.m.path.parsePath(path);
			if(ppath.command) fcf.c.execute(ppath.command);
			//alert("5 - address parsed to path: " + ppath.path);
			fcf.c.displayContentItem(ppath.path);
			//_gaq.push(['_trackPageview', ppath.path]);
			fcf.c.ignorePathChange = true;
			//console.log(ppath);
			if(ppath.replacement) fcf.m.path.setPath(ppath.replacement);
			fcf.c.ignorePathChange = false;
			if(typeof( fcf.s.session.username) == "undefined") return;
			if(fcf.s.session.username == "nobody") return;
			fcf.v.cms.init();
		},
		displayContentItem : function(path){
			//alert("6 - displayContentItem");
			var ci = fcf.m.ci.factor(path,true);
			//alert("7 - ci factored: " + ci.getDeepestChild().url);
			if(fcf.v.displayFlash(ci)) return;
			if(fcf.v.displayHtml(ci)) return;
			//alert("could not display: " + path);
		},
		initCms : function(){
			alert("loading cms...");
		},
		refreshLocal : function(){
			fcf.c.onPathChange(jQuery.address.value());
		},
		loadModule : function(name, condition, callback){
			if(condition.apply()){
				callback.apply();
				return;
			}
			jQuery.post("startup/loadjs/" + name + "/js", {} , function(data){
				var script   = document.createElement("script");
				script.type  = "text/javascript";
				script.text  = data;               // use this for inline script
				document.body.appendChild(script);
				callback.apply();
			});
		}
	};
	fcf.m.path = {
		_current : "",
		init : function(){
			jQuery.address.crawlable(true).init();
			jQuery.address.change(fcf.m.path.onPathChange);
			//alert("4 - address init");
			fcf.c.onPathChange(fcf.m.path.getPath());
		},
		onPathChange : function(e){
			//alert("path change");
		},
		getPath : function(){
			return jQuery.address.value();
		},
		setPath : function(val){
			jQuery.address.value(val);
		},
		parsePath : function(path){
			var pathCopy = path;
			var queryString = path.indexOf("?");
			if(queryString == -1){
				queryString = "";
			}else{
				path = pathCopy.substr(0,queryString);
				queryString = pathCopy.substr(queryString);
			}
			var segments = path.split("/");
			var ppath = {
				command		: null,
				path		: "",
				replacement	: ""
			}
			jQuery.each(segments,function(index,value){
				if(value == "") return;
				if(ppath.command != null){
					ppath.command += ppath.command == "" ? value : "/" + value;
				}else if(value == "command"){
					ppath.command = "";
				}else{
					ppath.path += ppath.path == "" ? value : "/" + value;
				}
			});
			if(ppath.path == ""){
				var defP = fcf.m.path._getDefaultPath();
				ppath.path 			= defP;
				ppath.replacement 	= defP;
			}else if(fcf.m.path.getPath() != path){
				ppath.replacement 	= ppath.path
			}
			return ppath;
		},
		_getDefaultPath : function(){
			return (fcf.s.config.default_path) ? fcf.s.config.default_path : "fcf_m_path_no_default";
		}
	}
	fcf.m.ci = {
		constructedItem : null,
		replaceUnderscores : function(input){
			while( -1 != input.search("_")){
				input = input.replace("_"," ");
			}
			return input;
		},
		filterForLanguage : function(JQ_fields){
			var xmlString = fcf.m.ci.xmlToString(JQ_fields[0]);
			var xmlDoc = jQuery.parseXML(xmlString);
			var $xmlDoc = jQuery(xmlDoc);
			var $xmlRoot = $xmlDoc.children().first();
			var i = 0;
			while(i < $xmlRoot.children().length){
				var field = $xmlRoot.children().eq(i);
				i++;
				var nodeName = field[0].nodeName;
				// if there is multiple of this field...
				if($xmlRoot.children(nodeName).length > 1){
					if(fcf.s.session.language == "nl"){
						if("undefined" != typeof(field.attr("lang"))){
							field.remove();
							i--;
						}
					}else{
						if(field.attr("lang") !== fcf.s.session.language){
							field.remove();
							i--;
						}
					}
				}
			}
			return $xmlRoot;
		},
		xmlToString: function(xmlData) { 
			var xmlString;
			//IE
			if (window.ActiveXObject){
			    if(typeof(xmlData.xml) == "undefined"){
				var sr = new XMLSerializer();
				xmlString = sr.serializeToString(xmlData);
			    }else{
				    xmlString = xmlData.xml;
			    }
			}
			// code for Mozilla, Firefox, Opera, etc.
			else{
				xmlString = (new XMLSerializer()).serializeToString(xmlData);
			}
			return xmlString;
		},
		newContentItem : function(opts){
			var c = {};
			c.version = opts.version;
			c.url = opts.path;
			c.title = opts.title;
			c.view = opts.view;
			c.fields = typeof(opts.fields[0]) == "undefined" ? null : fcf.m.ci.xmlToString(opts.fields[0]);
			c.lists = typeof(opts.$JQ_Lists[0]) == "undefined" ? null : fcf.m.ci.xmlToString(opts.$JQ_Lists[0]);
			c.childItems = opts.childItems;
			c.childItem = null;
			c.menus = opts.menus;
			//c.parent = opts.parent;
			c.append = function(toAppend){
				if(this.childItem == null){
					this.childItem = toAppend;
				}else{
					this.childItem.append(toAppend);
				}
			}
			c.getDeepestChild = function(){
				if(this.childItem == null){
					return this;
				}
				var deeper = this.childItem;
				while(deeper.childItem != null){
					deeper = deeper.childItem;
				}
				return deeper;
			}
			c.setParent = function(parentCI){
				if(this.parent){
					alert("error - parent already set in "  + this.url);
				}
				this.parent = {url:parentCI.url, title:parentCI.title, childItems:parentCI.childItems};
			}
			return c;
		},
		fieldsForCI : function(ci){
			var fieldsObj = { fcf_all : [] };
			var fieldsArray = jQuery(jQuery.parseXML(ci.fields)).children("fields").children();
			jQuery.each(fieldsArray, function(index,value){
				fieldsObj[value.nodeName] 	= jQuery(value).text();
				fieldsObj.fcf_all.push({content: fieldsObj[value.nodeName]}); 
			});
			fieldsObj.lists = {};
			var listsELEM = jQuery(jQuery.parseXML(ci.lists));
			jQuery.each(listsELEM.children().children(), function(index,value){
				var listItemsArray = fieldsObj.lists[$(value).attr("type")] = [];
				$(value).children().each(function(listItemIndex,listItemValue){
					var listItemObject = {};
					$(listItemValue).children().each(function(fldIndex,fldValue){
						listItemObject[fldValue.nodeName] = fldValue.textContent;
					});
					listItemsArray.push(listItemObject);
				});
			});
			fieldsObj.link = ci.url;
			fieldsObj.childItems = ci.childItems;
			fieldsObj.parentItem = {title:ci.parent.title,link:ci.parent.url};
			fieldsObj.siblings = ci.parent.childItems;
			fieldsObj.fcf = fcf;
			return fieldsObj;
		},
		getXmlElement : function(path){
			if(path == "fcf_ci_current_element"){
				path = fcf.m.ci.constructedItem.getDeepestChild().url;
			}
			path = path.split("-");
			var s = "";
			jQuery.each(path,function(index,value){
				s += (s == "") ? value + " " : "> children > " + value + " ";
			});
			return jQuery(fcf.db).find(s);
		},
		getMenus : function(ciElem){
			var ret = [];
			ciElem.find("menus").children().each(function(index,value){
				if($(value).attr("type") == "ref"){
					ret.push($(fcf.db).find("> menus > " + value.nodeName));
				}
			});
			return ret;
		},
		elemToOpts : function(elem,path,parent){
			if(typeof(path) == "undefined") alert("no path");
			if(typeof(elem) == "undefined") alert ( "no elem" );
			var opts = {
				path : path,
				title : "",
				view : "",
				fields : null,
				childItems : [],
				$JQ_Lists : "",
				version : "",
				menus : "",
				parent : parent
			};
			
			opts.menus = fcf.m.ci.getMenus(elem);
			opts.view = elem.attr("view");
			
			var fieldsSrc = elem.find("> fields");
			if(fieldsSrc.length > 0){
				opts.fields = elem.find("> fields").clone();
				opts.fields = fcf.m.ci.filterForLanguage(opts.fields);
				opts.title = opts.fields.children("titleField").text();
			}else{
				opts.fields = $.xml("<fields></fields>");
			}
			
			elem.find("> children").children().each(function(ind,val){
				var $val = jQuery(val);
				if($val.attr("unmenu") == "unmenu") return true;
				var fldChildren = $val.children("fields");
				var aChildLang = fcf.m.ci.filterForLanguage(fldChildren);
				var cl = {
						link: path + ((path == "") ? "" : "-") + val.nodeName,
						title: aChildLang.children("titleField").text()
				};
				opts.childItems.push(cl);
			});
			
			var ciOrder = elem.find("> order");
			var JQ_Lists = jQuery.parseXML("<lists></lists>");
			opts.$JQ_Lists = jQuery(JQ_Lists);
			if(ciOrder.length){
				jQuery.each(ciOrder,function(index,orderList){
					var JQ_orderList = jQuery(orderList);
					var orderType = JQ_orderList.attr("type");
					var JQ_listForType = jQuery.parseXML("<order type='" + orderType + "'></order>");
					var $JQ_listForType = jQuery(JQ_listForType);
					var table = jQuery(fcf.db).find("dataCollection > db > " + orderType);
					jQuery.each(JQ_orderList.children(),function(index,item){
						var itemInDb = table.find(orderType+"[id="+jQuery(item).attr("id")+"]");
						var itemClone = itemInDb.clone();
						var filteredCloneFields = fcf.m.ci.filterForLanguage(itemClone);
						$JQ_listForType.children().first().append(filteredCloneFields[0]);
					});
					opts.$JQ_Lists.children().first().append($JQ_listForType.children().first());
				});
			}
			opts.version = elem.attr("version");
			return opts;
		},
		factor : function(path,forFocus){
			var factored;
			
			var thisurl = "";
			var parent = null;
			var leveldownElem = jQuery(fcf.db).find("dataCollection > site");
			var rootOpts = fcf.m.ci.elemToOpts(leveldownElem,thisurl,parent);
			var rootCI = fcf.m.ci.newContentItem(rootOpts);
			var parentCI = rootCI;
			
			if(path == ""){
				return rootCI;
			}
			
			var splitArr = path.split('-');
			var noUnderscoresArr = path.split('-');
			jQuery.each(splitArr,function(index,value){
				noUnderscoresArr[index] = fcf.m.ci.replaceUnderscores(noUnderscoresArr[index]);
			});
			
			var i = 0;
			while(i < splitArr.length){
				thisurl = "";
				var oneLevelDeeper = null;
				if( splitArr[i] != "") oneLevelDeeper = leveldownElem.find("> children > " + splitArr[i]);
				if(!(oneLevelDeeper.length)) return false;
				
				leveldownElem = oneLevelDeeper;
				
				if(leveldownElem.attr("view") == 'LINK'){
					factored = null;
					return fcf.m.ci.factor(leveldownElem.attr("link"),forFocus);
				}
				
				var i2 = 0;
				while (i2 <= i){
					if(i2 == 0){
						thisurl += splitArr[i2];
					}else{
						thisurl += '-'+splitArr[i2];
					}
					i2++;
				}
				
				var levelDownOpts = fcf.m.ci.elemToOpts(leveldownElem,thisurl);
				var levelDownCI = fcf.m.ci.newContentItem(levelDownOpts);		
				
				if(levelDownCI){
					if(!factored){
						factored = levelDownCI;
					}else{
						factored.append(levelDownCI);
					}
				}	
				
				levelDownCI.setParent(parentCI);
				
				parentCI = levelDownCI;
				
				i++;
			}	
			if(forFocus){
				fcf.m.ci.constructedItem = factored;
			}
			return factored;
		}
	}
	fcf.v = {
		bufferContentItem : null,
		htmlOnDisplay : false,
		flashInitiated : false,
		flashResizeEnabled : false,
		minFlashHeight : null,
		hideFlash : function(){
			fcf.v.flashResizeEnabled = false;
			jQuery("#flashDiv").css("height","1px");
		},
		showFlash : function(){
			jQuery("#flashDiv").css("height","100%");
			fcf.v.flashResizeEnabled = true;
		},
		hideHtml : function(){
			jQuery("#htmlDiv").hide();
		},
		showHtml : function(){
			jQuery("#htmlDiv").show();
		},
		displayFlash : function(ci){
			var dpChild = ci.getDeepestChild();
			if("undefined" != typeof(dpChild.version)){
				if(ci.getDeepestChild().version != "1") return false;
			}
			if(!FlashDetect.installed) return false;
			//alert("8 - displayFlash: " + ci.getDeepestChild().url); 
			fcf.v.hideHtml();
			fcf.v.showFlash();
			//alert("9 - flash div on display");
			if(fcf.v.flashInitiated){
				//alert("10b - flash initiated, show Item");
				fcf.c.flash.flashMovie("AppSkeleton").showItemForObject(ci);
			}else{
				//alert("10a - flash not yet initiated, store Item");
				fcf.v.bufferContentItem = ci;
			}
			fcf.v.resizeFlashSite();
			return true;
		},
		resizeFlashSite : function(){
			if(!(fcf.v.flashInitiated)) return;
			if(!(fcf.v.flashResizeEnabled)) return;
			//alert("11 - resizeFlashSiteMethod");
			var winHeight = jQuery(window).height();
			var footHeight = jQuery("#footerDiv").height();
			var headHeight = jQuery("#htmlHD").height();
			var flashPortHeight = 5 + winHeight - footHeight - headHeight;
			if(flashPortHeight > fcf.v.minFlashHeight){
				jQuery("#flashDiv").css("height",flashPortHeight+"px");
			}else{
				jQuery("#flashDiv").css("height",fcf.v.minFlashHeight+"px");
			}
		},
		displayHtml : function(ci){
			fcf.v.hideFlash();
			fcf.v.showHtml();
			btci = ci.getDeepestChild();
			fieldsObj = fcf.m.ci.fieldsForCI(btci);
			if(btci.view.substr(0,5) == "form_"){
				var tplSelector = "#" + btci.view;
			}else if(btci.view.substr(0,5) == "page_"){
				var tplSelector = "#" + btci.view;
			}else{
				var tplSelector = "#page_" + btci.view;
				if(!(tplSelector.substr(tplSelector.length-5) == "_VIEW")) tplSelector += "_VIEW";
			}
			fieldsObj.content = $(tplSelector).render(fieldsObj);
			$("#standardBox").html($("#page_wrap_VIEW").render(fieldsObj));
			$(".fcf-nav-sibling#" + fieldsObj.link).css("background-color","yellow");
			fcf.v.form.implementForm();
			fcf.v.htmlOnDisplay = true;
		},
		menu : {
			render : function(contentItem){
				$("#mainItems > .mainItem").remove();
				$.each(contentItem.childItems, function(index,value){
					$("#mainItems").append($("#menu_item_VIEW").render(value));
				});
				$(".mainItem").click(function(e){
					$.address.value(e.currentTarget.id);
				});
			}
		},
		t : {
			browserIEbelow9 : function(){
				return parseInt(jQuery.browser.version) < 9 && jQuery.browser.msie;
			},
			browserIE : function(){
				return jQuery.browser.msie;
			},
			shuffleArray : function(myArray){
				var i = myArray.length;
				if ( i == 0 ) return false;
				while ( --i ) {
					var j = Math.floor( Math.random() * ( i + 1 ) );
					var tempi = myArray[i];
					var tempj = myArray[j];
					myArray[i] = tempj;
					myArray[j] = tempi;
				}
			}
		}
	}
})

$(function(){
	fcf.c.flash = {};
	fcf.c.flash.changeLanguage = function(language){
		jQuery.post("s/set/language/"+language, {}, function(data){
			if(data.hasOwnProperty("language")){
				fcf.s.session.language = data.language;
				fcf.c.refreshLocal();
			}
		}, "json");
	}
	fcf.c.flash.getLogoUrls = function(){
		var list = jQuery(fcf.db).find("dataCollection > site > children > logos > order");
		var refs = list.children();
		var type = list.attr("type");
		var table = jQuery(fcf.db).find("db >"+type);
		var urls = [];
		jQuery.each(refs,function(index,value){
			var id = jQuery(value).attr("id");
			var url = jQuery(table).find("ClientLogo[id=" + id + "] > url").text();
			urls.push(url);
		});
		return urls;
	}
	fcf.c.flash.siteLoaded = function(){
		//alert("13 - flash loaded");
		fcf.v.flashInitiated = true;
		if(fcf.v.bufferContentItem){
			fcf.v.displayFlash(fcf.v.bufferContentItem);
			fcf.v.bufferContentItem = null;
		}else{
			if(fcf.v.htmlOnDisplay){
				if(jQuery("#footerContent").children().length == 0){
					fcf.c.initFlashFooter2()
				}
			}else{
				fcf.c.onPathChange(jQuery.address.value());
			}
		}
	}
	fcf.c.flash.siteVisible = function(){
		if(jQuery("#footerContent").children().length == 0){
			fcf.c.initFlashFooter2()
		}
	}
	fcf.c.flash.flashMovie= function(movieName) {
		if (navigator.appName.indexOf("Microsoft") != -1) {
			return window[movieName]
		} else {
			return document[movieName]
		}
	}
	fcf.c.flash.scrollTo = function(y){
		var targetOffset = $("#htmlHD").height() + y + 20;
		if(fcf.v.t.browserIE())
		{
			$(document.documentElement).animate({scrollTop:targetOffset});
		}else if($.browser.mozilla){
			document.documentElement.scrollTop = targetOffset;
		}else 
		{
			$("body").animate({scrollTop:targetOffset});
		}
	}
	fcf.c.flash.setNewHeight = function(newHeight){
		fcf.v.minFlashHeight = newHeight;
		fcf.v.resizeFlashSite();
	}
	fcf.c.flash.factorContentItem = function(path){
		return fcf.m.ci.factor(path);
	}
	fcf.c.flash.displayContentItem = function(path){
		fcf.c.onPathChange(path);
	}
	fcf.c.flash.getLang = function(){
		return fcf.s.session.language;
	}
})














$(function(){
	fcf.v.cms = {
		dirty : false,
		dbitem_name : "dbitem_name",
		login : function(name,password){
			if(typeof(name) == "undefined" || typeof(password) == "undefined"){
				fcf.v.cms.displayLogin();
			}else{
				fcf.v.cms.postLogin(name,password);
			}
		},
		displayLogin : function(){
			jQuery("#cms").html(jQuery("#cms_loginform_VIEW").render());
			jQuery("#loginDisplay input[type=button]").click(function(){
				fcf.v.cms.postLogin(jQuery("#loginDisplay #username").val(), jQuery("#loginDisplay #password").val());
			});
		},
		postLogin : function(username,password){
			jQuery.post("getchall",function(data){
				var shaObj = new jsSHA(data+password, "ASCII");
				var dt = {username:username,handshake:shaObj.getHash("SHA-256", "HEX")};
				jQuery.post("shakehand", dt, fcf.v.cms.loginResult, 'json');
			}, "text");
		},
		loginResult : function(result){
			if(result.status == "success"){
				fcf.s.session.username = result.vars;
				fcf.c.refreshLocal();
			}else{
				alert(result.vars);
			}
		},
		tinyMceInitiated : false,
		init : function(){
			if(!fcf.v.cms.tinyMceInitiated){
				tinyMCE.init({
					mode : "none",
					theme : "advanced",   //(n.b. no trailing comma, this will be critical as you experiment later)
					theme_advanced_buttons1 : "bold,italic,link,unlink",
					theme_advanced_buttons2 : "",
					theme_advanced_buttons3 : "",
					valid_elements : "b/strong,i/em,a[href|target],br",
					entity_encoding : "raw",
					force_p_newlines : false,
					force_br_newlines : true,
					convert_newlines_to_brs : false,
					remove_linebreaks : true,
					setup : function(ed) {
						ed.onKeyDown.add(function(ed, e) {
							fcf.v.cms.dirtyFields();
						});
					}
				});
				fcf.v.cms.tinyMceInitiated = true;
			}
			jQuery("#cms").children().remove();
			if(jQuery("#loginDisplay").length == 0){
				jQuery("#cms").append(jQuery("#cms_loginform_VIEW").render());
			}
			var JQ_loginDisplay = jQuery("#loginDisplay");
			JQ_loginDisplay.children().remove();
			JQ_loginDisplay.append(jQuery("#cms_sessionpanel_VIEW").render({username:fcf.s.session.username}));
			jQuery("#sessionPanel #previewButton").click( fcf.v.cms.saveToPreview );
			jQuery("#sessionPanel #saveButton").click( fcf.v.cms.saveData );
			jQuery("#sessionPanel #logoutButton").click( fcf.v.cms.logout );
			jQuery("#footerDiv").hide();
			fcf.v.cms.openInCms(fcf.m.ci.constructedItem);
		},
		activateLogout:function(){
			jQuery("#logoutButton").removeAttr("disabled");
		},
		deactivateLogout:function(){
			jQuery("#logoutButton").attr("disabled","disabled");
		},
		activateSave : function(){
			jQuery("#saveButton").removeAttr("disabled");
			fcf.v.cms.dirty = false;
		},
		deactivateSave : function(){
			jQuery("#saveButton").attr("disabled","disabled");
		},
		activatePreview : function(){
			jQuery("#previewButton").removeAttr("disabled");
		},
		deactivatePreview : function(){
			jQuery("#previewButton").attr("disabled","disabled");
		},
		dirtyFields : function(){
			fcf.v.cms.dirty = true;
			fcf.v.cms.activatePreview();
		},
		saveToPreview : function(){
			
			fcf.v.cms.deactivatePreview();
			
			var editedPage = jQuery('.edited-page-item');
			var page = fcf.m.ci.getXmlElement("fcf_ci_current_element");
			editedPage.each(function(index,value){
				var jqval = jQuery(value);
				var fields = jqval.find(".vdvw-field-input");
				fields.each(function(index,fval){
					var jqf = jQuery(fval);
					var fieldID = jqf.attr("id");
					var fieldContent = ((tinyMCE.editors[fieldID] == null) ? jqf.val() : tinyMCE.editors[fieldID].getContent());
					var fieldName = jqf.parent().find(".vdvw-field-name").text();
					var fieldLang = jqf.parent().find(".vdvw-field-lang").text();
					if(fieldLang != "") var fieldInDb = page.children("fields").children(fieldName+"[lang="+fieldLang+"]");
					else var fieldInDb = page.children("fields").children(fieldName).first();
					fieldInDb.text(fieldContent);
				})
			})
			
			var listdivs = jQuery('#referencesDisplay .objectrefs');
			listdivs.each(function(k,v) {
				var objecttype = $(v).attr('id').split('_')[1];
				var listInDb = $(page).children("order[type=" + objecttype + "]");
				listInDb.children().remove();
				var referencingul = $(v).find('.draglist.referencing');
				var referencingLis = referencingul.find('li');
				referencingLis.each(function(kk,rli){
					var item = $.xml('<item id="' + $(rli).attr('id') + '"></item>');
					listInDb.append(item);
				});
			});
			
			var editedDbItems = jQuery('#referencesDisplay .edited-dbitem');
			editedDbItems.each(function(index,value){
				var jqval = jQuery(value);
				var segm = jqval.attr("id").split("_");
				var type = segm[0];
				var id = segm[1];
				var itemInDb = jQuery(fcf.db).find("dataCollection > db > " + type + " > " + type + "[id="+id+"]");
				var fields = jqval.find(".vdvw-field-input");
				fields.each(function(index,fval){
					var jqf = jQuery(fval);
					var fieldID = jqf.attr("id");
					var fieldContent = ((tinyMCE.editors[fieldID] == null) ? jqf.val() : tinyMCE.editors[fieldID].getContent());
					var fieldName = jqf.parent().find(".vdvw-field-name").text();
					var fieldLang = jqf.parent().find(".vdvw-field-lang").text();
					if(fieldName == fcf.v.cms.dbitem_name){
						itemInDb.attr("name", fieldContent);
					}else{
						if(fieldLang != "") var fieldInDb = itemInDb.find(fieldName+"[lang="+fieldLang+"]");
						else var fieldInDb = itemInDb.find(fieldName).first();
						fieldInDb.text(fieldContent);
					}
				})
			})
			fcf.c.refreshLocal();
			fcf.v.cms.activateSave();
		},
		saveData : function(){
			fcf.v.cms.deactivateSave();
			fcf.v.cms.deactivateLogout();
			jQuery.post("startup",{save:fcf.db.xml()},function(data){
				if(!(data == "success")){
					alert("PROBLEM: " + JSON.stringify(data));
				}else{
					fcf.v.cms.activateLogout();
				}
			});
		},
		logout : function(){
			jQuery.post("logout",{},function(data){
				if(data.status == "success"){
					fcf.s.session.username = "nobody";
					jQuery("#cms").children().remove();
					jQuery("#referencesDisplay").remove();
					jQuery(".edited-page-item").remove();
					jQuery("#footerDiv").show();
				}
				else alert(JSON.stringify(data));
			},"json");
		},
		contentItemXmlClone : function(path){
			var prepend = "dataCollection > site > children > ";
			var selector = "";
			var pathSegments = path.split("-");
			var bufferParent = "";
			var parentPath = "";
			jQuery.each(pathSegments,function(index,value){
				parentPath = bufferParent;
				selector += selector == "" ? value : " > children > " + value;
				bufferParent += bufferParent == "" ? value : "-" + value;
			});
			selector = prepend + selector;
			var JQ_CI = jQuery(fcf.db).find(selector).clone();
			JQ_CI.attr("parentPath",parentPath);
			return JQ_CI;
		},
		clearNavigationDisplay : function(){
			var JQ_nav = jQuery("#navigationDisplay");
			JQ_nav.remove();
			JQ_nav = jQuery("#cms_navpanel_VIEW").render({});
			jQuery("#cms").append(JQ_nav);
		},
		getFieldSuperClass : function(fieldSubclass){
			return jQuery(fcf.db).find("dataCollection > db > fieldTypes > visuals > " + fieldSubclass).length == 0 ? "strings" : "visuals";
		},
		getFieldRegExp : function(fieldType){
			var fieldTypes = jQuery(fcf.db).find("dataCollection > db > fieldTypes");
			var regexp = fieldTypes.find(fieldType).children("regexp");
			return regexp.text();
		},
		createElementForField : function(fieldXML,fieldID){
			var data = {};
			data.fieldID = fieldID;
			data.fieldName = fieldXML.nodeName;
			data.fieldContent = jQuery(fieldXML).text();
			data.fieldType = fieldXML.getAttribute("fieldType");
			data.regularExpression = fcf.v.cms.getFieldRegExp(data.fieldType);
			data.language = fieldXML.getAttribute("lang");
			if(data.language == null) data.language = "";
			var fieldSuperclass = fcf.v.cms.getFieldSuperClass(data.fieldType);
			return jQuery("#cms_input"+fieldSuperclass+"_VIEW").render(data);
		},
		addDbItem : function(type){
			// get the next increment ID and create an empty copy
			var items = jQuery(fcf.db).find("dataCollection > db > " + type + " > " + type);
			var maxID = 0;
			jQuery.each(items,function(index,value){
				var id = parseInt(jQuery(value).attr("id"));
				if(id>maxID) maxID = id;
			});
			var youngest = jQuery(fcf.db).find("dataCollection > db > " + type + " > " + type + "[id=" + maxID + "]");
			var newClone = youngest.clone();
			var itemId = maxID+1;
			var itemName = "NEW " + type;
			newClone.attr("id",itemId);
			newClone.attr("name",itemName);
			newClone.children().each(function(index,value){
				jQuery(value).text("");
			});
			jQuery(fcf.db).find("dataCollection > db > " + type).append(newClone);
			jQuery("ul#"+type+"_Refd").append("<li class='dbitem' id='"+itemId+"' style='background: #f7f7f7; border: 1px solid gray; margin:0; padding:0;'>"+itemName+"</li>");
			return itemId;
		},
		getDbItemEditBox : function(type){
			var editBoxID = type;
			var refsnode = jQuery("#objectrefs_"+type);
			var refdnode = refsnode.find("#"+type+"_Refd");
			var editbox = refsnode.find("#"+editBoxID);
			if(editbox.length == 0){
				editbox = jQuery("<div id='" + editBoxID + "' style='border:1px solid black;margin:10px;float:left;padding:0;zoom:1;position:relative;'></div>");
				refdnode.after(editbox);
			}else{
				editbox.find("div textarea").each(function(index,value){
					var textarea = jQuery(value);
					var id = textarea.attr("id");
					if (id.substr(0,6) == "editor"){
						tinyMCE.execCommand('mceRemoveControl',false, id);
					}
				});
				editbox.html("");
			}
			return editbox;
		},
		editDbItem : function(type,id){
			var editbox = fcf.v.cms.getDbItemEditBox(type);
			var itemDiv = jQuery("<div id='"+type+"_"+id+"' class='edited-dbitem'></div>").appendTo(editbox);
			
			var item = jQuery(fcf.db).find("dataCollection > db > " + type + " > " + type +"[id="+id+"]"); 
			
			var fieldID = fcf.v.cms.dbitem_name;
			
			var data = {};
			data.fieldID = fieldID;
			data.fieldName = fieldID;
			data.fieldContent = item.attr("name");
			data.fieldType = "head";
			data.regularExpression = fcf.v.cms.getFieldRegExp(data.fieldType);
			data.language = "";
			var fieldSuperclass = fcf.v.cms.getFieldSuperClass(data.fieldType);
			var fieldElement = $($("#cms_input"+fieldSuperclass+"_VIEW").render(data));
			fieldElement.attr("id",fieldID);
			
			itemDiv.append(fieldElement);
			if(fieldElement.find("#editor_" + fieldID).length > 0){	
				tinyMCE.execCommand("mceAddControl", true, "editor_"+fieldID);
			}
			
			item.children().each(function(index,value){
				var fieldID = "dbitem_" + 'field_' + index;
				var fieldElement = jQuery(fcf.v.cms.createElementForField(value,fieldID));
				fieldElement.attr("id",fieldID);
				itemDiv.append(fieldElement);
				if(fieldElement.find("#editor_" + fieldID).length > 0){	
					tinyMCE.execCommand("mceAddControl", true, "editor_"+fieldID);
				}
			});
		},
		clearFields : function(){
			// watch out - remove editors before removing attached textareas
			while (tinymce.editors.length > 0) {
				tinyMCE.execCommand('mceRemoveControl',false, tinymce.editors[0].id); 
			};
			if(jQuery(".edited-page-item").length == 0){
				ret = jQuery("#cms").append(jQuery("#cms_fields_VIEW").render({}));
			}else{
				ret = jQuery(".edited-page-item").replaceWith(jQuery("#cms_fields_VIEW").render({}));
			}
			return jQuery(".edited-page-item");
		},
		upload : function(fileObj){
			var JQ_form = jQuery(fileObj.form);
			var iframename = "iframe_container_" + JQ_form.attr("name");
			var imagesname = "images_container_" + JQ_form.attr("name");
			var JQ_iframe = jQuery("#"+iframename);
			JQ_iframe.css("display","block");
			var JQ_imgDiv = jQuery('#'+imagesname);
			JQ_imgDiv.append("<img src='" + fcf.s.config.css_url + "images/indicator2.gif'>");
			setTimeout(function(){ JQ_form.submit() },5000);
		},
		setUploadedImage : function(imgSrc, index) {
			var input = jQuery("#"+index+" input");
			input.val(imgSrc);
			var JQ_imgCont = jQuery("#images_container_"+index);
			if(imgSrc.substr(imgSrc.lastIndexOf(".")+1) == "mp3"){
				JQ_imgCont.html("<span>mp3 file: " + imgSrc + "</span>");
			}else{
				JQ_imgCont.html("<img src='" + imgSrc + "' />");
			}
			fcf.v.cms.activatePreview();
		},
		uploadError : function(divId, oName) {
			var par = window.document;
			var images = par.getElementById('images_container');
			var imgdiv = par.getElementById(divId);
			images.removeChild(imgdiv);
			var errorDiv = par.getElementById('error');
			errorDiv.innerHTML = oName + " has invalid file type.";
			errorDiv.style.display = '';
		},
		openInCms : function(ci){
			/*
			 * IF LOGGED IN, CREATE A CLONE OF THE DEEPEST CHILD
			 */
			//if(!(jQuery("#sessionPanel").length)) return;
			ci = ci.getDeepestChild();
			var cix = fcf.v.cms.contentItemXmlClone(ci.url);
			/*
			 * DEACTIVATE PREVIEW
			 */
			fcf.v.cms.deactivatePreview();
			/*
			 * CLEAR AND APPEND THE NAV PANEL
			 */
			var JQ_CMS = jQuery("#cmsDisplay");
			fcf.v.cms.clearNavigationDisplay();
			var JQ_PROP_UL = jQuery("#navigationDisplay #properties");
			jQuery.each(cix[0].attributes,function(index,value){
				JQ_PROP_UL.append("<li>" + value.name + "=" + value.value + "</li>");
			});
			jQuery("#navigationDisplay #up").append("<a href='#/" + cix.attr("parentPath") + "'>UP</a>");
			var JQ_CHIL_SPAN = jQuery("#navigationDisplay #down");
			jQuery.each(cix.find("> children").children(),function(index,value){
				var text = fcf.m.ci.filterForLanguage($(value).children("fields").clone()).children("titleField").text();
				JQ_CHIL_SPAN.append("<span><a href='#!"+ ci.url + "-" + value.nodeName +"'>"+text+"</a>, </span>");
			});
			var JQ_SIB_SPAN = jQuery("#navigationDisplay #siblings");
			/*$.each(ci.parent.childItems,function(index,value){
				JQ_SIB_SPAN.append("<span><a href='#!/"+value.link+"'>" + value.title + "</a>, </span>"); 
			});*/
			/*
			 * GET THE EMPTY FIELDS DIV, AND DISPLAY FIELDS
			 */
			var JQ_fields = fcf.v.cms.clearFields();
			cix.find("> fields").children().each(function(index,value){
				var fieldID = "page_" + 'field_' + index;
				var fieldElement = jQuery(fcf.v.cms.createElementForField(value,fieldID));
				fieldElement.attr("id",fieldID);
				JQ_fields.append(fieldElement);
				if(fieldElement.find("#editor_" + fieldID).length > 0){	
					tinyMCE.execCommand("mceAddControl", true, "editor_"+fieldID);
				}
			});
			/*
			 * GET EMPTY DB REFERENCES DIV, AND SHOW REFERENCE LISTS
			 */
			var rfrndiv = jQuery("#referencesDisplay");
			if(rfrndiv.length == 0){
				jQuery("#cms").append("<div id='referencesDisplay' style='position:relative; border:1px solid black;margin:10px;zoom:1;'></div>");
				rfrndiv = jQuery("#referencesDisplay");
			}
			rfrndiv.html("");
			if(cix.find("> order").length > 0){
				rfrndiv.append($("#cms_explain_VIEW").render({}));
			}
			var referencedClassNames = [];
			jQuery.each(cix.find("> order"),function(index,value){
				/*
				 * REFERENCES
				 */
				var objectType = value.getAttribute("type");
				var refsnodeId = "objectrefs_"+objectType;
				var refsnode = jQuery("<div class='objectrefs' id='"+refsnodeId+"'></div>").appendTo(rfrndiv);
				refsnode.css({'border':'1px solid black','float':'left','padding':'0','zoom':'1','position':'relative'});
				var typeheader = jQuery("<h4>"+objectType+"</h4>").appendTo(refsnode);
				typeheader.css('clear','both');
				referencedClassNames.push(objectType);
				var referencingulID = objectType+"_Refs";
				var referencingul = jQuery("<ul id='"+referencingulID+"' class='draglist referencing' style='list-style: none; border: 1px solid black; margin:10px; float:left; padding: 0; zoom: 1; position:relative; width:150px; min-height:200px;'></ul>").appendTo(refsnode);
				var JQ_listItem = jQuery(value);
				var table = jQuery(fcf.db).find("dataCollection > db > " + objectType);
				jQuery.each(JQ_listItem.children(),function(index2,value2){
					var listItemKey = value2.getAttribute("id");
					var referencedItem = table.find(objectType+"[id="+ listItemKey +"]");
					var listItemName = referencedItem.attr("name");
					var referencingLi = jQuery("<li class='objectref' id='"+listItemKey+"' style='background: #f7f7f7; border: 1px solid gray; margin:0; padding:0;'>"+listItemName+"</li>").appendTo(referencingul);
					//createConstrainedDD(referencingLi, refsnodeId,Y);
				});
				/*
				 * "BUTTONS"
				 */
				var btnsdiv = jQuery("<div style='margin:10px;float:left;padding:0;zoom:1;position:relative;'></div>").appendTo(refsnode);
				var boxstyle = {
					'border': '2px solid powderblue',
					'color': 'black',
					'text-align': 'center',
					'position': 'relative',
					'margin': '4px',
					'width': '60px',
					'height': '60px'
				};
				var verwijderen = jQuery("<div class='dropbutton deletebutton'>del</div>").appendTo(btnsdiv);
				verwijderen.css(boxstyle);
				var wijzigen = jQuery("<div class='dropbutton editbutton'>edit</div>").appendTo(btnsdiv);
				wijzigen.css(boxstyle);
				var nieuw = jQuery("<div class='dropbutton newbutton'>new</div>").appendTo(btnsdiv);
				nieuw.css(boxstyle);
				var btns = jQuery('.dropbutton');
				/*
				 * REFERENCED TABLE
				 */
				var referencedulID = objectType+"_Refd";
				var referencedul = jQuery("<ul id='"+objectType+"_Refd' class='draglist referenced' style='list-style: none;border:1px solid black;margin:10px;float:left;padding:0;zoom:1;position:relative;'></ul>").appendTo(refsnode);
				jQuery.each(table.children(),function(index,value){
					var JQ_val = jQuery(value);
					var itemId = JQ_val.attr("id");
					var itemName = JQ_val.attr("name");
					var referencedLi = jQuery("<li class='dbitem' id='"+itemId+"' style='background: #f7f7f7; border: 1px solid gray; margin:0; padding:0;'>"+itemName+"</li>").appendTo(referencedul);
					//createConstrainedDD(referencedLi, refsnodeId,Y);
				});
				refsnode.append("<div class='spacer' style='clear:both;'></div>");
				/*
				 * all the referencing list items
				 */
				 var referencingItemDragOpts = {
					// can be dragged in relation to the "referencingul"
					appendTo: referencingul,
					// the original stays there and we make a copy
					// (same behavior as the "clone" helper)
					// but we also give them a yellow color...
					helper: function(event) {
						var dragged = $(this);
						dragged.css("background-color","yellow");
						return dragged.clone();
					},
					// when the drag is stopped we give the original its grey color back
					// and if we still had to remove the original (workaround, see below) we do so
					stop: function(event,ui){
						$("li#" + $(ui.helper).attr("id") + ".objectref").css("background-color","#f7f7f7");
						if (jQuery(this).attr("removeme") == "true"){
							jQuery(this).remove();
						}
						fcf.v.cms.activatePreview();
					}
				}
				jQuery( "#" + referencingulID + " li" ).draggable(referencingItemDragOpts);
				/*
				 * options for the "over" behavior of the referencing items
				 */
				var referencingItemDropOpts = {
					over: function(event, ui) {
						// the item dragged-over this
						var dragged = $(ui.draggable);
						// it can be a clone of a referencing item (when only the order is changed)
						// or it can be a new reference originating from a db item
						var append = ui.helper.position().top > 8;
						
						if(dragged.hasClass("dbitem")){
							// here we remove the old clone
							referencingul.find("li#" + dragged.attr("id") + ".dbitem").remove();
							// and we insert a new clone after the dragged-over object (todo - insert before?)
							if(append) dragged.clone().insertAfter($(this));
							else dragged.clone().insertBefore($(this));
							// thus creating the effect that a new referencing item is moved
						}else if(dragged.hasClass("objectref")){
							// here we really move the dragged item itself
							if(append) dragged.insertAfter($(this));
							else dragged.insertBefore($(this));
						}
					}
				}
				/*
				 * options for the db items (the referenced items)
				 */
				var dbItemsDragOpts = {
					// use the "referencingul" as a context
					appendTo: referencingul,
					// drag a clone and give them a yellow color
					helper: function(event) {
						var dragged = $(this);
						dragged.css("background-color","yellow");
						return dragged.clone();
					},
					// on drag stop
					stop: function(event,ui){
						// give the original its grey color back
						$("li#" + $(ui.helper).attr("id") + ".dbitem").css("background-color","#f7f7f7");
						// get the item we have been cloning to the referencing list
						var clonedItem = referencingul.find("li#" + $(this).attr("id") + ".dbitem");
						// remove its old characteristics and add the characteristics of the referencing list
						clonedItem.removeClass("dbitem");
						clonedItem.addClass("objectref");
						clonedItem.droppable(referencingItemDropOpts);
						clonedItem.draggable(referencingItemDragOpts);
						// if something changed we have to enable preview
						if(clonedItem.length) fcf.v.cms.activatePreview();
					}
				};
				jQuery( "#" + referencedulID + " li").draggable(dbItemsDragOpts);
				referencingul.find(".objectref").droppable(referencingItemDropOpts);
				referencingul.droppable({
					over: function(event, ui) {
						// if there's items in the list we rely on the (sort-enabled) over functionality
						// todo - refactor so that THIS over method becomes the only one...
						if($(this).children().length > 0) return;
						// the item dragged-over this
						var dragged = $(ui.draggable);
						// it can be a clone of a referencing item (when only the order is changed)
						// or it can be a new reference originating from a db item
						//var append = ui.helper.position().top > 8;
						
						if(dragged.hasClass("dbitem")){
							// here we remove the old clone
							referencingul.find("li#" + dragged.attr("id") + ".dbitem").remove();
							// and we insert a new clone after the dragged-over object (todo - insert before?)
							$(this).append(dragged.clone());
							// thus creating the effect that a new referencing item is moved
						}
						// this else clause should never happen in the referencingul droppable over method
						// as long as this refactoring above mentioned is not done
						else if(dragged.hasClass("objectref")){
							// here we really move the dragged item itself
							//if(append) dragged.insertAfter($(this));
							//else dragged.insertBefore($(this));
						}
					},
					out: function(event,ui){
						var dragged = $(ui.draggable);
						if(dragged.hasClass("dbitem")){
							referencingul.find("li#" + dragged.attr("id") + ".dbitem").remove();
						}
					}
				});
				jQuery(".dropbutton").droppable({
					over: function(event,ui){
						$(this).css("border",'2px solid green');
					},
					out: function(event,ui){
						$(this).css("border",'2px solid powderblue');
					},
					drop: function(event, ui) {
						$(this).css("border",'2px solid powderblue');
						var button = $(this);
						var id = ui.helper.attr("id");
						var parent = ui.helper.parent();
						var parentID = parent.attr("id");
						var type = parentID.substr(0,parentID.length -5);
						if(button.hasClass("editbutton")){
							fcf.v.cms.editDbItem(type,id);
						}
						if(button.hasClass("deletebutton")){
							if(ui.draggable.hasClass("objectref")){
								ui.draggable.attr("removeme", "true");
							}
							if(ui.draggable.hasClass("dbitem")){
								alert("You have no permission to delete items from the database. Delete the references to them from a page instead.")
							}
						}
						if(button.hasClass("newbutton")){
							id = fcf.v.cms.addDbItem(type);
							fcf.v.cms.editDbItem(type,id);
							jQuery("#"+type+"_Refd #"+id).draggable(dbItemsDragOpts);
						}
					}
				});
			});
			rfrndiv.append("<div style='clear:both;'></div>");
		}
	}
})











$(function(){
	fcf.v.form = {
		implementForm : function(){
			// recaptchas
			if(fcf.v.t.browserIEbelow9()){
				setTimeout(fcf.v.form.implementRecaptchas, 2000);
			}else{
				fcf.v.form.implementRecaptchas();
			}
			var $forms = jQuery("#standardBox form.form");
			$forms.each(function(index,v){
				// implement form submissions
				$(v).find("input.formSubmit").css({"cursor":"pointer"});
				$(v).find("input.formSubmit").click(fcf.v.form.formSubmit);
				// implement upload fields
				$(v).find('#fileupload').fileupload({
					dataType: 'json',
					url: 'up',
					done: function (e, data) {
						$.each(data.result, function (index, file) {
							jQuery("#cvuploadveld").val(file.name);
							jQuery("#cvuploadveld").css("display","");
							jQuery("#cvuploadveld-retry").css("display","");
							jQuery("#fileupload").css("display","none");
						});
					}
				});
			});
		},
		implementRecaptchas : function(){
			var recaptchas = jQuery(".recaptchaDiv");
			jQuery.each(recaptchas,function(index,value){
				Recaptcha.create(fcf.s.config.recaptchaPublicKey, jQuery(value).attr("id"), { theme: "white" });
			});
		},
		formSubmit : function(e){
			var formID = e.currentTarget.id;
			$("#" + formID + " .formSubmitBusy img").show();
			$("#"+formID).find(".formfield[type=checkbox]").each(function(index,value){
				$(value).val($(value).is(':checked') ? "true" : "false");
			});
			var flds = jQuery("#"+formID).find(".formfield");
			var data = {};
			jQuery.each(flds,function(index,value){
				var jq_fld = jQuery(value);
				data[jq_fld.attr("id")] = jq_fld.val();
			});
			data["rcChallenge"] = Recaptcha.get_challenge();
			data["rcResponse"] = Recaptcha.get_response();
			jQuery.post("form/submit/" + formID, data, fcf.v.form.formSubmitCallback, "json");
		},
		formSubmitCallback : function(data,textStatus, jqXHR){
			if(data.hasOwnProperty("formID")){
				// there should be some remarks on the form
				jQuery("#" + form + " .formSubmitBusy img").hide();
				var form = data.formID;
				s = "#" + form + " .errorMessageParagraph";
				jQuery(s).children().remove();
				s = "#" + form + " .formfield";
				jQuery(s).css("border","1px black");
				if(data[0]){
					if(data[0].hasOwnProperty("error")){
						jQuery.each(data,function(index,error){
							if(!(index == "formID")){
								var form = error.form;
								var field = error.field;
								var msg = error.message.nl;
								var s = "#" + form + " " + "#" + field + ".formfield";
								jQuery(s).css("border","1px solid red");
								s = "#" + form + " " + "#" + field + ".errorMessageParagraph";
								jQuery(s).html(jQuery("#cms_errormessage_VIEW").render({message:msg}));
								if(field == "rcResponse"){
									var recaptchas = jQuery(".recaptchaDiv");
									jQuery.each(recaptchas,function(index,value){
										Recaptcha.create(fcf.s.config.recaptchaPublicKey, jQuery(value).attr("id"), { theme: "white" });
									});
								}
							}
						});
					}
				}
			}else if(data.hasOwnProperty("db_store_result")){
				if(data.db_store_result == "success"){
					if(data.hasOwnProperty("sendmail_formID")){
						var form = data.sendmail_formID;
						if(data.hasOwnProperty("sendmail_result")){
							if(data.sendmail_result == "success"){
								jQuery.address.value(jQuery.address.value()+"-dank");
							}
						}
					}
					document.location.reload(true);
				}
			}
			else if(data.hasOwnProperty("sendmail_formID")){
				var form = data.sendmail_formID;
				if(data.hasOwnProperty("sendmail_result")){
					if(data.sendmail_result == "success"){
					jQuery.address.value(jQuery.address.value()+"-dank");
						return;
					}
				}
			}
		}
	}
})
$(function(){
	fcf.v.playlist = {
		list : null,
		iter : 0,
		playing: false,
		doneSetup: false,
		play : function(){
			if(fcf.v.playlist.playing){
				soundManager.pauseAll();
				fcf.v.playlist.playing = false;
				return;
			}
			if(this.list == null){
				this.list = [];
				$(fcf.db).find("> site > children > muziek > order[type=Track] > item").each(function(i,v){
					var dbitem = $(fcf.db).find("> db > Track > Track[id=" + $(v).attr("id") + "] > mp3");
					fcf.v.playlist.list.push(dbitem.text());
				});
			}
			if(!(fcf.v.playlist.doneSetup)){
				soundManager.setup({
					url : fcf.s.config.swf_url,
					// optional: version of SM2 flash audio API to use (8 or 9; default is 8 if omitted, OK for most use cases.)
					// flashVersion: 9,
					// use soundmanager2-nodebug-jsmin.js, or disable debug mode (enabled by default) after development/testing
					// debugMode: false,
					// good to go: the onready() callback
					onready : fcf.v.playlist.playSong,
					// optional: ontimeout() callback for handling start-up failure
					ontimeout : function() {
						// Hrmm, SM2 could not start. Missing SWF? Flash blocked? Show an error, etc.?
						// See the flashblock demo when you want to start getting fancy.
						alert("could not start sound player");
					}
				});
				fcf.v.playlist.doneSetup = true;
			}else{
				soundManager.resumeAll();
			}
			fcf.v.playlist.playing = true;
		},
		playSong : function(){
			if(fcf.v.playlist.iter >= fcf.v.playlist.list.length) fcf.v.playlist.iter = 0;
			var mySound = soundManager.createSound({
				id : 'playlist' + fcf.v.playlist.iter,
				url : fcf.v.playlist.list[fcf.v.playlist.iter],
				onfinish: fcf.v.playlist.playSong
			});
			fcf.v.playlist.iter++;
			mySound.play();
		}
	}
})
