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
<div class="heading"><div class="icon user_add"></div><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?> verwijderen<div class="icon cross" style="float: right; margin-right: 0px;" onclick="Glybe.Overlay.Close();"></div></div>
<div class="inner" style="text-align: center;">
	<?php
	if(DB::NumRowsQuery("SELECT 1 FROM users_friends WHERE (user_one_id = '" . $user->Id . "' AND user_two_id = '" . $u->Id . "' OR user_two_id = '" . $user->Id . "' AND user_one_id = '" . $u->Id . "')") > 0) {
		if(isset($_POST['act']) && $_POST['act'] == 'del') {
		DB::Query("DELETE FROM users_friends WHERE user_one_id = '" . $user->Id . "' AND user_two_id = '" . $u->Id . "'");
		DB::Query("DELETE FROM users_friends WHERE user_two_id = '" . $user->Id . "' AND user_one_id = '" . $u->Id . "'");
		?>
		Je bent niet langer bevriend met<br/><strong><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?></strong><br/>
		<input type="button" value="Ok&eacute;" onclick="Glybe.Overlay.Close(); window.location.reload(true);" />
		<?php } else { ?>
		Weet je 100% zeker dat je <strong><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?></strong> wilt verwijderen uit je vriendenlijst? Maak een keuze.<br/>
		<input type="button" value="Verwijderen" onclick="Glybe.Overlay.OpenUrlOverlay('/data/vriend_verwijderen.php', { uId: '<?php echo $u->Id; ?>', act: 'del' });" />
		<input type="button" value="Annuleren" onclick="Glybe.Overlay.Close();" />
		<?php } ?>
	<?php } else { ?>
		<div class="error_notif error">Je bent niet bevriend met<br/><strong><?php echo(htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname"))) ? $u->GetSetting("displayname") : $u->Username))); ?></strong></i></div>
	<?php } ?>
</div>