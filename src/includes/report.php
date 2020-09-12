<?php
include'inc.bootstrap.php';

if(!isset($user)){
	echo '<div class="error_notif success">Je bent niet ingelogd.</div>';
	die;
}

if(!$user->HasPermissions("is_team")){
	echo '<div class="error_notif error">Je hebt niet de juiste permissies om dit te bekijken.</div>';
	echo '<script>setTimeout("document.location=\'/404\';", 2222);</script>';
	die;
}

if(isset($_POST['msg']))
{
	$time = time();
	DB::Query("INSERT INTO forum_reports (user_from, post_id, date, reason) VALUES ('" . $user->Id . "', '" . DB::Escape($_POST['id']) . "', '" . $time . "', '" . DB::Escape($_POST['msg']) . "')");
}

if(isset($_POST['id']))
{
	$query = DB::Query("SELECT * FROM forum_posts WHERE id = '" . DB::Escape($_POST['id']) . "'");
	if(DB::NumRows($query) > 0)
	{
		?>
		<script type="text/javascript">
		function postReport()
		{
			var msg = $('#reden').val();
			$.post("/includes/report.php", { msg: msg, id: <?= $_POST['id']; ?> },
			function(data)
			{
				$('#cc').html('<div class="error_notif success">Je report is verzonden, er wordt zo snel mogelijk naar gekeken!</div>');
			});
		}
		</script>
		<h2>Post reporten</h2>
		<div id="cc">
			Geef hieronder een <strong>duidelijke</strong> reden waarom je deze post report!<br /><br />
			<textarea id="reden" name="reden" style="height: 150px; width: 95%;"></textarea><br />
			<input type="submit" onClick="postReport();" value="Verzend" />
		</div>
		<?php
	}
	else
	{
		echo '<div class="error_notif error">Deze post bestaat niet!</div>';
	}
}
?>