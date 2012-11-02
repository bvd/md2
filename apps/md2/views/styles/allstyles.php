			<style type="text/css" media="screen" id="baseStyle">
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