<?php
include'includes/inc.bootstrap.php';

if(isset($_POST['profile_story_submit']))
{
	$story = DB::Escape($_POST['profile_story_txt']);
	DB::Query("UPDATE `users_settings` SET `profile_story` = '" . $story . "' WHERE `user_id` = '" . $user->Id . "'");
	header("location: /profiel/" . strtolower($user->Username));
}

if(isset($_GET['m_delete']) && isset($user) && is_numeric($_GET['m_delete']))
{
	$id = DB::Escape($_GET['m_delete']);
	if(DB::NumRowsQuery("SELECT 1 FROM profile_music WHERE id = '" . $id . "'") > 0)
	{
		DB::Query("DELETE FROM profile_music WHERE id = '" . $id . "'");
		$success = 'Het liedje is succesvol verwijderd van je profiel!';
	} else {
		$error = 'Ongeldig verzoek.';
	}
}

if(isset($_POST['pu_omslag']) && isset($user))
{
	$maxSize = (102400 * 5); //500KB
	$file = $_FILES['photo_upload_omslag'];
	
	if($file['size'] <= $maxSize)
	{
		if(!empty($file['tmp_name']))
		{
			$ext = strtolower(strrchr($file['name'], "."));
			if($ext != ".png" && $ext != ".gif" && $ext != ".jpeg" && $ext != ".jpg")
			{
				$error = 'Uploaden is niet gelukt, alleen .png .gif .jpeg .jpg is toegestaan!';
			} else {
				$name = "coverpicture_" . strtolower($user->Username . $ext);
				$folder = "/home/basmidn80/domains/static-gly.be/public_html/grolsch/static-content-a/user-coverpictures/";
				list($width, $height) = getimagesize($file['tmp_name']);
				if($width == 910 && $height == 200)
				{
					if(move_uploaded_file($file['tmp_name'], $folder . $name))
					{
						DB::Query("UPDATE `users_settings` SET `profile_cover` = '" . $name . "?" . time() . "' WHERE `user_id` = '" . $user->Id . "'");
						header("Location: /profiel");
						die();
					} else {
						$error = 'Daar ging iets niet helemaal goed.. De beheerders zijn op de hoogte gesteld over deze fout.';
					}
				} else {
					$error = "Je omslagfoto heeft de verkeerde afmeting, deze moet zijn.. 910 pixels breed en 200 pixels hoog.";
				}
			}
		} else {
			$error = 'Selecteer wel een bestand van je computer, anders valt er niet veel te uploaden..';
		}
	} else {
		$error = "Je omslagfoto is te groot! Maximaal 500Kb toegestaan!";
	}
}

if(isset($_POST['newSongUrl']) && isset($user))
{
	$songUrl = DB::Escape($_POST['newSongUrl']);
	$songTitle = DB::Escape($_POST['newSongTitle']);
	
	if(strlen($songUrl) > 20)
	{
		$ytId = explode("v=", $songUrl);
		$ytId = substr($ytId[1], 0, 11);
		if(strlen($songTitle) >= 10 && strlen($songTitle) <= 100)
		{
			if(DB::NumRowsQuery("SELECT 1 FROM profile_music WHERE yt_id = '" . $ytId . "' AND user_id = '" . $user->Id . "'") === 0)
			{
				if(DB::NumRowsQuery("SELECT 1 FROM profile_music WHERE user_id = '" . $user->Id . "'") < 6)
				{
					DB::Query("INSERT INTO `profile_music` (user_id, yt_id, trackname) VALUES ('" . $user->Id . "', '" . $ytId . "', '" . $songTitle . "')");
					$success = '"' . htmlspecialchars(stripslashes($songTitle)) . '" is toegevoegd aan je profiel!';
				} else {
					$error = 'Je mag maar 5 liedjes aan je profiel toevoegen!';
				}
			} else {
				$error = 'Dit liedje heb je al toegevoegd aan je profiel!';
			}
		} else {
			$error = 'Sorry, maar de titel van het liedje moet minimaal 10 en maximaal 100 karakters lang zijn..';
		}
	} else {
		$error = 'Vul wel een geldige YouTube-Link in!';
	}
}

