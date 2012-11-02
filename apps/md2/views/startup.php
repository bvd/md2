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
	
	
	
	
	<script type="text/mddb" id="fcf-db"><?php echo $site_data; ?></script>
	<script type="text/mddb" id="fcf-proxytweet"><?php echo $proxytweet_data; ?></script>

	
	
	<?php
	foreach($modules as $moduleName => $moduleTemplates):
		foreach($moduleTemplates as $tplName => $tplValue):?>
			<script type="text/jquery-tpl" id="<?php echo $moduleName . "_" . $tplName?>_VIEW">
				<?php echo $tplValue; ?>
			</script>
		<?php endforeach;
	endforeach; ?>
	
	
	<!-- templates -->
		<!-- templates - site views -->
			<!--<script type="text/jquery-tpl" id="vdvwTpl_MENU_ITEM_VIEW">-->
			<!--<script type="text/jquery-tpl" id="vdvwTpl_DEFAULT_VIEW">-->
			<!--<script type="text/jquery-tpl" id="vdvwTpl_MIDDENDUIN_WERKEN_VACATURE_FORM_DANK_VIEW">-->
			<!--<script type="text/jquery-tpl" id="vdvwTpl_MIDDENDUIN_WERKEN_VACATURE_FORM_VIEW">-->
			<!--<script type="text/jquery-tpl" id="vdvw-cms-display">-->
			<!--<script id="vdvw-cms-fields" type="text/jquery-tpl">-->
			<!--<script type="text/jquery-tpl" id="vdvw-cms-loginForm">-->
			<!--<script type="text/jquery-tpl" id="vdvw-cms-nav-panel">-->
			<!--<script type="text/jquery-tpl" id="vdvw-cms-session-panel">-->
			<!--<script type="text/jquery-tpl" id="vdvw-field-element-strings">-->
			<!--<script type="text/jquery-tpl" id="vdvw-field-element-visuals">-->
			<!--<script type="text/jquery-tpl" id="vdvw-cms-db-fields-editor-explanation">-->
			<!--<script type="text/jquery-tpl" id="errorMessageTPL">-->
			<!--<script type="text/jquery-tpl" id="vdvw-footer-main">-->
			<!--<script type="text/jquery-tpl" id="vdvw-footerlogo-img">-->
			<!--<script type="text/jquery-tpl" id="vdvw-twitterfeed-feed">-->
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