<?php
include'includes/inc.bootstrap.php';

if(!checkIfIsNlOrBe($_SERVER['REMOTE_ADDR']))
{
	header("location: /error_607.html");
	die();
}

if(isset($_POST['su_username']))
{
	$username = DB::Escape($_POST['su_username']);
	$password_a = DB::Escape($_POST['su_pwd_a']);
	$password_b = DB::Escape($_POST['su_pwd_b']);
	$email = DB::Escape($_POST['su_mail']);
	$captcha = strtoupper(DB::Escape($_POST['su_check']));
	$av = (isset($_POST['su_av']) && $_POST['su_av'] == '1');
	
	if($av)
	{
		if(ctype_alnum($username) && (strlen(str_replace(" ", "", $username)) >= 3) && (strlen(str_replace(" ", "", $username)) < 16))
		{
			if(strlen($password_a) > 3 && strlen($password_b) < 50)
			{
				if($password_a == $password_b)
				{
					if(stristr($email, "@") && stristr($email, "."))
					{
						if($captcha == $_SESSION['captcha'])
						{
							if(DB::NumRowsQuery("SELECT 1 FROM users WHERE username = '" . $username . "'") === 0)
							{
								$time = time();
								DB::Query("INSERT INTO users (username,password,mail,ip, reg_date) VALUES ('" . $username . "', '" . Glybe::HashForPassword($password_a) . "', '" . $email . "', '" . $_SERVER['REMOTE_ADDR'] . "', '".$time."')");
								$usr = new User(DB::InsertId());
								$usr->PrepareSession();
								header("location: /home?utm_source=login_success");
							} else {
								$error = 'Sorry, maar deze gebruikersnaam is al bezet!';
							}
						} else {
							$error = 'De controle-code is niet goed ingevuld.';
						}
					} else {
						$error = 'Dit is een ongeldig email-adres';
					}
				} else {
					$error = 'De wachtwoorden zijn niet het zelfde!';
				}
			} else {
				$error = 'Het wachtwoord moet minimaal 3 en maximaal 50 karakters hebben!';
			}
		} else {
			$error = 'Dit is een ongeldige gebruikersnaam, het mag alleen letters en cijfers hebben en moet minimaal 3 en maximaal 15 karakters lang zijn.';
		}
	} else {
		$error = 'Als je je wilt aanmelden moet je akkoord gaan met onze Algemene Voorwaarden!';
	}
}

$page = Array('title' => 'Aanmelden', 'access' => Array(false, true));
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
				<div class="heading"><div class="icon user_add"></div>Aanmelden</div>
				<div class="inner">
					<?php echo((isset($error)) ? '<div class="error_notif error">' . $error . '</div>' : ''); ?>
					<form action="" method="post">
						<strong>Gebruikersnaam</strong><br/>
						<i>Dit word jou naam op Glybe</i><br/>
						<input type="text" name="su_username" placeholder="Hoe wil je heten?" style="width: 400px;" /><br/>
						<br/>
						<strong>Wachtwoord</strong><br/>
						<i>Deze mag je nooit aan iemand geven, hou hem voor jezelf!</i><br/>
						<input type="password" name="su_pwd_a" placeholder="Geheim woord" style="float: left; width: 185px;" />
						<input type="password" name="su_pwd_b" placeholder="Herhaal" style="float: right; width: 185px;" /><br/>
						<div class="clear"></div>
						<br/>
						<strong>Mail</strong><br/>
						<i>Vul een geldig mail-adres in, we sturen zo dadelijk een activatie-mail</i><br/>
						<input type="text" name="su_mail" placeholder="Vul je email-adres in" style="width: 400px;" /><br/>
						<br/>
						<strong>Controle</strong><br/>
						<img src="/content/captcha.php?r=<?php echo time(); ?>" align="left" style="border: 1px solid #C6C6C6; margin: 2px 0px; margin-right: 8px;" /><input type="text" name="su_check" placeholder="Vul hier het antwoord in" style="width: 290px;" /><br/>
						<br/>
						<input type="checkbox" name="su_av" value="1" /> Ik ga akkoord met de <a href="/glybe/av">Algemene Voorwaarden</a><br/>
						<br/>
						<input type="submit" name="su_submit" value="Aanmelden" /><br/>
					</form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
include'content/footer.php';
?>