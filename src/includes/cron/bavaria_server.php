<?php
mysql_connect('localhost', 'basmidn80', '23bmil06');
mysql_select_db('basmidn80_glybeforum');
$fp = fsockopen('bavaria.static-gly.be', '80', $errno, $errstr, 10);
if($fp)
{
	$online = 1;
	fclose($fp);
}
else
{
	$online = 0;
}
echo $online;

mysql_query("INSERT INTO bavariaserver (date, online) VALUES (NOW(), '" . $online . "')");
/*
$fp = fsockopen('bavaria.static-gly.be', '8888', $errno, $errstr, 10);
if($fp)
{
	$online = 1;
	fclose($fp);
}
else
{
	$online = 0;
}
echo $online;
mysql_query("INSERT INTO amstelserver (date, online) VALUES (NOW(), '" . $online . "')");
*/
?>