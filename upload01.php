<?php
//PLEASE, READ THESE ARTICLES BEFORE YOU ~DO SHIT~ DO ANYTHING
// see more here:
// http://php.net/manual/pt_BR/function.extract.php
// and here too:
// http://php.net/manual/pt_BR/reserved.variables.files.php

//PROTECTION
if(!isset($_POST)) die('DUDE, SEND A POST DATA');

/* VARIABLES CONFIGURATION */
$src = $_SERVER["DOCUMENT_ROOT"]."/uploads/";

//if true that file will receive a new name
$new_name = true;

//extensions to accept on uploading
$extensions = ["exe"];

//if true will allow just files with extensions on $extensions
$protect = false;

//max size file in bytes
$max_size = 5*1024*1024;//5 MB

$i = 0;
// Reordering array. Thanks to sergio_ag
// http://php.net/manual/pt_BR/reserved.variables.files.php#109958
function diverse_array($vector) { 
    $result = []; 
    foreach($vector as $key1 => $value1) 
        foreach($value1 as $key2 => $value2) 
            $result[$key2][$key1] = $value2; 
    return $result; 
} 

if(!$_FILES) die(json_encode(["error" => '$_FILES[] not found']));
$json = [];
$_FILES = diverse_array($_FILES["files"]);;
foreach ($_FILES as $_FILE) {
	
	$allow = true;
	extract($_FILE);
	$tmp = explode('.', $name);	
	$file_extension = strtolower(end($tmp));
	if($protect == true){
		
		if (array_search($file_extension, $extensions) === false):			
			$allow = false;
		endif;
	}

	if($new_name == true){
		$name = uniqid().".".$file_extension;
	}

	if($size <= $max_size && $allow == true){
		//so, upload that
		if (move_uploaded_file($tmp_name, $src . $name)){
			//image uploaded, do WTFYW
			$json[] = ["code" => "success", "file" => $i];			
		}else{
			$json[] = ["code" => "error", "file" => $i, "error" => "Some error occurred on file nº {$i}. Do you have permission 777 on $src?"];
		}
	}else{
		//image uploaded, do WTFYW
		$json[] = ["code" => "error", "file" => $i, "error" => "File nº {$i} is very large, or is not allowed. Max size = {$max_size} bytes. Your file = $size bytes."];
	}
	
	$i++;
}

die(json_encode(["files" => $json]));