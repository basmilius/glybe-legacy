<?php
include'../includes/inc.bootstrap.php';
if(!isset($user)) die();

$pId = ((isset($_POST['pId']) && is_numeric($_POST['pId'])) ? DB::Escape($_POST['pId']) : 0);
$aId = ((isset($_POST['aId']) && is_numeric($_POST['pId'])) ? DB::Escape($_POST['pId']) : 0);
$sToken = ((isset($_POST['sToken']) && is_numeric($_POST['sToken'])) ? DB::Escape($_POST['sToken']) : 0);

$success = false;
if($sToken == sha1(md5($pId . $aId)))
{
	if(DB::NumRowsQuery("SELECT 1 FROM poll_votes WHERE poll_id = '" . $pId . "' AND user_id = '" . $user->Id . "'") == 0 && DB::NumRowsQuery("SELECT 1 FROM poll_questions WHERE id = '" . $pId . "'") > 0)
	{
		DB::Query("INSERT INTO `poll_votes` (poll_id, answer_id, user_id, voted_on) VALUES ('" . $pId . "', '" . $aId . "', '" . $user->Id . "', UNIX_TIMESTAMP())");
		$success = true;
	}
}
?>
<script type="text/javascript">
Glybe.Overlay.SetSize(400, 80);
<?php if($success) { ?>
window.location.reload();
<?php } ?>
</script>
<div class="heading"><div class="icon thumb_up"></div>Stemmen<div class="icon cross" style="float: right; margin-right: 0px;" onclick="Glybe.Overlay.Close();"></div></div>
<div class="inner" style="text-align: center;">
	<?php if($success) { ?>
	<div class="error_notif success">Gestemd!</div>
	<?php } else { ?>
	<div class="error_notif error">Ongeldig verzoek.</div>
	<?php } ?>
</div>