<?php
/*
 * GLYBEFORUM
 * By Bas Milius
 * =======================
 * inc.bootstrap.php
 * Hoofdbestand van de website.
 */

$version = "Glybe V0.7.31";

$glb = Array(); // Instellingen Array
// Database Instellingen
$glb['db'] = Array();
$glb['db']['gebruiker'] = "DATABASE USERNAME";
$glb['db']['wachtwoord'] = "DATABASE WACHTWOORD";
$glb['db']['host'] = "localhost";
$glb['db']['db'] = "DATABASE DATABASE";

header("Content-Type: text/html; charset=utf-8");

/*if($_SERVER['HTTP_HOST'] != "www.glybe.nl")
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.glybe.nl" . $_SERVER['REQUEST_URI']);
	die();
}*/

function checkIfIsNlOrBe($ip)
{
	$landCodes = array (
		"be" => "belgie",
		"nl" => "nederland",
	);
	$gethostbyaddr = gethostbyaddr($ip);
	if($gethostbyaddr == $ip)
	{
		return "onbekend";
	}
	$explode = explode(".", $gethostbyaddr);
	foreach($explode as $key)
	{
		$ext = $key;
	}
	$land = $landCodes[$ext];
	if($land == "")
	{
		$land = "onbekend";
	}
	if($land == 'nederland' || $land == 'belgie')
	{
		return true;
	}
	else
	{
		return false;
	}
}

function removenewline($in)
{
	return str_replace(Array("	"), "", $in);
}

error_reporting(E_ALL);
session_start();
ob_start("removenewline");
setlocale(LC_ALL, 'nl_NL');

/* Parse time berekenen */
$pTime = microtime();
$pExplode = explode(" ", $pTime);
$pTime2 = $pExplode[0];
$pSec = date("U");
$pStart = $pTime2 + $pSec;

// Alles includen wat nodig is
include dirname(__FILE__) . '/settings.basic.php';
include dirname(__FILE__) . '/class.glybe.php';
include dirname(__FILE__) . '/class.db.php';
include dirname(__FILE__) . '/class.user.php';
include dirname(__FILE__) . '/class.thumb.php';
include dirname(__FILE__) . '/class.poll.php';
include dirname(__FILE__) . '/class.ubb.php';

// Verbinden met MySql-server
DB::Initialize($glb['db']);

// Gebruikers data, bijv. hoeveel zijn er online
$glb['users'] = Array();
$glb['users']['count'] = DB::NumRowsQuery("SELECT 1 FROM `users`");
$glb['users']['online'] = count(Glybe::GetOnlineUsersAsArray());
$glb['users']['most_online'] = Glybe::MostUsersOnline();
$glb['users']['most_online_date'] = Glybe::MostUsersOnlineDate();

// Kijken of er een gebruiker moet worden ingelogd, zo nee.. niks doen
if(isset($_COOKIE[$glb_settings['cookie_us']]))
{
	//Er bestaat een sessie-cookie dus we moeten eens gaan kijken of de gebruiker
	//daadwerkelijk is ingelogd door de web_sessions tabel te checken.
	$token = Glybe::Security($_COOKIE[$glb_settings['cookie_us']]);
	
	$sessionQuery = DB::Query("SELECT * FROM `web_sessions` WHERE `session_hash` = '" . $token . "' AND `user_ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `user_ua` = '" . Glybe::Security($_SERVER['HTTP_USER_AGENT']) . "' AND `session_end` > UNIX_TIMESTAMP()");
	if(DB::NumRows($sessionQuery) > 0)
	{
		$sessionFetch = DB::Fetch($sessionQuery);
		$user = new User($sessionFetch['user_id']);
	}
}

// Meeste gebruikers tegelijkertijd online ophalen en kijken of het verbroken is zoja; updaten..
$most_users_online_query = DB::Query("SELECT * FROM most_online");
$most_users_online_fetch = DB::Fetch($most_users_online_query);
if($most_users_online_fetch['number_of_users'] < count(Glybe::GetOnlineUsersAsArray()))
{
	DB::Query("UPDATE most_online SET date = NOW(), number_of_users = '" . count(Glybe::GetOnlineUsersAsArray()) . "'");
}
function jesseRound($n,$x=5)
{
	return $x * round($n / $x);
}
?>