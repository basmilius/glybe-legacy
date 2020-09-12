<?php
include'includes/inc.bootstrap.php';

if(isset($_POST['pm_send']) && isset($_POST['pm_subject']) && isset($_POST['pm_text']))
{
	$error = Array();
	if(isset($_POST['pm_receivers']) && count($_POST['pm_receivers']) > 0)
	{
		$receivers = $_POST['pm_receivers'];
		$subject = DB::Escape($_POST['pm_subject']);
		$text = DB::Escape($_POST['pm_text']);
		
		if(!empty($receivers) && !empty($subject) && !empty($text))
		{
			if(strlen($subject) <= 100 && strlen($subject) >= 3)
			{
				if(strlen($text) >= 3 && strlen($text) <= 5000000)
				{
					$receiversList = Array();
					foreach($receivers as $receiver)
					{
						$uQuery = DB::Query("SELECT id FROM users WHERE username = '" . DB::Escape($receiver) . "'");
						if(DB::NumRows($uQuery) > 0)
						{
							$uFetch = DB::Fetch($uQuery);
							$receiversList[] = $uFetch['id'];
						} else {
							$errors[] = 'De gebruiker "' . $receiver . '" bestaat niet! Er is geen bericht naar die persoon gestuurd.';
						}
					}
					foreach($receiversList as $uId)
					{
						DB::Query("INSERT INTO `messages` (user_from_id, user_to_id, sended_on, readed_on, subject, message) VALUES ('" . $user->Id . "', '" . $uId . "', UNIX_TIMESTAMP(), '0', '" . $subject . "', '" . $text . "')");
						DB::Query("INSERT INTO `notifications` (`user_id`, `user_from_id`, `icon`, `title`, `message`, `url`, `n_ts`) VALUES ('" . $uId . "', '" . $user->Id . "', 'email_go', 'Priv&eacute;bericht', '<strong>" . htmlspecialchars(((str_replace(" ", "", $user->GetSetting("displayname")) != "") ? $user->GetSetting("displayname") : $user->Username)) . "</strong> heeft je een bericht gestuurd!', '/berichten/bericht?id=" . DB::InsertId() . "', UNIX_TIMESTAMP())");
					}
					if(count($error) === 0) { header("location: /berichten/index?utm_source=pm_sended"); }
				} else {
					$error[] = 'Het bericht moet minimaal 3 en maximaal 5.000.000 karakters lang zijn.';
				}
			} else {
				$error[] = 'Het onderwerp moet minimaal 3 en maximaal 100 karakters lang zijn.';
			}
		} else {
			$error[] = 'Vul wel alles in, anders kunnen we het bericht niet versturen :)';
		}
	} else {
		$error[] = 'Selecteer tenminste 1 ontvanger.';
	}
	if(count($error) == 0) unset($error);
}

if(isset($_GET['in_reply_to']) && is_numeric($_GET['in_reply_to']))
{
	$rQuery = DB::Query("SELECT m.*, u.username sender FROM messages m, users u WHERE (m.user_from_id = '" . $user->Id . "' OR m.user_to_id = '" . $user->Id . "') AND m.id = '" . DB::Escape($_GET['in_reply_to']) . "' AND u.id = m.user_from_id");
	if(DB::NumRows($rQuery) > 0)
	{
		$rFetch = DB::Fetch($rQuery);
	}
}

