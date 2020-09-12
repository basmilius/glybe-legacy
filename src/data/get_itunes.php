<?php
header("Content-Type: text/javascript");
function GetData($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0');
	
	return curl_exec($ch);
}

$request = Array();
if(isset($_GET) && count($_GET) > 0)
{
	$request = array_merge($_GET);
}
if(isset($_POST) && count($_POST) > 0)
{
	$request = array_merge($_POST);
}
$params = "?version=2";
foreach($request as $key => $value)
{
	$params .= "&" . $key . "=" . urlencode($value);
}
$url = "";
echo GetData("http://itunes.apple.com/search" . $params);
?>