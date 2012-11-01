<?php
class Pmupload extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	// const scaleModeOptions
	private $scaleModeOptions = Array(
		'bothDimensionsPrescribed' 				=> 1, 
		'proportionallyToPrescribedWidth' 		=> 2,
		'proportionallyToPrescribedHeight'		=> 3,
		'proportionallyToInputPercentage'		=> 4
	);
	
	private $dimensions;
	private $formIndex;
	private $fieldType;
	private $explainUploadForm = 'Hiermee kan je een plaatje uploaden of wijzigen.';
	private $scaleMode;
	private $imageUrl;
	private $sWidth; // source image width
	private $sHeight; // source image height
	
	function index(){
		
		log_message('debug', '-------------------');
		log_message('debug', '- - PMUPLOAD- - - -');
		log_message('debug', '-------------------');
		log_message('debug', date("D M j G:i:s"));
		log_message('debug', 'uri: ' . $this->uri->uri_string());
		
		// zijn de basisparameters er?
		if(!isset($this->uri->segments[3])){
			log_message('error', 'pmupload: form index not set');
			die('form index not set');
		}elseif(!isset($this->uri->segments[4])){
			log_message('error', 'pmupload: field type not set');
			die('field type not set');
		}
		
		// initialiseer de private variabelen
		$this->init($this->uri->segments[3],$this->uri->segments[4]);
		
		$sourceFile;
		
		// wordt er een bestand geupload?
		if(isset($_FILES['image'])){
			$sourceFile = $this->saveUploadedFile();
		}
		// of is er een bronbestand waaruit we mogen schalen?
		elseif(isset($_POST['sourceFile'])){
			$sourceFile = $_POST['sourceFile'];
		}
		
		if(isset($sourceFile)){
			if(!is_file($sourceFile)){
				log_message('error','pmupload cannot access source file, !is_file(' . $sourceFile . ')');
				die('pmupload cannot access source file, !is_file(' . $sourceFile . ')');
			}else{
				$this->scaleUploadedImage($sourceFile);
			}
		}else{
			$this->printUploadForm();
		}
	}
	
	private function init($formIndex,$type){
		
		// de upload gaat via een form in een iframe via een index
		$this->formIndex = $formIndex;
		
		$frf = $this->config->item('db_dir') . 'frf';
		if(!file_exists($frf)){
			log_message('error','nonexistent contentfile management file: ' . $frf);
			die();
		}
		$rfr = unserialize(file_get_contents($frf));
		$datafile = $this->config->item('db_dir') . 'content_' . $rfr[sizeof($rfr)-1];
		if(!is_file($datafile)){
			log_message('error','nonexistent contentfile: ' . $datafile);
			die();
		}
		if(!$contentxml = simplexml_load_file($datafile)){
			log_message('error','cannot create xml out of ' . $datafile);
			die();
		}
		
		// het veldtype zou zich moeten bevinden binnen de visuale veldtypen
		$this->dimensions = $contentxml->db->fieldTypes->visuals->{$type};
		if(!$this->dimensions){
			log_message('error','no dimensions found for ' . $type);
			die('scale error: '.$type.' is unknown fieldType');
		}
		$this->fieldType = $type;
		
		log_message('debug', 'arguments: 1-(formIndex): ' . $this->formIndex . ', 2-(type): ' . $this->fieldType);
		
		$this->findScaleMode();
	}
	
	private function isUndevidedNumeric($value){
		$float = (float) $value;
		$int = (int) $value;
		if($float - $int == 0){
			return true;
		}else{
			return false;
		}
	}
	
	private function isPrescribedDimension($value){
		if(is_object($value)){
			$value = (string) $value;
		}
		log_message('debug', 'isPrescribedDimension: ' . $value . '?' . ' gettype = ' . gettype($value));
		
		if(!is_numeric($value)){
			log_message('debug', 'not numeric: ' . $value);
			return false;
		}else{
			log_message('debug', 'numeric: ' . $value);
		}
		if(!$this->isUndevidedNumeric($value)){
			log_message('debug', 'not undevided: ' . $value);
			return false;
		}else{
			log_message('debug', 'undevided: ' . $value);
		}
		return true;
	}
	
	private function findScaleMode(){
		
		// de gewenste afmetingen voor dit type staan in de definitie in de database cq. content file	
		function dimensionsError(){
			log_message('error','invalid dimensions definition found for ' . $type);
			die('scale error: '.'invalid dimensions definition found for ' . $type);
		}
		
		// de schalingsmodaliteit kan worden opgemaakt uit de 'dimensions' property
		if(isset($this->dimensions->height)&&isset($this->dimensions->width)){
			// er kan sprake zijn van één (horizontaal of verticaal) voorgeschreven dimensie, of beide
			// ingeval van één, mag de andere een regexp zijn met een marge, waarbij in het algemeen geldt, tot 2000
			// een zonder meer voorgeschreven waarde is een ongedeeld numerieke en positieve string
			if($this->isPrescribedDimension($this->dimensions->width) && $this->isPrescribedDimension($this->dimensions->height)){
				$this->scaleMode = $this->scaleModeOptions['bothDimensionsPrescribed'];
			}else{
				if($this->isPrescribedDimension($this->dimensions->height)){
					$this->scaleMode = $this->scaleModeOptions['proportionallyToPrescribedHeight'];
				}else if($this->isPrescribedDimension($this->dimensions->width)){
					$this->scaleMode = $this->scaleModeOptions['proportionallyToPrescribedWidth'];
				}else{
					log_message('error','invalid width/height parameters. One dimension needs to be prescribed');
					die('invalid width/height parameters. One dimension needs to be prescribed');
				}
			}
		}else{
			if(isset($this->dimensions->scale)){
				if($this->dimensions->scale == 'proportional'){
					$this->scaleMode = $this->scaleModeOptions['proportionallyToInputPercentage'];
				}else{
					dimensionsError();
				}
			}else{
				dimensionsError();
			}
		}
	}
	
	private function printUploadForm(){
		
		// het is fijn als de gebruiker weet dat er verschillende types plaatjes zijn
		$this->explainUploadForm .= ' Binnen deze layout is dit een plaatje van het type: ' . (string) $this->fieldType . '. Je kunt dus ';
		
		// de toelichting kan verschillen afhankelijk van de inrichting van de dimensies
		if($this->scaleMode == $this->scaleModeOptions['bothDimensionsPrescribed']){
			$this->explainUploadForm .= 'een plaatje uploaden wat naar vastgestelde afmetingen van ' . 
				$this->dimensions->width . 'x' . $this->dimensions->height .
				' zal worden geschaald. Als de verhoudingen erg afwijken wordt er in de lengte of breedte aan beide kanten iets afgehaald. Kies een bestand om het plaatje te uploaden of te vervangen. Na het kiezen en uploaden van het plaatje wordt het geschaalde resultaat op deze plek getoond.';
		}
		elseif($this->scaleMode == $this->scaleModeOptions['proportionallyToPrescribedWidth']){
			$this->explainUploadForm .= 'een plaatje uploaden wat naar een vastgestelde breedte van ' . $this->dimensions->width . 
				' zal worden geschaald. De hoogte wordt proportioneel gerelateerd aan de vastgestelde breedte. ' . 
				'Kies een bestand om het plaatje te uploaden of te vervangen. ' . 
				'Na het kiezen en uploaden van het plaatje wordt het geschaalde resultaat op deze plek getoond.';
		}
		elseif($this->scaleMode == $this->scaleModeOptions['proportionallyToPrescribedHeight']){
			$this->explainUploadForm .= 'een plaatje uploaden wat naar een vastgestelde hoogte van ' . $this->dimensions->height . 
				' zal worden geschaald. De breedte wordt proportioneel gerelateerd aan de vastgestelde hoogte. ' . 
				'Kies een bestand om het plaatje te uploaden of te vervangen. ' . 
				'Na het kiezen en uploaden van het plaatje wordt het geschaalde resultaat op deze plek getoond.';
		}
		elseif($this->scaleMode == $this->scaleModeOptions['proportionallyToInputPercentage']){
			$this->explainUploadForm .= 'een plaatje uploaden en vervolgens zelf het schalingspercentage opgeven.' . 
				' Na het kiezen en uploaden van een bestand kan het schalingspercentage worden opgegeven en wordt het resultaat getoond';
		}
		
		print(	"<form name='" . $this->formIndex . "' action='' method='post' enctype='multipart/form-data'>
			<span style='font-size:11px;'>" . $this->explainUploadForm . "</span>
			<input id='file' type='file' name='image' onChange='window.parent.fcf.v.cms.upload(this);' /><br>
			<span style='font-size:11px; color:#666666;'>only gif, png, jpg files.</span>
			<input type='hidden' value='" . $this->fieldType . "' name='field_type' />
			</form>"
		);
	}
	
	private function saveUploadedFile(){
		
		log_message('debug', 'saveUploadedFile()');
		log_message('debug', 'tmp_name: '.$_FILES['image']['tmp_name']);
		log_message('debug', 'name: '.$_FILES['image']['name']);
		//log_message('debug', 'div_id: '.$_POST['div_id']);
		log_message('debug', 'type: '.$_FILES['image']['type']);
		
		$ftmp = $_FILES['image']['tmp_name'];
		$oname = $_FILES['image']['name'];
		//$div_id = $_POST['div_id'];

		$type = @explode('/', $_FILES['image']['type']);
		$type = isset($type[1]) ? $type[1] : '';

		$type = ($type != 'pjpeg') ? $type : 'jpeg';

		$img_types = array('jpg', 'jpeg', 'gif', 'png', 'x-png');
		
		// check for image type
		if ( !in_array($type, $img_types)) {
			log_message('error','saveUploadedFile: this image type is not an option: ' . $type);
			die('saveUploadedFile: this image type is not an option: ' . $type);
		}
		
		// create filename and move uploaded file to its storage location
		$file_temp_name = substr(md5(time()), 0, 14) . 'n' . '.' . $type;
		$fname = $this->config->item("uploaded_dir") . $file_temp_name;
		$this->imageUrl = $this->config->item("uploaded_url") . $file_temp_name;
		if (!move_uploaded_file($ftmp, $fname)){
			log_message('error','saveUploadedFile: could not move ' . $ftmp . ' to ' . $fname);
			die('saveUploadedFile: could not move ' . $ftmp . ' to ' . $fname);
		}
		log_message('debug','stored ' . $fname);
		return $fname;
	}
	
	private function scaleUploadedImage($sourceFile){
		
		log_message('debug','scaleUploadedImage()');
		
		// retrieve information from image
		if(!($aSize = getimagesize($sourceFile))){
			log_message('error','scaleUploadedImage error: '.' could not get image information on ' . $sourceFile);
			die('scaleUploadedImage error: '.' could not get image information on ' . $sourceFile);
		}
		
		/* different scale functions
		private $scaleModeOptions = Array(
			'bothDimensionsPrescribed' 				=> 1, 
			'proportionallyToPrescribedWidth' 		=> 2,
			'proportionallyToPrescribedHeight'		=> 3,
			'proportionallyToInputPercentage'		=> 4
		);
		*/
		
		if ($this->scaleMode == $this->scaleModeOptions['bothDimensionsPrescribed']){
			$this->scaleBoth($sourceFile,$aSize);
		}
		elseif ($this->scaleMode == $this->scaleModeOptions['proportionallyToPrescribedWidth']){
			$this->scaleToWidth($sourceFile,$aSize);
		}
		elseif ($this->scaleMode == $this->scaleModeOptions['proportionallyToPrescribedHeight']){
			$this->scaleToHeight($sourceFile,$aSize);
		}
		elseif ($this->scaleMode == $this->scaleModeOptions['proportionallyToInputPercentage']){
			$this->scaleToPercentage($sourceFile,$aSize);
		}
	}
	
	private function scaleBoth($sourceFile,$sourceInfo){
		
		log_message('debug','scaleBoth()');
		$this->sWidth = $sourceInfo[0];;
		$this->sHeight = $sourceInfo[1];
		// set the target file name for the newly scaled image
		$targetFile = substr(md5( time()), 0, 14) . 'n' . '.jpeg';
		$targetFileWithPath = $this->config->item("uploaded_dir") . $targetFile;
		$targetFileUrl = $this->config->item("uploaded_url") . $targetFile;
		$this->scale($sourceFile, $sourceInfo['mime'],$targetFileWithPath, $targetFileUrl);
	}
	private function scaleToWidth($sourceFile,$sourceInfo){
		
		log_message('debug','scaleToWidth()');
		
		// get the range for the resulting height
		$minmaxHeight = $this->tryMinMaxRegExp($this->dimensions->height);
		$heightMin = $minmaxHeight[0];
		$heightMax = $minmaxHeight[1];
		if($heightMin < 0 || $heightMax <= $heightMin){
			log_message('error','geen goede reg exp voor visual type height');
			die('geen goede reg exp voor visual type height');
		}
		$this->sWidth = $sourceInfo[0];
		$this->sHeight = $sourceInfo[1];
		$adaptedRatio = $this->dimensions->width / $this->sWidth;
		if($this->sHeight * $adaptedRatio > $heightMax || $this->sHeight * $adaptedRatio < $heightMin){
			$mess = 'cannot scale proportionally within margin of heightmin=' . $heightMin . ' and heightmax=' . $heightMax . 
				' with a fixed target width of ' . strval($this->dimensions->width);
			log_message('debug',$mess);
			die($mess);
		}
		$this->dimensions->height = $this->sHeight * $adaptedRatio;
		
		// set the target file name for the newly scaled image
		$targetFile = substr(md5( time()), 0, 14) . 'n' . '.jpeg';
		$targetFileWithPath = $this->config->item("uploaded_dir") . $targetFile;
		$targetFileUrl = $this->config->item("uploaded_url") . $targetFile;
		
		$this->scale($sourceFile, $sourceInfo['mime'], $targetFileWithPath, $targetFileUrl);
	}
	private function scaleToHeight($sourceFile,$sourceInfo){
		
		log_message('debug','scaleToHeight()');
		// get the range for the resulting width
		$minmaxWidth = $this->tryMinMaxRegExp($this->dimensions->width);
		$widthMin = $minmaxWidth[0];
		$widthMax = $minmaxWidth[1];
		if(($widthMin < 0 ) || ($widthMax <= $widthMin)){
			log_message('error','geen goede reg exp voor visual type width');
			die('geen goede reg exp voor visual type width');
		}
		$this->sWidth = $sourceInfo[0];
		$this->sHeight = $sourceInfo[1];
		$adaptedRatio = $this->dimensions->height / $this->sHeight;
		if($this->sWidth * $adaptedRatio > $widthMax || $this->sWidth * $adaptedRatio < $widthMin){
			$mess = 'cannot scale proportionally within margin of widthmin=' . $widthMin . ' and widthmax=' . $widthMax . 
				' with a fixed target height of ' . strval($this->dimensions->height);
			log_message('debug',$mess);
			die($mess);
		}
		$this->dimensions->width = $this->sWidth * $adaptedRatio;
		
		// set the target file name for the newly scaled image
		$targetFile = substr(md5( time()), 0, 14) . 'n' . '.jpeg';
		$targetFileWithPath = $this->config->item("uploaded_dir") . $targetFile;
		$targetFileUrl = $this->config->item("uploaded_url") . $targetFile;
		$this->scale($sourceFile, $sourceInfo['mime'],$targetFileWithPath, $targetFileUrl);
	}
	
	private function showErrorAndRetryForm($mess,$sourceFile){
		$retryHtml = "<form name='" . $this->formIndex . "' action='' method='post' enctype='multipart/form-data'>
			<span style='font-size:11px;'>voer een percentage in voor het schalen van het plaatje</span>
			<input id='scale' type='input' name='scale' size='3' maxchars='3'>%<br>
			<input type='submit' value='Submit' onSubmit='window.parent.fcf.v.cms.upload(this);' /><br>
			<input type='hidden' value='" . $this->fieldType . "' name='field_type' />
			<input type='hidden' value='" . $sourceFile . "' name='sourceFile' />
			</form>";
		print($mess . $retryHtml);
	}
	private function calculateMaximumPercentage($sourceFile){
		if(property_exists($this->dimensions,'maxWidth')){
			$wMax = 100 * ( (int)$this->dimensions->maxWidth / $this->sWidth  );
		}
		if(property_exists($this->dimensions,'maxHeight')){
			$hMax = 100 * ( (int)$this->dimensions->maxHeight / $this->sHeight  );
		}
		$pMax = (isset($wMax)) ? $wMax : (isset($hMax)) ? $hMax : 0;
		$pMax = ( isset($hMax) && isset($wMax) && $hMax < $wMax ) ? $hMax : $pMax;
		if($pMax == 0){
			$this->showErrorAndRetryForm('je kan elk percentage boven nul gebruiken',$sourceFile);
		}else{
			$this->showErrorAndRetryForm('het maximaal toegestane percentage voor dit plaatje in deze context is: ' . (int)$pMax . '%',$sourceFile);
		}
	}
	private function scaleToPercentage($sourceFile,$sourceInfo){
		
		log_message('debug','scaleToPercentage()');
		
		$scale;
		
		
		
		
		
		if(!isset($_POST['scale'])){
			// print form for percentage
			print("<html><head><script language='javascript'>window.parent.fcf.v.cms.setUploadedImage('" . $this->imageUrl . "', '" . $this->formIndex . "');</script></head>
			<body>
			<form name='" . $this->formIndex . "' action='' method='post' enctype='multipart/form-data'>
			<span style='font-size:11px;'>voer een percentage in voor het schalen van het plaatje</span>
			<input id='scale' type='input' name='scale' size='3' maxchars='3'>%<br>
			<input type='submit' value='Submit' onSubmit='window.parent.fcf.v.cms.upload(this);' /><br>
			<input type='hidden' value='" . $this->fieldType . "' name='field_type' />
			<input type='hidden' value='" . $sourceFile . "' name='sourceFile' />
			</form>
			</body></html>");
			return;
		}
		
		
		
		
		// scale, show error or result and print retry form
		if(!is_numeric($_POST['scale'])){
			$this->showErrorAndRetryForm('graag een heel getal hoger dan nul voor het schalingspercentage',$sourceFile);
			return;
		}
		$sfloat = (float) $_POST['scale'];
		$sint = (int) $_POST['scale'];
		if($sfloat - $sint != 0){
			$this->showErrorAndRetryForm('graag een heel getal voor het schalingspercentage',$sourceFile);
			return;
		}
		if($sint <= 0){
			$this->showErrorAndRetryForm('graag een positief getal voor het schalingspercentage',$sourceFile);
			return;
		}
		
		// at this point we have a correct scale var
		$scale = $sint;
		
		// source dimensions
		$this->sWidth = $sourceInfo[0];
		$this->sHeight = $sourceInfo[1];
		
		// set the targeted width and height
		$this->dimensions->width = $this->sWidth * $scale * 0.01;
		$this->dimensions->height = $this->sHeight * $scale * 0.01;
		
		//if maxima are set, check
		if(property_exists($this->dimensions,'maxWidth')){
			log_message('debug','maxWidth exists: ' . $this->dimensions->maxWidth);
			if( $this->dimensions->width > (int)$this->dimensions->maxWidth ){
				$this->calculateMaximumPercentage($sourceFile);
				return;
			}
		}
		if(property_exists($this->dimensions,'maxHeight')){
			log_message('debug','maxHeight exists: ' . $this->dimensions->maxHeight);
			if( $this->dimensions->height > (int)$this->dimensions->maxHeight ){
				$this->calculateMaximumPercentage($sourceFile);
				return;
			}
		}
		
		// set the target file name for the newly scaled image
		$targetFile = substr(md5( time()), 0, 14) . 'n' . '.jpeg';
		$targetFileWithPath = $this->config->item("uploaded_dir") . $targetFile;
		$targetFileUrl = $this->config->item("uploaded_url") . $targetFile;
		
		$this->scale($sourceFile, $sourceInfo['mime'],$targetFileWithPath, $targetFileUrl);
	}
		
	// helper function for reg exps expressing a range
	private function tryMinMaxRegExp($regExp){
		$fRegExp = '/' . $regExp . '/';
		log_message('debug','tryMinMaxRegExp(' . $fRegExp .  ')');
		$min = -1;
		$max = 0;
		$tryUpTo = 2001;
		$iter = 0;
		while($iter < $tryUpTo){
			if(preg_match($fRegExp,strval($iter))){
				if($min==-1){
					$min = $iter;
				}
				$max = $iter;
			}
			$iter++;
		}
		if($min == -1){
			$min=0;
		}
		log_message('debug','min=' . $min . ', max=' . $max);
		return array($min,$max);
	}
	
	private function scale($sourceFile,$mimeType,$targetFile,$targetUrl){
		
		log_message('debug','creating destination image of ' . $this->dimensions->width . 'x' . $this->dimensions->height);
		$dst_img = imagecreatetruecolor((int)$this->dimensions->width,(int)$this->dimensions->height);
		// Creating the source image.
		switch($mimeType) 
		{
			case 'image/jpeg':
			case 'image/pjpeg':
			case 'image/jpg':
				$rResized = imagecreatefromjpeg($sourceFile);
				break;
			case 'image/gif':
				$rResized = imagecreatefromgif($sourceFile);
				break;
			case 'image/png':
			case 'image/x-png':
				$rResized = imagecreatefrompng($sourceFile);
				break;
			default:
				log_message('error','scale error: '.' corrupt image information on ' . $sourceFile);
				die('scale error: '.' corrupt image information on ' . $sourceFile);
		}
		
		// first we crop, to have the targeted dimensions without distorting the original h:w ratio
		$targetWidthPerHeightPx = $this->dimensions->width / $this->dimensions->height;
		log_message('debug', 'targeted is '.$this->dimensions->width.' devided by '.$this->dimensions->height.': '.$targetWidthPerHeightPx);
		$sourceWidthPerHeightPx = $this->sWidth / $this->sHeight;
		log_message('debug', 'source is '.$this->sWidth.' devided by '.$this->sHeight.': '.$sourceWidthPerHeightPx);
		
		$srcX = 0;
		$srcY = 0;
		
		if($targetWidthPerHeightPx == $sourceWidthPerHeightPx){
			//no cropping is needed
			$srcX = 0;
			$srcY = 0;
		}else if($targetWidthPerHeightPx > $sourceWidthPerHeightPx){
			// image is too high, crop vertically
			$targetUncroppedHeight = $this->sWidth / $targetWidthPerHeightPx;
			//log_message('debug','image is too high, rather read out a height of '.$targetUncroppedHeight);
			$srcY = (int)(($this->sHeight - $targetUncroppedHeight) / 2);
			$this->sHeight = (int)$targetUncroppedHeight;
		}else{
			// image is too wide, crop horizontally
			$targetUncroppedWidth = $this->sHeight * $targetWidthPerHeightPx;
			//log_message('debug','image is too wide, rather read out a width of '.$targetUncroppedWidth);
			$srcX = (int)(($this->sWidth - $targetUncroppedWidth) / 2);
			$this->sWidth = (int)$targetUncroppedWidth;
		}
		
		
		imagecopyresampled(
			$dst_img,
			$rResized,
			0,
			0,
			$srcX,
			$srcY,
			(int)$this->dimensions->width,
			(int)$this->dimensions->height,
			$this->sWidth,
			$this->sHeight
		);
		
		log_message('debug','storing as jpeg ' . $targetFile);
		
		imagejpeg($dst_img,$targetFile,100);
		
		// Cleaning the memory.
		imagedestroy($rResized);
		imagedestroy($dst_img);
		
		if($this->scaleMode == $this->scaleModeOptions['proportionallyToInputPercentage']){
			print("<html><head><script language='javascript'>window.parent.fcf.v.cms.setUploadedImage('" . $targetUrl . "', '" . $this->formIndex . "');</script></head>
				<body>
				<form name='" . $this->formIndex . "' action='' method='post' enctype='multipart/form-data'>
				<span style='font-size:11px;'>voer een percentage in voor het schalen van het plaatje</span>
				<input id='scale' type='input' name='scale' size='3' maxchars='3'>%<br>
				<input type='submit' value='Submit' onSubmit='window.parent.fcf.v.cms.upload(this);' /><br>
				<input type='hidden' value='" . $this->fieldType . "' name='field_type' />
				<input type='hidden' value='" . $sourceFile . "' name='sourceFile' />
				</form>
				</body></html>");
		}else{
			print("<html><head><script language='javascript'>window.parent.fcf.v.cms.setUploadedImage('" . $targetUrl . "', '" . $this->formIndex . "');</script></head>
				<body>");
			$this->printUploadForm();
			print("</body></html>");
		}
	}
}
?>