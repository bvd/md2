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