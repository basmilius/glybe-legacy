<?php
include'../includes/inc.bootstrap.php';
$uId = ((isset($_POST['pId']) && is_numeric($_POST['pId'])) ? DB::Escape($_POST['pId']) : 0);
$gId = ((isset($_POST['gId']) && is_numeric($_POST['gId'])) ? DB::Escape($_POST['gId']) : 0);
$qId = ((isset($_POST['qId']) && is_numeric($_POST['qId'])) ? DB::Escape($_POST['qId']) : 1);

if(DB::NumRowsQuery("SELECT 1 FROM users WHERE id = '" . $uId . "'") === 0 || $gId == 0 || !isset($user) || DB::NumRowsQuery("SELECT 1 FROM profile_guestbook WHERE profile_id = '" . $user->Id . "' AND deleted = 'false' AND id = '" . $gId . "'") === 0 || $uId != $user->Id)
{
	die('<script type="text/javascript">Glybe.Overlay.Close();</script>');
}

$u = new User($uId, false, true, true);
?>
<script type="text/javascript">
Glybe.Overlay.SetSize(400, 80);
Glybe.Profile.GetGuestbook(<?php echo $uId; ?>, <?php echo $qId; ?>);
</script>
<div class="heading"><div class="icon delete"></div>Bericht verwijderen<div class="icon cross" style="float: right; margin-right: 0px;" onclick="Glybe.Overlay.Close();"></div></div>
<div class="inner" style="text-align: center;">
	<?php
	DB::Query("UPDATE profile_guestbook SET deleted = 'true' WHERE id = '" . $gId . "'");
	?>
	<div class="error_notif success">Het bericht is uit je gastenboek verwijderd.</div>
	<input type="button" value="Ok&eacute;" onclick="Glybe.Overlay.Close();" />
</div>