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