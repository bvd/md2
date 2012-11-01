<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">	
    <head>
	<title><?php echo $title;?></title>
	<meta name="google" value="notranslate">        
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?php echo $analytics_account; ?>']);
	  _gaq.push(['_trackPageview']);
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>
	<script type="text/javascript" src="<?php echo $js_url; ?>tinymce/tiny_mce.js"></script>
	<!-- styles -->
		<!-- styles - google fonts dir -->
			<!--<link href='http://fonts.googleapis.com/css?family=Archivo+Narrow:400,400italic,700,700italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>-->
		<!-- styles - swfobject -->
			<style type="text/css" media="screen" id="baseStyle" href="css/baseStyle.css">
				object:focus { outline:none; }
				html, body{ padding: 0px; margin: 0px; }
				body { padding-bottom: 80px; }
				#AppSkeleton { display:none; }
				#mainItems a:hover {
					background: MediumBlue;
					color: #AAAAAA;
					cursor: pointer;
				}
			</style>
		<!-- styles - site -->
			<style type="text/css" media="screen" id="formStyle">
				#footerContent a img{
					border: none;
				}
				.twitfeed a:link{
					color: #0084B4;
					text-decoration:none;
				}
				.twitfeed a:visited{
					color: #0084B4;
					text-decoration:none;
				}
				.twitfeed a:hover{
					color: #0084B4;
					text-decoration:underline;
				}
				.twitfeed a:active{
					color: #0084B4;
					text-decoration:underline;
				}
				#staticContent a:link{
					color: white
				}
				#staticContent a:visited{
					color: white
				}
				#staticContent p{
					color: white
				}
				p
				{
					font-family: 'Archivo Narrow', sans-serif;
					color: SteelBlue;
				}
				input, textarea
				{
					padding: 9px;
					border: solid 1px #E5E5E5;
					outline: 0;
					font: normal 13px/100% Verdana, Tahoma, sans-serif;
					width: 400px;
					background: #FFFFFF url('<?php echo $css_url; ?>images/input_bg.png') left top repeat-x;
					background: -webkit-gradient(linear, left top, left 25, from(#FFFFFF), color-stop(4%, #EEEEEE), to(#FFFFFF));
					background: -moz-linear-gradient(top, #FFFFFF, #EEEEEE 1px, #FFFFFF 25px);
					box-shadow: rgba(0,0,0, 0.1) 0px 0px 8px;
					-moz-box-shadow: rgba(0,0,0, 0.1) 0px 0px 8px;
					-webkit-box-shadow: rgba(0,0,0, 0.1) 0px 0px 8px;
				}
				textarea
				{
					width: 400px;
					max-width: 400px;
					height: 150px;
					line-height: 150%;
					background: #FFFFFF url('<?php echo $css_url; ?>images/textarea_bg.png') left top repeat-x;
					background: -webkit-gradient(linear, left top, left 25, from(#FFFFFF), color-stop(4%, #EEEEEE), to(#FFFFFF));
					background: -moz-linear-gradient(top, #FFFFFF, #EEEEEE 1px, #FFFFFF 25px);
				}
				input:hover, textarea:hover, input:focus, textarea:focus
				{
					border-color: #C9C9C9;
					-webkit-box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 8px;
				}
				.form label
				{
					margin-left: 10px;
					color: #999999;
					font: normal 13px/100% Verdana, Tahoma, sans-serif;
				}
				.form h1
				{
					font: normal 18px/100% Verdana, Tahoma, sans-serif;
				}
				.submit input
				{
					width: auto;
					padding: 9px 15px;
					background: #617798;
					border: 0;
					font-size: 14px;
					color: #FFFFFF;
					-moz-border-radius: 5px;
					-webkit-border-radius: 5px;
				}
			</style>
		<!-- END styles subs -->
	<!-- END styles -->
	
	<!-- FCF_DB_XML -->
		<!-- site -->
			<script type="text/mddb" id="fcf-db"><?php echo $site_data; ?></script>
		<!-- proxytweet -->
			<script type="text/mddb" id="fcf-proxytweet"><?php echo $proxytweet_data; ?></script>
	<!-- END FCF_DB_XML -->
	
	<!-- templates -->
		<!-- templates - site views -->
			<script type="text/jquery-tpl" id="vdvwTpl_MENU_ITEM_VIEW">
				<a class="mainItem" id="{{>childLink}}" style="font-size:12px; color:white; margin:0px 15px; text-decoration:none;">{{>childTitle}}</a>
			</script>
			<script type="text/jquery-tpl" id="vdvwTpl_DEFAULT_VIEW">
				{{for fcf_all}}
					<div>
						<p>{{>content}}</p>
					</div>
				{{/for}}
			</script>
			<script type="text/jquery-tpl" id="vdvwTpl_MIDDENDUIN_WERKEN_VACATURE_FORM_DANK_VIEW">
				<p>{{>message}}</p>
			</script>
			<script type="text/jquery-tpl" id="vdvwTpl_MIDDENDUIN_WERKEN_VACATURE_FORM_VIEW">
				<form class="form" id="sollicitatieFormulier">
					<h1>{{>contactFormTitle}}</h1>
					<input class="formfield" type="hidden" name="contactPersonName" id="contactPersonName" value="{{>contactPersonName}}"></input>
					<input class="formfield" type="hidden" name="mailFormSubmissionTo" id="mailFormSubmissionTo" value="{{>mailFormSubmissionTo}}"></input>
					<table>
						<tr>
							<td>
								<label for="name">{{>vacancyFieldDescription}}</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td>
								<input class="formfield" type="text" name="functionName" id="functionName" value="{{if functionName===''}}{{else}}{{>functionName}}{{/if}}"></input>
								<div><span class="errorMessageParagraph" id="functionName"></span></div>
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<p style="margin-left: 10px; margin-bottom:0px; font: normal 13px/100% Verdana, Tahoma, sans-serif; font-weight:bold; color: #999999;">
									{{>personalDataDescription}}
								</p>
								<p style="margin-left: 10px; margin-top:0px; font: normal 13px/100% Verdana, Tahoma, sans-serif; color: #999999;">
									{{>formFilloutInstruction}}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="name">
									{{>emailDescription}}
								</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td>
								<input class="formfield" type="text" name="email" id="email"></input>
								<div><span class="errorMessageParagraph" id="email"></span></div>
							</td>
						</tr>
						<tr>
							<td colspan="3" style="text-align: right; font: normal 13px/100% Verdana, Tahoma, sans-serif; color: #999999;">
								<span>
									{{>emailInstruction}}
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="name">
									{{>telNr1Description}}
								</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td>
								<input class="formfield" type="text" name="telnr1" id="telnr1"></input>
								<div><span class="errorMessageParagraph" id="telnr1"></span></div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="name">
									{{>telNr2Descrtiption}}
								</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td>
								<input class="formfield" type="text" name="telnr2" id="telnr2"></input>
								<div><span class="errorMessageParagraph" id="telnr2"></span></div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="name">
									{{>firstNameDescription}}
								</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td>
								<input class="formfield" type="text" name="voornaam" id="voornaam"></input>
								<div><span class="errorMessageParagraph" id="voornaam"></span></div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="name">
									{{>insertionsDescription}}
								</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td>
								<input class="formfield" type="text" name="tussenvoegsels" id="tussenvoegsels"></input>
								<div><span class="errorMessageParagraph" id="tussenvoegsels"></span></div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="name">
									{{>surnameDescription}}
								</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td>
								<input class="formfield" type="text" name="achternaam" id="achternaam"></input>
								<div><span class="errorMessageParagraph" id="achternaam"></span></div>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<label for="name">
									{{>personalMessageDescriptionLine1}}
								</label><br/>
								<label for="name">
									{{>personalMessageDescriptionLine2}}
								</label>
							</td>
							<td valign="top">
								<label for="name">:</label>
							</td>
							<td>
								<textarea class="formfield" name="motivatie" id="motivatie"></textarea>
								<div><span class="errorMessageParagraph" id="motivatie"></span></div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="name">
									{{>fileUploadDescription}}
								</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td>
								<input id="fileupload" type="file" name="files[]">
								<input class="formfield" type="text" name="cvuploadveld" id="cvuploadveld" style="display:none; width: 280px;"/>
								<span class="submit"><input id="cvuploadveld-retry" type="button" value="OPNIEUW" onclick="jQuery('#fileupload').css('display',''); jQuery('#cvuploadveld').css('display','none'); jQuery('#cvuploadveld-retry').css('display','none');" style="display:none; float:right; margin-top:4px;"/></span>
								<div><span class="errorMessageParagraph" id="cvuploadveld"></span></div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="name">
									{{>spamProtectionDescriptionLine1}}
								</label><br/>
								<label for="name">
									{{>spamProtectionDescriptionLine2}}
								</label>
							</td>
							<td>
								<label for="name">:</label>
							</td>
							<td style="text-align:right; float:right;">
								<span><object><div class="recaptchaDiv" id="sollicitatieFormulierReCapTcha" style="text-align:right;">
								</div></object></span>
								<div><span class="errorMessageParagraph" id="rcResponse"></span></div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<p style="margin-left: 10px; margin-top:0px; font: normal 10px/100% Verdana, Tahoma, sans-serif; color: #999999;">
									{{>privacyStatement}}
								</p>
							</td>
							<td style="text-align:right;">
								<span class="formSubmitBusy" style="display: inline; float: right; margin-top: 16px; ">
									<img src="<?php echo $css_url; ?>/images/busy.gif" style="display:none;"></img></span>
								<p class="submit"><input type="button" class="formSubmit" value="VERZENDEN" id="sollicitatieFormulier"/></p>
							</td>
						</tr>
					</table>
				</form>
			</script>
		<!-- templates - cms container for cms -->
			<script type="text/jquery-tpl" id="vdvw-cms-display">
				<div id="cmsDisplay" class="vdvw-cms display" style="position: block; top:0px; background-color: #C9C9D0; border: 1px solid black; height: auto; margin: 3px; padding: 0px;"></div>
			</script>
		<!-- templates - cms container for page fields -->
			<script id="vdvw-cms-fields" type="text/jquery-tpl">
				<div class="edited-page-item" style="top:80px; position:relative"></div>
			</script>
		<!-- templates - cms login form -->
			<script type="text/jquery-tpl" id="vdvw-cms-loginForm">
				<div style="margin: 3px; padding: 0px; color:#353268; font-family: Verdana, sans-serif; font-size: 13px; display:inline-block;" id="loginDisplay">
				<div>
				   <form name="loginForm" onsubmit="encryptPassword()">
					  <table>
					 <tbody>
						<tr>
						<td>Welkom! Door in te loggen kan je de CMS-weergave opstarten.<br />- LETOP - er kan er maar &eacute;&eacute;n tegelijk inloggen</td>
						<td>naam: </td>
						<td style="text-align:right"><input size="20" style="width: 150px;" id="username" le="" type="text" /></td>
						<td>wachtwoord: </td>
						   <td style="text-align:right"><input size="20" style="width: 150px;" id="password" type="password" /></td>
						   <td style="text-align:right"><input name="loginButton" value="Inloggen" type="button" style="width: 100px;"/></td>
						</tr>
					 </tbody>
					  </table>
				   </form>
				</div>
				 </div>
			</script>
		<!-- templates - cms navigation panel -->
			<script type="text/jquery-tpl" id="vdvw-cms-nav-panel">
				<div id="navigationDisplay" style="color:#353268; font-family: Verdana, sans-serif; font-size: 11px; display:inline-block; vertical-align:top;">
					<div style="display:inline-block;vertical-align:top">
						<ul id="properties" style="margin:0px 0px; padding: 0px 20px;"></ul>
					</div>
					<div style="display:inline-block;vertical-align:top; width:300px;">
						<span id="up"></span>
						<span>, DOWN: </span>
						<span id="down"></span>
					</div>
				</div>
			</script>
		<!-- templates - cms session panel -->
			<script type="text/jquery-tpl" id="vdvw-cms-session-panel">
				<div id="sessionPanel" style="margin: 1px; zoom: 1; padding: 0px;">
				  <table border="0" cellspacing="0">
				<tbody>
				  <tr  style="padding:0px 0px;">
					<td id="username" style="padding:0px 0px;">user: {{>username}}</td>
					<td style="padding:0px 0px;"><input type="button" style="padding:0px 0px;width:150px;" id="logoutButton" value="Uitloggen"/></td>
				  </tr>
				  <tr  style="padding:0px 0px;">
					<td style="padding:0px 0px;"><input type="button" style="padding:0px 0px;width:150px;" id="previewButton" value="Preview"/></td>
					<td style="padding:0px 0px;"><input type="button" style="padding:0px 0px;width:150px;" id="saveButton" value="Opslaan" disabled="disabled"/></td>
				  </tr>
				</tbody>
				  </table>
				</div>
			</script>
		<!-- templates - cms input form element -->
			<script type="text/jquery-tpl" id="vdvw-field-element-strings">
				<hr/>
				<div>
				<span class="vdvw-field-name">{{>fieldName}}</span><br />
				<span class="vdvw-field-lang">{{>language}}</span>
				<textarea rows='1' cols='50' class='{{>fieldType}} vdvw-field-input' onkeydown='fcf.v.cms.dirtyFields();' name='editor_{{>fieldID}}' id='editor_{{>fieldID}}'>{{>fieldContent}}</textarea>
				</div>
			</script>
		<!-- templates - cms file upload form element -->
			<script type="text/jquery-tpl" id="vdvw-field-element-visuals">
				<hr/>
				<div>
				<span class="vdvw-field-name">{{>fieldName}}</span><br />
				<span class="vdvw-field-lang">{{>language}}</span>
				<input type="text" class='{{>fieldType}} vdvw-field-input' onkeydown='fcf.v.cms.dirtyFields();' value='{{>fieldContent}}'></input>
				<div id='iframe_container_{{>fieldID}}'>
					<iframe src='pmupload/index/{{>fieldID}}/{{>fieldType}}' frameborder='0' style='height:75px;' id='iframe_{{>fieldID}}'></iframe>
				</div>
				<div id='images_container_{{>fieldID}}'>
					<img src='{{>fieldContent}}'></img>
				</div>
				</div>
			</script>
		<!-- templates - cms database fields editor explanation -->
			<script type="text/jquery-tpl" id="vdvw-cms-db-fields-editor-explanation">
				<div>
					<h3>werking van de lijsten</h3>
					<b>Volgorde aanpassen:</b> versleep een item binnen de <i>linkerlijst</i>.<br/>
					<b>Items toevoegen:</b> sleep een item van de <i>rechterlijst</i> (database) naar de <i>linkerlijst</i>.<br/>
					<b>Item verwijderen van pagina:</b> sleep gewenst item uit <i>linkerlijst</i> naar 'del' vierkant.<br/>
					<b>Item aanpassen:</b> eerst item uit <i>rechterlijst</i> of <i>linkerlijst</i> op 'edit' vierkant slepen, dan wijzigen.<br/>
					<b>LETOP!!!</b> Klik NA ELK ITEM op "preview" om de wijzigingen niet te verliezen.<br/>
					<b>Nieuw item maken:</b> sleep een item uit <i>linker- of rechterlijst</i> op 'new'; leeg item verschijnt onderaan <i>rechterlijst</i> (database).<br/>
				</div>
			</script>
		<!-- templates - forms error messages block -->
			<script type="text/jquery-tpl" id="errorMessagesTPL">
				<ul id="messagesList">
				</ul>
			</script>
		<!-- templates - forms error message -->
			<script type="text/jquery-tpl" id="errorMessageTPL">
				<span class="errorMessage" style="color: red; font-family: Courier New, serif; font-size: 12pt;">
					{{>message}}
				</span>
			</script>
		<!-- templates - footer -->
			<script type="text/jquery-tpl" id="vdvw-footer-main">
				<div style="border-top:1px #002D77 solid; width:100%; height: 80px; background-color:white">
					<div style="position:relative; background-color:white; margin:0px auto; width:760px; height:80px;">
						<div style="position:absolute; background-color:white; top:19px; left:36px; width:22px; height:22px;">
							<a href="http://twitter.com/Middenduin" target="_blank"><img src="<?php echo $css_url; ?>images/proxytweet/t_small-b.gif"></img></a>
						</div>
						<div id="twitterFeed" style="position:absolute; background-color:white; top:16px; left:66px; width:280px; height:0px;"></div>
						<div style="position:absolute; background-color:white; top:19px; left:420px; width:335px; height:50px;">
							<div id="footerLogo1" style="position:absolute; width:165px; height:50px;">
							</div>
							<div id="footerLogo2" style="position:absolute; width:165px; height:0px; left:170px;">
							</div>
						</div>
					</div>
				</div>
			</script>
		<!-- templates - footer - logo img -->
			<script type="text/jquery-tpl" id="vdvw-footerlogo-img">
				<img src="{{>src}}"></img>
			</script>
		<!-- templates - footer - twitterfeed -->
			<script type="text/jquery-tpl" id="vdvw-twitterfeed-feed">
				<p style="font-family: 'Helvetica Neue',Arial,sans-serif; font-size: 12px; color:#333; margin-top:0px;" class="twitfeed">{{:text}}</p>
			</script>
		<!-- END templates -->
		<?php echo $js_tags; ?>
		<script type="text/javascript">
			if(typeof( fcf ) == "undefined") var fcf = {};
			if(typeof( fcf.s ) == "undefined") fcf.s = {};
			fcf.s.session = <?php echo $session; ?>;
			fcf.s.config = {};
			fcf.s.config.base_url = "<?php echo $base_url; ?>";
			fcf.s.config.default_path = "<?php echo $default_page; ?>";
			fcf.s.config.css_url = "<?php echo $css_url; ?>";
			fcf.s.config.cb = "<?php echo $cb_version; ?>";
			fcf.s.main = {};
			fcf.s.main.swf = {};
			fcf.s.main.swf.src = '<?php echo $swf_url; ?>middenduin3.swf?cb=' + fcf.s.config.cb;
			fcf.db = $.xml($("#fcf-db").html());
			fcf.proxytweet = $.parseXML($("#fcf-proxytweet").html());
		</script>
    </head>
    <body onLoad="fcf.c.startup()">
		<div id="containerDiv" style="height:100%">	
			<div id="siteContainer">
				<div id="staticContent" style="font-size : 9px; color: white">
					<?php echo $links; ?>
					<?php echo $content; ?>
				</div>
				<div id="noFlash" style="display:none">
					<p>
						<a href="http://www.adobe.com/nl/shockwave/download/triggerpages_mmcom/flash.html">Voor deze site is Flash Player nodig. Klik hier om de Flash player te installeren.</a>
					</p>
				</div>
				<div id="htmlHD" style="position: relative; width:1600px; max-width: 100%; min-height:135px; background-color:white; margin:0 auto;">
					<div id="logo" style="width:268px; margin:auto; text-align:center; position:relative; margin-top:-5px;">
						<img src="<?php echo $css_url; ?>images/logo_1072.jpg" style="margin-top:24px; width:268px;"></img>
						<div id="logoSub" style="width:268px; height: 30px; position: relative; text-align:left; top: 0px;">
							<img id="logoSub1" src="<?php echo $css_url; ?>images/6adm.png" 	style="display:none; top:1px; position: absolute; width: 268px;"></img>
							<img id="logoSub2" src="<?php echo $css_url; ?>images/6corp.png" 	style="display:none; top:1px; position: absolute; width: 268px; "></img>
							<img id="logoSub3" src="<?php echo $css_url; ?>images/6leg.png" 	style="display:none; top:1px; position: absolute; width: 268px; "></img>
							<img id="logoSub4" src="<?php echo $css_url; ?>images/6man.png" 	style="display:none; top:1px; position: absolute; width: 268px; "></img>
						</div>
					</div>
					<div id="mainItems" style="min-height:20px; width:100%; background-color:#002D77; top:120px; position:absolute; text-align: center;">
						<div style="display: inline-block; position: absolute; right: 20px;">
							<a class="langBtn" id="en" style="margin-top:8px;">
							<img
								 src="<?php echo $css_url; ?>/images/uk.jpg"
								 style="
								top: 2px;
								margin-top: 0px;
								padding-top: 0px;
								position: relative;"
							></img>
							</a>
							<a class="langBtn" id="nl" style="margin-top:8px;">
							<img
								 src="<?php echo $css_url; ?>/images/nl.jpg"
								 style="
								top: 2px;
								margin-top: 0px;
								padding-top: 0px;
								position: relative;"
							></img>
							</a>
						</div>
					</div>
				</div>
				<div id="htmlDiv" style="display: none;">
					<div id="htmlBG" style="background-image:url('<?php echo $css_url; ?>images/duinen/duin3large.jpg'); background-repeat:no-repeat; background-position: center -150px; min-height: 100%; height: 100%; position: fixed; min-width: 100%; top: 0px; z-index:-1;">
					</div>
					<div id="contentContainer" style="width: 100%; height: 100%;">
						<div id="standardBoxBG" style="width: 720px; background-color:rgba(255,255,255,0.8); margin: 0 auto; position:relative; box-shadow: 0px 0px 20px #888888; top: 40px;">
							<div id="standardBox" style="padding-left: 30px; padding-right: 30px; padding-top:20px; padding-bottom:40px"></div>
						</div>
					</div>
				</div>
				<div id="flashDiv" style="height:100%" >
					<div id="AppSkeleton">
					</div>
				</div>
				<div id="footerDiv" style="height:80px; position:fixed; bottom:0px; width:100%;" >
					<div id="footerContent">
					</div>
				</div>
				<div id="cms" style='position: relative; background-color: grey; bottom: 0px;'></div>
				</div>
			</div>
		</div>
    </body>
</html>