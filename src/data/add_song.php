<?php
include'../includes/inc.bootstrap.php'

if(isset($_GET['sh']) && isset($_GET['stok']))
{
	$sh = DB::Escape($_GET['sh']);
	$stok = DB::Escape($_GET['stok']);
	
	if($stok == sha1("bmcom-glybe-add-song::serv.bavaria"))
	{
		DB::Query("UPDATE `music_songs` SET `ready` = 'true' WHERE `song_hash` = '" . $sh . "'");
	}
}
?>