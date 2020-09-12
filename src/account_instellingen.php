<?php
include'includes/inc.bootstrap.php';

if(isset($_POST['rpb_submit']) && isset($user))
{
	$on_gb_post = ((isset($_POST['rpb_on_gb']) && $_POST['rpb_on_gb'] == '1') ? true : false);
	$pbl_upd = ((isset($_POST['rpb_pbl_upd']) && $_POST['rpb_pbl_upd'] == '1') ? true : false);
	$snd_ntf = ((isset($_POST['rpb_snd_ntf']) && $_POST['rpb_snd_ntf'] == '1') ? 'true' : 'false');
	
	DB::Query("UPDATE `users_settings` SET `send_pb_on_gb_post` = '" . $on_gb_post . "', `public_updates` = '" . $pbl_upd . "', `sound_notif` = '" . $snd_ntf . "' WHERE `user_id` = '" . $user->Id . "'");
	header("location: /account/instellingen?utm_source=rpb_saved");
	die();
}

if(isset($_POST['forum_submit']) && isset($user))
{
	$onderschrift = DB::Escape($_POST['forum_onderschrift']);
	DB::Query("UPDATE `users_settings` SET `onderschrift` = '" . $onderschrift . "' WHERE `user_id` = '" . $user->Id . "'");
	header("location: /account/instellingen?utm_source=forum_saved");
	die();
}

if(isset($_POST['dof_submit']) && isset($user))
{
	$day = DB::Escape($_POST['dof_day']);
	$month = DB::Escape($_POST['dof_month']);
	$year = DB::Escape($_POST['dof_year']);
	$dof = $day . "-" . $month . "-" . $year;
	DB::Query("UPDATE `users_settings` SET `birthdate` = '" . $dof . "' WHERE `user_id` = '" . $user->Id . "'");
	header("location: /account/instellingen?utm_source=dof_saved");
	die();
}

if(isset($_POST['da_submit']) && isset($user))
{
	$name = DB::Escape($_POST['displayname']);
	if(strlen($name) > 20)
	{
		$error = 'Je weergave naam mag maximaal 20 karakters lang zijn!';
	} else {
		DB::Query("UPDATE `users_settings` SET `displayname` = '" . $name . "' WHERE `user_id` = '" . $user->Id . "'");
		header("location: /account/instellingen?utm_source=da_saved");
		die();
	}
}

if(isset($_FILES['photo_upload']) && isset($_POST['submit']) && isset($user)) {
	$maxSize = (102400 * 5); //500KB
	$file = $_FILES['photo_upload'];
	
	if($file['size'] < $maxSize)
	{
		if(!empty($file['tmp_name']))
		{
			$ext = strtolower(strrchr($file['name'], "."));
			if($ext != ".png" && $ext != ".gif" && $ext != ".jpeg" && $ext != ".jpg")
			{
				$error = 'Uploaden is niet gelukt, alleen .png .gif .jpeg .jpg is toegestaan!';
			} else {
				$name = time() . "_" . strtolower($user->Username . $ext);
				$folder = "/home/basmidn80/domains/static-gly.be/public_html/grolsch/static-content-a/user-profilepictures/";
				if(move_uploaded_file($file['tmp_name'], $folder .$name))
				{
					$thumb = new Thumb();
					$thumb->openImage($folder .$name);
					$thumb->savePath($folder . "thumb_" .$name);
					$thumb->setSize(320);
					$thumb->save();
					$thumb->savePath($folder . "thumb_149_" .$name);
					$thumb->setSize(149);
					$thumb->save();
					$thumb->savePath($folder . "thumb_128_" .$name);
					$thumb->setSize(128);
					$thumb->save();
					$thumb->savePath($folder . "thumb_48_" .$name);
					$thumb->setSize(48);
					$thumb->save();
					$thumb->savePath($folder . "thumb_36_" .$name);
					$thumb->setSize(36);
					$thumb->save();
					$thumb->savePath($folder . "thumb_24_" .$name);
					$thumb->setSize(24);
					$thumb->save();
					DB::Query("UPDATE `users_profilepictures` SET `current` = 'false' WHERE `user_id` = '" . $user->Id . "'");
					DB::Query("INSERT INTO `users_profilepictures` (user_id, save_file, thumb_file, current, uploaded) VALUES ('" . $user->Id . "', '" . $name . "', 'thumb_" . $name . "', 'true', '" . time() . "')");
					DB::Query("UPDATE `users` SET `avatar` = 'thumb_" . $name . "' WHERE `id` = '" . $user->Id . "'");
					header("location: /account/instellingen?utm_source=pf_saved");
					die();
				} else {
					$error = 'Daar ging iets niet helemaal goed.. De beheerders zijn op de hoogte gesteld over deze fout.';
				}
			}
		} else {
			$error = 'Selecteer wel een bestand van je computer, anders valt er niet veel te uploaden..';
		}
	} else {
		$error = 'Je foto is te groot, maximaal 500Kb toegestaan.';
	}
}