$page = Array('title' => 'Profiel Instellingen', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<?php echo((isset($error)) ? '<div class="error_notif error">' . $error . '</div>' : ''); ?> 
		<?php echo((isset($success)) ? '<div class="error_notif success">' . $success . '</div>' : ''); ?> 
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon color_wheel"></div>Profiel Style</div>
				<div class="inner">
					<strong>Omslagfoto</strong><br/>
					<img src="<?php echo(($user->GetSetting("profile_cover") != "") ? 'http://grolsch.static-gly.be/static-content-a/user-coverpictures/' . $user->GetSetting("profile_cover") : '/cache/style_default/images/omslagfoto_sample_1.png'); ?>" height="90" width="410" />
					<i>Deze <u>moet</u> de afmeting 910x200 pixels hebben!!</i><br/>
					<div>
						<form action="" method="post" enctype="multipart/form-data">
							<i>Nieuwe omslagfoto uploaden, Kies een bestand van max. 500Kb</i><br/>
							<input type="file" name="photo_upload_omslag" style="width: 100%;" />
							<input type="submit" name="pu_omslag" value="Uploaden" />
						</form>
					</div>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon cd_burn"></div>Jouw afspeel-lijst</div>
				<div class="inner">
					<center><i>Hiermee kun je de liedjes die op jouw profiel worden weergeven beheren, je kunt er maximaal 5 toevoegen :)</i></center>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<?php
					$mQuery = DB::Query("SELECT * FROM `profile_music` WHERE `user_id` = '" . $user->Id . "' ORDER BY `ordernum`");
					if(DB::NumRows($mQuery) === 0)
					{
						echo'<center><i>Je hebt nog geen muziek op je profiel gezet!</i></center>';
					} else {
						while($mFetch = DB::Fetch($mQuery))
						{
							echo'	<div class="music_item">
									<div class="music_thumb" style="background: url(http://i.ytimg.com/vi/' . $mFetch['yt_id'] . '/1.jpg) no-repeat center center;"></div>
									<div class="music_title">' . htmlspecialchars($mFetch['trackname']) . '</div>
									<a href="/account/profiel_instellingen?m_delete=' . $mFetch['id'] . '"><div class="icon cross" style="position: absolute; top: 10px; right: 10px;"></div></a>
									<div class="clear"></div>
								</div>';
						}
					}
					?>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<form action="/account/profiel_instellingen" method="post">
						<strong>Nieuw liedje toevoegen</strong><br/>
						<i>Plak hieronder een link van een YouTube-Filmpje</i><br/>
						<input type="text" name="newSongUrl" style="width: 400px;" onblur="if(this.value.length < 20) return; var m = document.getElementById('songTitle'); m.value = 'Laden..'; m.disabled = true; Glybe.Profile.Search(this.value, function(r) { m.value = r; m.disabled = false; });" /><br/>
						<br/>
						<strong>Geef het liedje een naam</strong><br/>
						<i>Je kan het liedje zelf een naam geven als je wilt!</i><br/>
						<input type="text" name="newSongTitle" id="songTitle" style="width: 400px;" />
						<input type="submit" value="Toevoegen!" />
					</form>
				</div>
			</div>
		</div>
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon house"></div>Jouw verhaal</div>
				<div class="inner">
					<form action="/account/profiel_instellingen" method="post">
						<center><i>Jouw verhaal is het echte persoonlijke gedeelte op je profiel, je kan er namelijk alles in kwijt.. Foto's, Video's, teksten en nog veel meer! Hieronder kan je jouw verhaal veranderen, als je dat wilt.</i></center><br/>
						<textarea name="profile_story_txt" style="width: 400px; height: 200px;"><?php echo htmlspecialchars($user->GetSetting("profile_story")); ?></textarea>
						<input type="submit" name="profile_story_submit" value="Opslaan" />
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