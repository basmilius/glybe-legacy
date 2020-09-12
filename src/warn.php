<?php
include'includes/inc.bootstrap.php';
if(!$user->HasPermissions('warn_user'))
{
	header('Location: /home');
}
else
{
	$page = Array('title' => 'Waarschuw gebruiker', 'access' => Array(true, false));
	include'content/header.php';
	include'content/heading.php';
	?>
		<div class="content">
			<div class="container large">
				<div class="c_box">
					<div class="heading"><div class="icon flag_red"></div>Gebruiker waarschuwen</div>
					<div class="inner">
						<?php
						$user2 = new User($_GET['id'], false);
						$time = time();
						if($_SERVER['REQUEST_METHOD'] == 'POST')
						{
							if($_POST['ban'] != 0)
							{
								if($_POST['ipban'] == 1)
								{
									$ipban = 1;
								}
								else
								{
									$ipban = 0;
								}
								DB::Query("INSERT INTO users_bans (ip_ban, user_id, user_ip, ban_reason, ban_start, ban_expire, added_by) VALUES ('".$ipban."', '" . DB::Escape($_GET['id']) . "', '" . $user2->Ip . "', '" . DB::Escape($_POST['reason']) . "', '" . $time . "', '" . $_POST['ban'] . "', '" . $user->Id . "')");
								DB::Query("INSERT INTO users_bans (ip_ban, user_id, user_ip, ban_reason, ban_start, ban_expire, added_by) VALUES ('".$ipban."', '" . DB::Escape($_GET['id']) . "', '" . $user2->LastIP . "', '" . DB::Escape($_POST['reason']) . "', '" . $time . "', '" . $_POST['ban'] . "', '" . $user->Id . "')");
								DB::Query("UPDATE users SET permission_id = 0 WHERE id = '".DB::Escape($_GET['id'])."'");
								$ban = 1;
							}
							else
							{
								$ban = 0;
							}
							DB::Query("INSERT INTO users_warn (ban_tot, user_id, user_from, percent, reason, date, ban, ipban) VALUES ('" . DB::Escape($_POST['ban']) . "', '" . DB::Escape($_GET['id']) . "', '" . $user->Id . "', '" . DB::Escape($_POST['percent']) . "', '" . DB::Escape($_POST['reason']) . "', '" . $time . "', '" . $ban . "', '" . DB::Escape($_POST['ipban']) . "')");							
							DB::Query("UPDATE users SET warn = warn+" . $_POST['percent'] . " WHERE id = '" . DB::Escape($_GET['id']) . "'");
							echo '<div class="error_notif success">De gebruiker heeft er nu ' . $_POST['percent'] . '% bij!</div>';
							echo '<div class="error_notif warning">' . $user2->Username . ' heeft momenteel ' . ($user2->Warn+$_POST['percent']) . '%</div><br /><br />';
						}
						else
						{
							echo '<div class="error_notif warning"> ' . $user2->Username . ' heeft momenteel ' . $user2->Warn . '%</div><br /><br />';
						}
						?>
						<form action="" method="POST">
							<table width="100%">
								<tr>
									<td>
										Aantal procent
									</td>
									<td>
										<select name="percent">
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="15">15</option>
											<option value="20">20</option>
											<option value="25">25</option>
											<option value="30">30</option>
											<option value="35">35</option>
											<option value="40">40</option>
											<option value="45">45</option>
											<option value="50">50</option>
											<option value="55">55</option>
											<option value="60">60</option>
											<option value="65">65</option>
											<option value="70">70</option>
											<option value="75">75</option>
											<option value="80">80</option>
											<option value="85">85</option>
											<option value="90">90</option>
											<option value="95">95</option>
											<option value="100">100</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>
										Reden
									</td>
									<td>
										<textarea name="reason" style="height: 150px; width: 90%;"></textarea>
									</td>
								</tr>
								<tr>
									<td>
										Ban
									</td>
									<td>								
										<select name="ban">
											<option value="0">Geen ban</option>
											<option value="<?= ($time + (60*60*24)); ?>">24 uur</option>
											<option value="<?= ($time + (60*60*48)); ?>">48 uur</option>
											<option value="<?= ($time + (60*60*72)); ?>">72 uur</option>
											<option value="<?= ($time + (60*60*168)); ?>">1 week</option>
											<option value="<?= ($time + (60*60*336)); ?>">2 weken</option>
											<option value="<?= strtotime("+1 MONTH"); ?>">1 maand</option>
											<option value="<?= strtotime("+2 MONTH"); ?>">2 maanden</option>
											<option value="<?= strtotime("+3 MONTH"); ?>">3 maanden</option>
											<option value="<?= strtotime("+6 MONTH"); ?>">6 maanden</option>
											<option value="<?= strtotime("+1 YEAR"); ?>">1 jaar</option>
											<option value="<?= strtotime("+2 YEAR"); ?>">2 jaar</option>
											<option value="<?= strtotime("+5 YEAR"); ?>">5 jaar</option>
											<option value="<?= strtotime("+10 YEAR"); ?>">10 jaar</option>
											<option value="<?= strtotime("+100 YEAR"); ?>">Tot de dood</option>
										</select>
									</tr>
								</tr>
								<tr>
									<td>
										IP ban?
									</td>
									<td>
										<label for="no">
											Nee
										</label>
										<input type="radio" id="no" name="ipban" checked value="0" />
										<label for="yes">
											Ja
										</label>
										<input type="radio" id="yes" name="ipban" value="1" />
									</td>
								</tr>							
								<tr>
									<td>
										&nbsp;
									</td>
									<td>
										<input type="submit" name="submit" value="Waarschuw gebruiker" />
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>
			<div class="container large">
				<div class="c_box">
					<div class="heading"><div class="icon flag_blue"></div>Warn geschiedenis</div>
					<div class="inner">
					<?php
						$query = DB::Query("SELECT * FROM users_warn WHERE user_id = '" . ($_GET['id']) . "' ORDER BY id DESC");
						if(DB::NumRows($query) > 0)
						{
							while($fetch = DB::Fetch($query))
							{
								$user3 = new User($fetch['user_id'], false);
								$user4 = new User($fetch['user_from'], false);
								echo '' . htmlspecialchars($user4->Username) . ' gaf ' . htmlspecialchars($user3->Username) . ' op ' . strftime('%e %B %Y om %H:%M:%S uur', $fetch['date']) . ' ' . $fetch['percent'] . '% met de reden:<br /><br /><i><strong>' . htmlspecialchars($fetch['reason']) . '</strong></i><br />';						
								if(!empty($fetch['ban_tot']))
								{
									echo '<em>Lid is verbannen tot ' . strftime('%e %B %Y om %H:%M:%S uur', $fetch['ban_tot']) . '!</em><br />';
								}								
								echo '<hr /><br />';
							}
						}
						else
						{
						?>
							<em>Deze gebruiker heeft nog geen waarschuwingen gekregen!</em>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	<?php
	$footerLinks = Array('Waarschuw gebruiker' => '/warn');
	include'content/footer.php';
}
?>