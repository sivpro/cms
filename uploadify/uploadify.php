<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

$docroot = $_SERVER['DOCUMENT_ROOT'];
if (substr($docroot, -1) == "/") {
	$docroot = substr($docroot, 0, strlen($docroot)-1);
}
define("DOC_ROOT", $docroot);


if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	
	$fn = $_FILES['Filedata']['name'];
	$n = strrpos($fn, ".");
	$ext = substr($fn, $n);
	$fn = (rand(0, 255) + 256 * rand(0, 255) + 65536 * rand(0, 255)) . time() . $ext;

	$targetPath = DOC_ROOT . $_REQUEST['folder'] . '/';
	$targetFile =  $targetPath . $fn;
		
		
	$fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	$fileTypes  = str_replace(';','|',$fileTypes);
	$typesArray = split('\|',$fileTypes);
	$fileParts  = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array($fileParts['extension'],$typesArray)) {
		
		move_uploaded_file($tempFile, $targetFile);
		echo $fn;
	}
	else {
		echo 'Invalid file type.';
	}
}
?>