<?php
if(isset($page['access']))
{
	$access = $page['access'];
	if(!$access[0] && isset($user))
	{
		header("location: /home");
		die();
	} else if(!$access[1] && !isset($user))
	{
		header("location: /");
		die();
	}
	if(isset($access[2]) && isset($user) && !$user->HasPermissions($access[2]))
	{
		header("location: /error_403.html");
		die();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo((isset($page['title'])) ? $page['title'] : 'Home'); ?> // Glybe</title>
	<link rel="stylesheet" href="/cache/style_default/style.php?v=glb_v0.4.100" />
	<!--[if IE 7]>
	<link rel="stylesheet" href="/cache/style_default/ie7-fix.css?t=<?php echo time(); ?>" />
	<![endif]-->
	<link rel="image_src" href="http://www.glybe.nl/cache/images/user_avatars/0_default.png" />
	<link rel="shortcut icon" href="/cache/images/favicon.ico" type="image/x-icon" />
	<?php
	if(isset($page['css']))
	{
		foreach($page['css'] as $css)
		{
			echo'	<link rel="stylesheet" href="' . $css . '" />';
		}
	}
	?>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="title" content="Glybe, Online community" />
	<meta name="description" content="Op Glybe kun je samen met anderen kletsen over alles wat los en vast zit, ons doel is om de grootste gezellige community van Nederland te worden!" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="http://grolsch.static-gly.be/static-content-a/web-actions/glb.s.js"></script>
	<script type="text/javascript" src="http://grolsch.static-gly.be/static-content-a/web-actions/glybe.js#v=glb_v0.8.1"></script>
	<script type="text/javascript" src="/cache/users.php"></script>
	<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-22808198-4']);
	_gaq.push(['_setDomainName', 'glybe.nl']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	</script>
	<?php if(isset($user)){ ?>
	<script type="text/javascript">
	var US = {};
	// User.settings
	US['user.id'] = '<?php echo $user->Id; ?>';
	US['user.name'] = '<?php echo htmlspecialchars($user->Realname); ?>';
	
	US['sound.notif'] = '<?php echo $user->GetSetting('sound_notif'); ?>';
	</script>
	<?php } ?>
</head>
<body<?php echo((isset($page['class'])) ? ' class="' . $page['class'] . '"' : ''); ?>>