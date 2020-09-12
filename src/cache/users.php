<?php
include'../includes/inc.bootstrap.php';
header("Content-Type: text/javascript");

if(!isset($user)) die();

echo 'MyUser={';

$usersQuery = DB::Query("SELECT username, id FROM users ORDER BY username");
echo '"Users":["Alle Glybers",[';
$users = "";
while($usersFetch = DB::Fetch($usersQuery))
{
	if($usersFetch['id'] != 3)
	{
		$users .= '["' . $usersFetch['username'] . '", "Klik om toe te voegen.."],';
	}
}
echo substr($users, 0, -1);
echo ']],';

echo '"Friends":["Vrienden",[';
$users = "";
foreach($user->Friends as $key => $fUser)
{
	$myFriends = new User($fUser, false);
	if($myFriends->Id != 3)
	{
		$users .= '["' . $myFriends->Username . '", "Klik om toe te voegen.."],';
	}
}
echo substr($users, 0, -1);
echo ']],';

echo '"Ping":"Pong"';

echo '}';
?>