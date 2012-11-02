				<hr/>
				<div>
					<span class="vdvw-field-name">{{>fieldName}}</span><br />
					<span class="vdvw-field-lang">{{>language}}</span>
					<textarea rows='1' cols='50' class='{{>fieldType}} vdvw-field-input' onkeydown='fcf.v.cms.dirtyFields();' name='editor_{{>fieldID}}' id='editor_{{>fieldID}}'>{{>fieldContent}}</textarea>
				</div>