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
<div class="heading"><div class="icon user_add"></div><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?> toevoegen<div class="icon cross" style="float: right; margin-right: 0px;" onclick="Glybe.Overlay.Close();"></div></div>
<div class="inner" style="text-align: center;">
	<?php
	if(DB::NumRowsQuery("SELECT 1 FROM users_friends WHERE (user_one_id = '" . $user->Id . "' AND user_two_id = '" . $u->Id . "' OR user_two_id = '" . $user->Id . "' AND user_one_id = '" . $u->Id . "')") === 0 && DB::NumRowsQuery("SELECT 1 FROM users_friends_requests WHERE (user_to = '" . $user->Id . "' AND user_from = '" . $u->Id . "' OR user_to = '" . $u->Id . "' AND user_from = '" . $user->Id . "')") === 0) {
		if(isset($_POST['act']) && $_POST['act'] == 'add') {
		DB::Query("INSERT INTO `notifications` (`user_id`, `user_from_id`, `icon`, `title`, `message`, `url`, `n_ts`) VALUES ('" . $u->Id . "', '" . $user->Id . "', 'user_add', 'Vrienden-verzoek', '<strong>" . DB::Escape(htmlspecialchars(((str_replace(" ", "", $user->GetSetting("displayname")) != "") ? $user->GetSetting("displayname") : $user->Username))) . "</strong><br/>wilt vrienden met je worden op Glybe!', '/vrienden/requests', UNIX_TIMESTAMP())");
		DB::Query("INSERT INTO users_friends_requests (user_from, user_to, date_requested) VALUES ('" . $user->Id . "', '" . $u->Id . "', UNIX_TIMESTAMP())");
		?>
		Je hebt een vriendenverzoek gestuurd naar<br/><strong><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?></strong><br/>
		<input type="button" value="Ok&eacute;" onclick="Glybe.Overlay.Close();" />
		<?php } else { ?>
		Weet je 100% zeker dat je <strong><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?></strong> wilt toevoegen aan je vriendenlijst? Maak een keuze.<br/>
		<input type="button" value="Toevoegen" onclick="Glybe.Overlay.OpenUrlOverlay('/data/vriend_toevoegen.php', { uId: '<?php echo $u->Id; ?>', act: 'add' });" />
		<input type="button" value="Annuleren" onclick="Glybe.Overlay.Close();" />
		<?php } ?>
	<?php } else { ?>
		<div class="error_notif error">Je bent al bevriend met <strong><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?></strong>!<br/><i>Of er is al een verzoek verzonden naar deze persoon.</i></div>
	<?php } ?>
</div>