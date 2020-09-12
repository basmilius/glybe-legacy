<?php
include'../includes/inc.bootstrap.php';
$uId = ((isset($_POST['uId']) && is_numeric($_POST['uId'])) ? DB::Escape($_POST['uId']) : 0);

if(DB::NumRowsQuery("SELECT 1 FROM users WHERE id = '" . $uId . "'") === 0 || !isset($user))
{
	die('<script type="text/javascript">Glybe.Overlay.Close();</script>');
}

$u = new User($uId, false, true, true);
?>
<script type="text/javascript">
Glybe.Overlay.SetSize(400, 100);
</script>
<div class="heading"><div class="icon group"></div>Vrienden-verzoek<div class="icon cross" style="float: right; margin-right: 0px;" onclick="Glybe.Overlay.Close();"></div></div>
<div class="inner" style="text-align: center;">
	<?php
	if(DB::NumRowsQuery("SELECT 1 FROM users_friends_requests WHERE user_to = '" . $user->Id . "' AND user_from = '" . $u->Id . "'") > 0) {
		if(isset($_POST['_act']) && $_POST['_act'] == 'acc') {
		DB::Query("DELETE FROM users_friends_requests WHERE user_to = '" . $user->Id . "' AND user_from = '" . $u->Id . "'");
		DB::Query("INSERT INTO users_friends (user_one_id, user_two_id) VALUES ('" . $user->Id . "', '" . $u->Id . "')");
		DB::Query("INSERT INTO `notifications` (`user_id`, `user_from_id`, `icon`, `title`, `message`, `url`, `n_ts`) VALUES ('" . $u->Id . "', '" . $user->Id . "', 'user_add', 'Vrienden-verzoek', '<strong>" . DB::Escape(htmlspecialchars(((str_replace(" ", "", $user->GetSetting("displayname")) != "") ? $user->GetSetting("displayname") : $user->Username))) . "</strong><br/>heeft je vrienden-verzoek geaccepteerd!', '/profiel/" . strtolower($user->Username) . "', UNIX_TIMESTAMP())");
		?>
		Je bent nu bevriend met<br/><strong><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?></strong><br/>
		<input type="button" value="Ok&eacute;" onclick="Glybe.Overlay.Close(); window.location.reload(true);" />
		<?php } else if(isset($_POST['_act']) && $_POST['_act'] == 'rem') { 
		DB::Query("DELETE FROM users_friends_requests WHERE user_to = '" . $user->Id . "' AND user_from = '" . $u->Id . "'");
		?>
		Het vrienden-verzoek van <strong><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?></strong> is geweigerd.<br/>
		<input type="button" value="Ok&eacute;" onclick="Glybe.Overlay.Close(); window.location.reload(true);" />
		<?php } ?>
	<?php } else { ?>
		<div class="error_notif error">Ongeldig verzoek</div>
	<?php } ?>
</div>