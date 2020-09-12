<?php
include'../includes/inc.bootstrap.php';

if(isset($_POST['a']) && isset($_POST['b']) && isset($_POST['c']))
{
	$msg = DB::Escape($_POST['a']);
	$statusId = DB::Escape($_POST['b']);
	$sToken = DB::Escape($_POST['c']);
	
	if(isset($user))
	{
		if($sToken == sha1(md5($user->Id . $statusId)))
		{
			if(strlen(str_replace(" ", "", $msg)) >= 3 && strlen($msg) <= 500)
			{
				DB::Query("INSERT INTO `status_updates_replies` (status_id, user_id, message, post_timestamp) VALUES ('" . $statusId . "', '" . $user->Id . "', '" . $msg . "', UNIX_TIMESTAMP())");
				DB::Query("UPDATE `status_updates` SET `last_update` = UNIX_TIMESTAMP() WHERE `id` = '" . $statusId . "'");
				echo'	<div class="comment" id="' . sha1($statusId . ':' .  rand(10000, 99999) . ':' . DB::InsertId()) . '">
						<div class="profile_picture">
							<img src="' . $user->Avatar . '" height="36" width="36" alt="Profiel-foto" />
						</div>
						<div class="comment_content">
							<a href="/profiel/' . strtolower($user->Username) . '"><strong>' . htmlspecialchars(((str_replace(" ", "", $user->GetSetting("displayname")) != "") ? $user->GetSetting("displayname") : $user->Username)) . '</strong></a><br/>
							' . UBB::Parse($msg, false, 220) . '
						</div>
						<div class="clear"></div>
					</div>';
				die();
			}
			die('2');
		}
		die('0');
	}
	die('1');
}
die('0');
?>