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
	fcf.m = {};
	fcf.v = {};
	fcf.c = {};
	
	fcf.c = {
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
			var segments = path.split("/");
			var ppath = {
				command		: null,
				path		: "",
				replacement	: ""
			}
			jQuery.each(segments,function(index,value){
				if(value == "") return; // continue
				if(value.substr(0,1) == "?") return; // continue
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
		newContentItem : function(url,title,view,fields,childItems,lists,version){
			var c = {};
			c.isOtherVersion = version != "" && version != null;
			c.url = url;
			c.title = title;
			c.view = view;
			c.fields = typeof(fields[0]) == "undefined" ? null : fcf.m.ci.xmlToString(fields[0]);
			c.lists = typeof(lists[0]) == "undefined" ? null : fcf.m.ci.xmlToString(lists[0]);
			c.childItems = childItems;
			c.childItem = null;
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
			return c;
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
		factor : function(path,forFocus){
			var factored;
			var splitArr = path.split('-');
			var noUnderscoresArr = path.split('-');
			jQuery.each(splitArr,function(index,value){
				noUnderscoresArr[index] = fcf.m.ci.replaceUnderscores(noUnderscoresArr[index]);
			});
			var leveldown = jQuery(fcf.db).find("dataCollection > site");
			var thisurl;
			var i = 0;
			var i2 = 0;
			var tryToConstructItem;
			while(i < splitArr.length){
				//console.log('now looking for '+splitArr[i] + " within:");
				//console.log(leveldown);
				if( splitArr[i] != "") leveldown = leveldown.find("> children > " + splitArr[i]);
				if(!(leveldown.length)) return false;
				//console.log('leveldown is now: ');
				//console.log(leveldown);
				if(leveldown.attr("view") == 'LINK'){
					//console.log('this is a link, make a jump');
					factored = null;
					fcf.m.ci.factor(leveldown.attr("link"),forFocus);
				}	
				i2 = 0;
				thisurl = "";
				while (i2 <= i){
					if(i2 == 0){
						thisurl += splitArr[i2];
					}else{
						thisurl += '-'+splitArr[i2];
					}
					i2++;
				}
				//var ciTitle = leveldown.attr("title");
				var ciView = leveldown.attr("view");
				// watch out not to touch the database xml
				var ciFields = null;
				var ciTitle = "";
				var fieldsSrc = leveldown.find("> fields");
				if(fieldsSrc.length > 0){
					ciFields = leveldown.find("> fields").clone();
					ciFields = fcf.m.ci.filterForLanguage(ciFields);
					ciTitle = ciFields.children("titleField").text();
				}else{
					ciFields = $.xml("<fields></fields>");
				}
				// and do not clone a too large portion of it,
				// we may also boil it down to titles and links
				
				var childItems = [];
				leveldown.find("> children").children().each(function(ind,val){
					var $val = jQuery(val);
					if($val.attr("view") == "BLANK_VIEW") return true;
					var aChildLang = fcf.m.ci.filterForLanguage($val.children("fields"));
					var cl = {
							childLink: thisurl + ((thisurl == "") ? "" : "-") + val.nodeName,
							childTitle: aChildLang.find("titleField").text()
					};
					childItems.push(cl);
				});
				var ciOrder = leveldown.find("> order");
				var JQ_Lists = jQuery.parseXML("<lists></lists>");
				var $JQ_Lists = jQuery(JQ_Lists);
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
						$JQ_Lists.children().first().append($JQ_listForType.children().first());
					});
				}
				var cVersion = leveldown.attr("version");
				tryToConstructItem = fcf.m.ci.newContentItem(thisurl,ciTitle,ciView,ciFields,childItems,$JQ_Lists,cVersion);		
				if(tryToConstructItem){
					if(!factored){
						factored = tryToConstructItem;
					}else{
						factored.append(tryToConstructItem);
					}
				}	
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
			if(ci.getDeepestChild().isOtherVersion) return false;
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
			var templateSelector = "#" + btci.view;
			var fieldsObj = { fcf_all : [] };
			var fieldsArray = jQuery(jQuery.parseXML(btci.fields)).children("fields").children();
			jQuery.each(fieldsArray, function(index,value){
				fieldsObj[value.nodeName] 	= jQuery(value).text();
				// for simplification of default template code:
				fieldsObj.fcf_all.push({content: fieldsObj[value.nodeName]}); 
			});
			var jqTpl = jQuery(templateSelector);
			if(!(jqTpl.length)) jqTpl = jQuery("#default_default_VIEW");
			var pageContentHTML = jqTpl.render(fieldsObj);
			jQuery("#standardBox").html(pageContentHTML);
			if(jQuery("#footerContent").children().length == 0){
				fcf.c.initFlashFooter2()
			}
			if(!(jQuery("#standardBox").children().first().hasClass("form"))) return;
			else fcf.v.form.implementForm();
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
			fcf.c.onPathChange(jQuery.address.value());
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
			fcf.v.cms.clearCmsDisplay();
			jQuery("#cmsDisplay").append(jQuery("#cms_loginform_VIEW").render());
			jQuery("#loginDisplay input[type=button]").click(function(){
				fcf.v.cms.postLogin(jQuery("#loginDisplay #username").val(), jQuery("#loginDisplay #password").val());
			});
		},
		clearCmsDisplay : function(){
			if(jQuery("#cmsDisplay").length == 0){
				jQuery("#cms").append(jQuery("#cms_display_VIEW").render());
			}else{
				jQuery("#cmsDisplay").replaceWith(jQuery("#cms_display_VIEW").render());
			}
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
			fcf.v.cms.clearCmsDisplay();
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
				ret = jQuery("#siteContainer").append(jQuery("#cms_fields_VIEW").render({}));
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
			JQ_imgCont.html("<img src='" + imgSrc + "' />");
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
			if(!(jQuery("#sessionPanel").length)) return;
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
				JQ_CHIL_SPAN.append("<span><a href='#!"+ ci.url + "-" + value.nodeName +"'>"+value.getAttribute("title")+"</a></span>");
			});
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
				jQuery("#siteContainer").append("<div id='referencesDisplay' style='position:relative; top:80px;border:1px solid black;margin:10px;zoom:1;'></div>");
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
				refsnode.css({'margin':'1px solid black','float':'left','padding':'0','zoom':'1','position':'relative'});
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
						var type = objectType;
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
			// implement form submissions
			jQuery("input.formSubmit").css({"cursor":"pointer"});
			jQuery("input.formSubmit").click(fcf.v.form.formSubmit);
			// implement upload fields
			jQuery('#fileupload').fileupload({
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
		},
		implementRecaptchas : function(){
			var recaptchas = jQuery(".recaptchaDiv");
			jQuery.each(recaptchas,function(index,value){
				Recaptcha.create("6Lfy29YSAAAAAAHECn_eyr7EJzI8ptSUUG1447cJ", jQuery(value).attr("id"), { theme: "white" });
			});
		},
		formSubmit : function(e){
			var formID = e.currentTarget.id;
			jQuery("#" + formID + " .formSubmitBusy img").show();
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
			if(!(data.hasOwnProperty("formID"))) alert("ERROR: " + data);
			var form = data.formID;
			jQuery("#" + form + " .formSubmitBusy img").hide();
			if(data.hasOwnProperty("result")){
				if(data.result == "success"){
				jQuery.address.value(jQuery.address.value()+"-dank");
					return;
				}
			}
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
								Recaptcha.create("6Lfy29YSAAAAAAHECn_eyr7EJzI8ptSUUG1447cJ", jQuery(value).attr("id"), { theme: "white" });
							});
							}
						}
					});
				}
			}
		}
	}
})