$page = Array('title' => 'Bericht maken', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container small">
			<div class="c_box">
				<div class="heading"><div class="icon house"></div>Mappen</div>
				<div class="inner">
					<a href="/berichten/index"><div class="nav_link"><div class="icon email"></div>Postvak IN <?php $c = DB::NumRowsQuery("SELECT 1 FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `folder_id` = '0' AND `state` = 'open' AND `readed` = 'false'"); echo(($c > 0) ? '<strong>(' . $c . ')</strong>' : ''); ?></div></a>
					<a href="/berichten/verzonden"><div class="nav_link"><div class="icon email_go"></div>Verzonden</div></a>
					<a href="/berichten/verwijderd"><div class="nav_link"><div class="icon email_delete"></div>Verwijderd <?php $c = DB::NumRowsQuery("SELECT 1 FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `state` = 'deleted' AND `readed` = 'false'"); echo(($c > 0) ? '<strong>(' . $c . ')</strong>' : ''); ?></div></a>
					<a href="/berichten/maak"><div class="nav_link"><div class="icon email_edit"></div>Nieuw bericht</div></a>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<?php
					$fQuery = DB::Query("SELECT * FROM `messages_folders` WHERE `user_id` = '" . $user->Id . "'");
					if(DB::NumRows($fQuery) === 0)
					{
						echo '<center><i>Je hebt nog geen mappen!</i></center>';
					} else {
						while($fFetch = DB::Fetch($fQuery))
						{
							$c = DB::NumRowsQuery("SELECT 1 FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `folder_id` = '" . $fFetch['id'] . "' AND `state` = 'open' AND `readed` = 'false'");
							echo '<a href="/berichten/index?fid=' . $fFetch['id'] . '"><div class="nav_link"><div class="icon ' . $fFetch['icon'] . '"></div>' . htmlspecialchars($fFetch['caption']) . (($c > 0) ? ' <strong>(' . $c . ')</strong>' : '') . '&nbsp;</div></a>';
						}
					}
					?>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<a href="javascript:void(0);" onclick="Glybe.Berichten.CreateFolder();"><div class="nav_link"><div class="icon folder_add"></div>Nieuwe map maken</div></a>
				</div>
			</div>
		</div>
		<div class="container big">
			<div class="c_box">
				<div class="heading"><div class="icon email_edit"></div>Bericht maken</div>
				<div class="inner">
					<form action="" method="post">
						<?php $errors="";if(isset($error)){foreach($error as $err){$errors.="<br/>".$err;}} echo((isset($error)) ? '<div class="error_notif error">Whoops, er zijn wat foutjes!' . $errors . '</div>' : ''); ?>
						<strong>Aan wie wil je dit bericht versturen?</strong><br/>
						<i>Je kan meerdere personen selecteren..</i><br/>
						<?php echo Glybe::SuggestionBox("pm_receivers", "[MyUser.Friends,MyUser.Users]", ((isset($_GET['users_to'])) ? explode(",", $_GET['users_to']) : ((isset($rFetch)) ? Array($rFetch['sender']) : Array()))); ?>
						<br/>
						<strong>Onderwerp</strong><br/>
						<i>Geef je bericht een onderwerp, van maximaal 100 karakters.</i><br/>
						<input type="text" name="pm_subject" maxlength="100" style="width: 625px;" value="<?php echo((isset($rFetch)) ? ((substr($rFetch['subject'], 0, 4) != 'Re: ') ? 'Re: ' : '') . $rFetch['subject'] : ''); ?>" /><br/>
						<br/>
						<strong>Bericht</strong><br/>
						<i>Hier kan je je bericht typen aan de leden die je geselecteerd hebt.</i><br/>
						<textarea name="pm_text" style="width: 625px; height: 180px; margin: 0px -1px;"><?php echo((isset($rFetch)) ? '[quote="' . $rFetch['sender'] . '"]' . htmlspecialchars($rFetch['message']) . "[/quote]\r\n" : ''); ?></textarea><br/>
						<br/>
						<strong>Waarschuwing</strong><br/>
						<span>Voor spam berichten kun je waarschuwing's procenten krijgen, de ontvanger kan het bericht aangeven aan een team-lid op Glybe. Verstuur geen spam-berichten, dat wil je zelf ook niet in je inbox.</span><br/>
						<br/>
						<input type="submit" value="Versturen" name="pm_send" />
					</form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Berichten' => '/berichten/index');
include'content/footer.php';
?>