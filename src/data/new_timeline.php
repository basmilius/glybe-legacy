<?php
include'../includes/inc.bootstrap.php';

if(isset($_POST['a']))
{
	$msg = DB::Escape($_POST['a']);
	
	if(isset($user))
	{
		if(strlen(str_replace(" ", "", $msg)) >= 3 && strlen($msg) <= 500)
		{
			DB::Query("INSERT INTO `status_updates` (user_id, message, post_timestamp, last_update) VALUES ('" . $user->Id . "', '" . $msg . "', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			die('4');
		}
		die('2');
	}
	die('1');
}
die('0');
?>