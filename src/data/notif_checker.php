<?php
include'../includes/inc.bootstrap.php';
header("Content-Type: text/javascript");

$userId = ((isset($user)) ? $user->Id : 0);

$notifications = "";

$nQuery = DB::Query("SELECT * FROM `notifications` WHERE `user_id` = '" . $userId . "' AND `is_done` = '0' ORDER BY `id`");
while($nFetch = DB::Fetch($nQuery))
{
	DB::Query("UPDATE `notifications` SET `is_done` = '1' WHERE `id` = '" . $nFetch['id'] . "'");
	$u = new User($nFetch['user_from_id'], false, false, false);
	$notifications .= '["' . $nFetch['icon'] . '", "' . $nFetch['title'] . '", "' . $nFetch['message'] . '", "' . addslashes($u->GetAvatar(40)) . '", "' . $nFetch['url'] . '"],';
}

echo'{';
echo'"Notifications":[';
echo substr($notifications, 0, -1);
echo']';
echo'}';
?>