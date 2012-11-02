<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">	
    <head>
	<title><?php echo $title;?></title>
	<meta name="google" value="notranslate">        
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php if($analytics_enabled): ?>
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
	<?php endif; ?>
	<script type="text/javascript" src="<?php echo $js_url; ?>tinymce/tiny_mce.js"></script>
	<?php foreach($styles as $value): ?>
		<?php echo $value; ?>
	<?php endforeach; ?>
	
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
			fcf.s.main.swf.src = '<?php echo $swf_url; echo $swf_main; ?>?cb=' + fcf.s.config.cb;
			fcf.db = $.xml($("#fcf-db").html());
			fcf.proxytweet = $.parseXML($("#fcf-proxytweet").html());
		</script>
    </head>
    <body onLoad="fcf.c.startup()">
		<?php echo $bodyContent; ?>
    </body>
</html>