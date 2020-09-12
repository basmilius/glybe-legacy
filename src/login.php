<?php
include'includes/inc.bootstrap.php';

if(isset($_POST['username']) && isset($_POST['password']))
{
	$username = Glybe::Security($_POST['username']);
	$password = Glybe::Security($_POST['password']);
	$password_hash = Glybe::HashForPassword($password);
	
	$userQuery = DB::Query("SELECT `id`,`username`,`password` FROM `users` WHERE `username` = '" . $username . "'");
	if(DB::NumRows($userQuery) > 0)
	{
		$userFetch = DB::Fetch($userQuery);
		if($password_hash == $userFetch['password'])
		{
			// Oké, de gebruiker kan worden ingelogd, maar we voeren nog 1 check uit..
			// En dat is kijken of de gebruiker verbannen is.
			$banQuery = DB::Query("SELECT * FROM `users_bans` WHERE `user_id` = '" . $userFetch['id'] . "' AND `ban_expire` > UNIX_TIMESTAMP()");
			$banQuery2 = DB::Query("SELECT * FROM `users_bans` WHERE `user_ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND ip_ban = 1 AND `ban_expire` > UNIX_TIMESTAMP()");
			if(DB::NumRows($banQuery) > 0)
			{
				$banFetch = DB::Fetch($banQuery);
				$error = 'Je account is verbannen tot ' . strftime('%e %B %Y om %H:%M:%S uur', $banFetch['ban_expire']) . ' met de volgende reden<br/><i>' . $banFetch['ban_reason'] . '</i>';
			}
			elseif(DB::NumRows($banQuery2) > 0)
			{
				$banFetch2 = DB::Fetch($banQuery2);
				$error = 'Je IP is verbannen tot ' . strftime('%e %B %Y om %H:%M:%S uur', $banFetch2['ban_expire']) . ' met de volgende reden<br/><i>' . $banFetch2['ban_reason'] . '</i>';
			}
			else
			{
				$userClass = new User($userFetch['id']);
				$userClass->PrepareSession();
				header("location: /home?utm_source=login_success");
			}
		} else {
			$error = 'Onjuist wachtwoord!';
		}
	} else {
		$error = 'De gebruiker "' . $username . '" bestaat niet!';
	}
}

$page = Array('title' => 'Inloggen', 'access' => Array(false, true));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon information"></div>Waarom Glybe?</div>
				<div class="inner">
					<table>
						<tr>
							<td style="width: 58px; height: 58px;"><img src="/cache/images/icons/nuvola-48/chat.png" /></td>
							<td>Chatten en communiceren met andere gebruikers van Glybe is heel simpel via ons Forum en chat-systeem.<td/>
						</tr>
						<tr>
							<td style="width: 58px; height: 58px;"><img src="/cache/images/icons/nuvola-48/piano.png" /></td>
							<td>Deel en luister muziek die andere Glybers je sturen, via de categorie Muziek in ons Forum.<td/>
						</tr>
						<tr>
							<td style="width: 58px; height: 58px;"><img src="/cache/images/icons/nuvola-48/kopete.png" /></td>
							<td>Word vrienden met andere Glybers en speel allerlei spelletjes met elkaar, of je kan natuurlijk ook onze priv&eacute; chat gebruiken.<td/>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon user_go"></div>Inloggen</div>
				<div class="inner">
					<?php echo((isset($error)) ? '<div class="error_notif error">' . $error . '</div>' : ''); ?>
					<form action="/login" method="post">
						<strong>Gebruikersnaam</strong><br/>
						<input type="text" name="username" style="width: 400px;" />
						<strong>Wachtwoord</strong><br/>
						<input type="password" name="password" style="width: 400px;" />
						<input type="submit" value="Inloggen" />
					</form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
include'content/footer.php';
?>