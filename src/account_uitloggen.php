<?php
include'includes/inc.bootstrap.php';

if(isset($user))
{
	$user->KillSession($_COOKIE[$glb_settings['cookie_us']], $_GET['hash']);
}

header("location: /?utm_source=logout_success");
?>