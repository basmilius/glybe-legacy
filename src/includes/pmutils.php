<?php
include'inc.bootstrap.php';

if(!isset($_POST['_cmd'])) die();
if(!isset($_POST['ids'])) die();
$ids = explode(',', Glybe::Security($_POST['ids']));

switch($_POST['_cmd'])
{
	case "mark_as_readed":
		{
			foreach($ids as $key => $value)
			{
				$pbQuery = DB::Query("SELECT * FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `id` = '" . $value . "'");
				if(DB::NumRows($pbQuery) === 0)
				{
					echo'Een van de berichten is niet gevonden!';
					die();
				} else {
					DB::Query("UPDATE `messages` SET `readed` = 'true' WHERE `id` = '" . $value . "'");
				}
			}
			die("ok");
		}
	case "mark_as_unreaded":
		{
			foreach($ids as $key => $value)
			{
				$pbQuery = DB::Query("SELECT * FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `id` = '" . $value . "'");
				if(DB::NumRows($pbQuery) === 0)
				{
					echo'Een van de berichten is niet gevonden!';
					die();
				} else {
					DB::Query("UPDATE `messages` SET `readed` = 'false' WHERE `id` = '" . $value . "'");
				}
			}
			die("ok");
		}
	case "move_to":
		{
			$folderId = Glybe::Security($_POST['data_a']);
			if(!is_numeric($folderId)) die("Dit is een ongeldige map.");
			if(DB::NumRowsQuery("SELECT 1 FROM messages_folders WHERE id = '" . $folderId . "' AND user_id = '" . $user->Id . "'") === 0 && $folderId != 0) die("Deze map is niet van jou of bestaat niet!");
			foreach($ids as $key => $value)
			{
				$pbQuery = DB::Query("SELECT * FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `id` = '" . $value . "'");
				if(DB::NumRows($pbQuery) === 0)
				{
					echo'Een van de berichten is niet gevonden!';
					die();
				} else {
					DB::Query("UPDATE `messages` SET `folder_id` = '" . $folderId . "', state = 'open' WHERE `id` = '" . $value . "'");
				}
			}
			die("ok");
		}
	case "remove":
		{
			foreach($ids as $key => $value)
			{
				$pbQuery = DB::Query("SELECT * FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `id` = '" . $value . "'");
				if(DB::NumRows($pbQuery) === 0)
				{
					echo'Een van de berichten is niet gevonden!';
					die();
				} else {
					DB::Query("UPDATE `messages` SET `state` = 'deleted' WHERE `id` = '" . $value . "'");
				}
			}
			die("ok");
		}
	case "create_folder":
		{
			$name = DB::Escape($_POST['data_a']);
			DB::Query("INSERT INTO `messages_folders` (user_id, caption) VALUES ('" . $user->Id . "', '" . $name . "')");
		}
}

echo'Ongeldige parameter.';
?>