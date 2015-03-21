<?php
$workingPath = 'd:/temp';
$cssFiles = $workingPath . '/css2';
// grab all style that have pixel values
$pattern = "#([\-\d\.]+)px#msi";
$files = glob($cssFiles . '/*-fixed.css');
foreach($files as $k => $v) {
	$outputCss = '';
	$basename = basename($v);
	$media = array();
	$normal = array();
	$cssCode = file_get_contents($cssFiles . '/' . $basename);
	// remove comments
	$cssCode = preg_replace("#\/\*[\S\s]*?\*\/#", '', $cssCode);
	// replace white-space
	$cssCode = str_replace("\r", '', $cssCode);
	$cssCode = str_replace("\n", '', $cssCode);
	$cssCode = str_replace("\t", '', $cssCode);
	//replace @media
	$cssCode = str_replace("@media", 'this1-is-media1-query', $cssCode);
	//create array based on previous replacement
	$cssCode = explode("this1-is-", $cssCode);
	var_dump($cssCode);
	// Separate @media styles and styles that are not related to @media
	foreach($cssCode as $val) {
		$mediaStyles = array();
		$val = trim($val);
		if(empty($val))
			continue;
		//if we found a line with @media style create an array
		if(strpos($val, 'media1-query') !== false) {
			$mediaStyles = explode("}}", $val);
			var_dump($mediaStyles);
			foreach($mediaStyles as $mediaLines) {
				$v = trim($mediaLines);
				if(empty($mediaLines))
					continue;
				$media[] = $mediaLines . '}}' . "\n";
			} //$mediaStyles as $mediaLines
		} //strpos($val, 'media1-query') !== false
		else {
			$normal[] = $val;
		}
	} //$cssCode as $val
	$cssCodeMedia = implode('', $media);
	$cssCode = implode('', $normal);
	var_dump('media', $cssCodeMedia);
	var_dump('css', $cssCode);
	// process styles that doesn't have any @media .nofx {color: red;}
	// so it would be .nofx {color: red;
	$newCode = explode("}", $cssCode);
	foreach($newCode as $cssCodeLine) {
		// create a new array, where [0] are class names and [1] are properties
		// [0] => .nofx, text | [1] => color: red;
		if(empty($cssCodeLine))
			continue;
		$cssCodeLine = trim($cssCodeLine);
		//create array of selector name and properties
		$cssCodeLineExp = explode("{", $cssCodeLine);
		// create a new array of all css properties in line
		$cssCodeLineProperties = explode(";", $cssCodeLineExp[1]);
		// new string
		$cssCodeCollected = '';
		// cycle through all properties
		foreach($cssCodeLineProperties as $cssCodeLineProperty) {
			if(empty($cssCodeLineProperty))
				continue;
			// if property has what we need then add it to string $cssCodeCollected
			if(preg_match($pattern, $cssCodeLineProperty)) {
				$cssCodeLineProperty = trim($cssCodeLineProperty);
				$cssCodeCollected .= $cssCodeLineProperty . ';';
			} //preg_match($pattern, $cssCodeLineProperty)
		} //$cssCodeLineProperties as $cssCodeLineProperty
		if(empty($cssCodeCollected))
			continue;
		// create a new sring and add to it every iteration the properties from above
		$outputCss .= $cssCodeLineExp[0] . '{' . $cssCodeCollected . "}\n\n";
	} //$newCode as $cssCodeLine
	// process code with @media
	// so it would be media1-query print {.nofx {color: red;} .test {color: red;
	$newCode = explode("}}", $cssCodeMedia);
	foreach($newCode as $cssCodeMediaLines) {
		$cssCodeMediaLines = trim($cssCodeMediaLines);
		if(empty($cssCodeMediaLines))
			continue;
		//so it would be this1-is-media1-query print { .nofx {color: red;} .test {color: red;}
		$cssCodeMediaLines = $cssCodeMediaLines . '}';
		//so it would be cssCodeMediaLines[1] => .nofx {color: red;} .test {color: red;}
		$cssCodeMediaLines = explode("{", $cssCodeMediaLines, 2);
		//so it would be .nofx {color: red;
		$cssInMedia = explode("}", $cssCodeMediaLines[1]);
		$cssCodeCollectedInMedia = '';
		foreach($cssInMedia as $rule) {
			if(empty($rule))
				continue;
			$rule = trim($rule);
			// create a new array, where [0] are class names and [1] are properties
			// [0] => .nofx, text | [1] => color: red;
			$ruleSeparated = explode("{", $rule);
			// create a new array of css properties
			$properties = explode(";", $ruleSeparated[1]);
			// new string
			$cssCodeInMediaCollected = '';
			// cycle through all properties
			foreach($properties as $property) {
				if(empty($property))
					continue;
				// if property has a px values add it to string $cssCodeInMediaCollected
				if(preg_match($pattern, $property)) {
					$property = trim($property);
					$cssCodeInMediaCollected .= $property . ';';
				} //preg_match($pattern, $property)
			} //$properties as $property
			if(empty($cssCodeInMediaCollected))
				continue;
			// create a new sring and add to it every iteration the properties from above
			$cssCodeCollectedInMedia .= $ruleSeparated[0] . '{' . $cssCodeInMediaCollected . "}\n\n";
		} //$cssInMedia as $rule
		$cssCodeMediaLines[0] = str_replace('media1-query', '@media', $cssCodeMediaLines[0]);
		if(!empty($cssCodeCollectedInMedia)) {
			$outputCss .= $cssCodeMediaLines[0] . '{' . "\n" . $cssCodeCollectedInMedia . '}' . "\n\n";
		} //!empty($cssCodeCollectedInMedia)
	} //$newCode as $cssCodeMediaLines
	$basename = explode('.', $basename);
	if(!empty($outputCss)) {
		file_put_contents($cssFiles . '/' . $basename[0] . '-only.css', $outputCss);
	} //!empty($outputCss)
} //$files as $k => $v