if(isset($_GET['act']) && isset($user))
{
	switch($_GET['act'])
	{
		case "set_pf":
			{
				$pid = Glybe::Security($_GET['pid']);
				DB::Query("UPDATE `users_profilepictures` SET `current` = 'false' WHERE `user_id` = '" . $user->Id . "'");
				$pfQuery = DB::Query("SELECT * FROM `users_profilepictures` WHERE `id` = '" . $pid . "'");
				while($pfFetch = DB::Fetch($pfQuery))
				{
					DB::Query("UPDATE `users` SET `avatar` = '" . $pfFetch['thumb_file'] . "' WHERE `id` = '" . $user->Id . "'");
					DB::Query("UPDATE `users_profilepictures` SET `current` = 'true' WHERE `id` = '" . $pfFetch['id'] . "'");
					header("location: /account/instellingen?utm_source=pf_saved");
					die();
				}
			}
			break;
	}
}

$page = Array('title' => 'Instellingen', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<?php echo((isset($error)) ? '<div class="error_notif error">' . $error . '</div>' : ''); ?> 
		<?php echo((isset($_GET['utm_source']) && $_GET['utm_source'] == 'pf_saved') ? '<div class="error_notif success">Je profiel-foto is opgeslagen!</div>' : ''); ?> 
		<?php echo((isset($_GET['utm_source']) && $_GET['utm_source'] == 'forum_saved') ? '<div class="error_notif success">Je onderschrift is opgeslagen!</div>' : ''); ?> 
		<?php echo((isset($_GET['utm_source']) && $_GET['utm_source'] == 'da_saved') ? '<div class="error_notif success">Je weergave naam is opgeslagen!</div>' : ''); ?> 
		<?php echo((isset($_GET['utm_source']) && $_GET['utm_source'] == 'rpb_saved') ? '<div class="error_notif success">Je Priv&eacute;bericht instellingen zijn opgeslagen!</div>' : ''); ?> 
		<?php echo((isset($_GET['utm_source']) && $_GET['utm_source'] == 'dof_saved') ? '<div class="error_notif success">Je geboortedatum is opgeslagen!</div>' : ''); ?> 
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon cup_edit"></div>Algemene Instellingen</div>
				<div class="inner">
					<form action="" method="post">
						<strong>Wanneer wil ik een PB ontvangen</strong><br/>
						<input type="checkbox" name="rpb_on_gb" value="1" <?php echo(($user->GetSetting('send_pb_on_gb_post') == 'true') ? 'CHECKED' : ''); ?> /> Als iemand iets post in mijn gastenboek.<br/>
						<br/>
						<strong>Privacy</strong><br/>
						<input type="checkbox" name="rpb_pbl_upd" value="1" <?php echo(($user->GetSetting('public_updates') == 'true') ? 'CHECKED' : ''); ?> /> Mijn status-updates mag iedereen zien.<br/>
						<br />
						<strong>Geluiden</strong><br />
						<input type="checkbox" name="rpb_snd_ntf" value="1" <?php echo(($user->GetSetting('sound_notif') == 'true') ? 'checked="checked"' : ''); ?> />Notificatie geluiden laten horen<br />
						<input type="submit" name="rpb_submit" value="Opslaan" />
					</form>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon tux"></div>Weergave naam</div>
				<div class="inner">
					<form action="" method="post">
						<i>Je kan jou naam op het Forum veranderen in iets wat jij zelf wilt, maak het niet te lang.. er zijn maar 20 karakters toegestaan ;p</i><br/>
						<input name="displayname" style="width: 400px;" value="<?php echo htmlspecialchars($user->GetSetting("displayname")); ?>" />
						<input type="submit" name="da_submit" value="Opslaan" />
					</form>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon book"></div>Onderschrift</div>
				<div class="inner">
					<form action="" method="post">
						<strong>Forum Onderschrift</strong><br/>
						<textarea name="forum_onderschrift" style="height: 100px; width: 400px;"><?php echo htmlspecialchars($user->GetSetting("onderschrift")); ?></textarea>
						<input type="submit" name="forum_submit" value="Opslaan" />
					</form>
				</div>
			</div>
		</div>
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon user_edit"></div>Profiel-foto</div>
				<div class="inner">
					<i>Ben je al helemaal panisch van je profiel-foto en wil je een andere? Nou dat kan hieronder, je kunt heel simpel gewoon een foto van je computer afhalen en dat word dan jou nieuwe profiel-foto!</i>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<div style="position: relative; float: left; margin: 5px;"><?php echo $user->GetAvatar(128); ?></div>
					<?php
					$pfQuery = DB::Query("SELECT * FROM `users_profilepictures` WHERE `current` = 'false' AND `user_id` = '" . $user->Id . "' ORDER BY `id` DESC LIMIT 4");
					while($pfFetch = DB::Fetch($pfQuery)){
					?>
						<a href="/account/instellingen?act=set_pf&pid=<?php echo $pfFetch['id']; ?>"><div style="position: relative; float: left; margin: 27px 5px;"><div onmouseover="this.style.borderColor = '#3388D5';" onmouseout="this.style.borderColor = '#D7D7D7';" style="border: 2px solid #D7D7D7; height: 56px; width: 56px; position: relative; margin: 0px auto; background: url(http://grolsch.static-gly.be/static-content-a/user-profilepictures/<?php echo $pfFetch['thumb_file']; ?>) no-repeat center center; background-size: 100%;"></div></div></a>
					<?php } ?>
					<div style="clear: both;"></div>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<form action="" method="post" enctype="multipart/form-data">
						<strong>Nieuwe profielfoto uploaden</strong><br/>
						<i>Kies een bestand van max. 500Kb</i><br/>
						<input type="file" name="photo_upload" style="width: 100%;" />
						<input type="submit" name="submit" value="Uploaden" />
					</form>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon cake"></div>Geboortedatum</div>
				<div class="inner">
					<form action="" method="post">
						<?php
						$udof = explode("-", $user->GetSetting("birthdate"));
						?>
						<i>Vul hieronder je echte geboortedatum in, je kan het maar &eacute;&eacute;n keer veranderen, anders moet je een ticket aanmaken.</i><br/>
						<select name="dof_day" <?php echo(($user->GetSetting("birthdate") != "") ? 'disabled' : ''); ?>>
							<?php
							for($i = 1; $i <= 31; $i++)
							{
								if(strlen($i) == 1) $i = "0" . $i;
								echo'	<option value="' . $i . '" ' . (($i == $udof[0]) ? 'selected' : '') . '>' . $i . '</option>';
							}
							?>
						</select>
						<select name="dof_month" <?php echo(($user->GetSetting("birthdate") != "") ? 'disabled' : ''); ?>>
							<?php
							$maanden = Array('Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December');
							for($i = 1; $i <= 12; $i++)
							{
								$y = $i;
								if(strlen($i) == 1) $i = "0" . $i;
								echo'	<option value="' . $i . '" ' . (($i == $udof[1]) ? 'selected' : '') . '>' . $maanden[($y - 1)] . '</option>';
							}
							?>
						</select>
						<select name="dof_year" <?php echo(($user->GetSetting("birthdate") != "") ? 'disabled' : ''); ?>>
							<?php
							for($i = (date("Y") - 6); $i >= 1900; $i--)
							{
								echo'	<option value="' . $i . '" ' . (($i == $udof[2]) ? 'selected' : '') . '>' . $i . '</option>';
							}
							?>
						</select>
						<br/>
						<input type="submit" name="dof_submit" value="Opslaan" <?php echo(($user->GetSetting("birthdate") != "") ? 'disabled' : ''); ?> />
					</form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Account' => '/profiel/' . strtolower($user->Username), 'Instellingen' => '/account/instellingen');
include'content/footer.php';
?>