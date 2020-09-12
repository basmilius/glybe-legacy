<?php
include'includes/inc.bootstrap.php';

$p = ((isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] > 0) ? $_GET['p'] : 1);

$bQuery = DB::Query("SELECT 1 FROM messages pm WHERE pm.user_from_id = '" . $user->Id . "' AND pm.state = 'open'");
$bCount = DB::NumRows($bQuery);

$ps = Glybe::PaginaSysteem($bCount, $p, 10);

$page = Array('title' => 'Berichten', 'access' => Array(true, false));
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
				<div class="heading"><div class="icon comments"></div>Berichten</div>
				<div class="inner">
					<form action="" method="post">
						<div class="psystem" style="float: left;">
							<?php
							foreach($ps['paginas'] as $key => $value)
							{
								echo'<a href="/berichten/verzonden?p=' . $value[1] . '"><div class="item">' . $value[0] . '</div></a>';
							}
							?>
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
						<div class="error_notif error" id="pm_error_notif" style="display: none;"></div>
						<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
						<?php
						$messagesQuery = DB::Query("	SELECT
													pm.*,
													ua.username sender,
													ub.username receiver,
													usa.displayname aDisplayName,
													usb.displayname bDisplayName
												FROM
													messages pm,
													users ua,
													users ub,
													users_settings usa,
													users_settings usb
												WHERE
													ua.id = pm.user_from_id
												AND
													ub.id = pm.user_to_id
												AND
													usa.user_id = ua.id
												AND
													usb.user_id = ub.id
												AND
													pm.user_from_id = '" . $user->Id . "'
												AND
													pm.state = 'open'
												ORDER BY
													id DESC
												LIMIT
													" . $ps['limit'] . ", 10");
						if(DB::NumRows($messagesQuery) === 0)
						{
							echo'<div class="error_notif information">Geen berichten gevonden in deze map.</div>';
						} else {
							echo'	<table style="width: 100%;" border="0" cellspacing="0">
									<tr style="background: #E9E9E9; font-weight: bold;">
									<td style="padding: 6px; width: 16px;"><div class="icon email"></div></td>
									<td style="padding: 5px;">Onderwerp</td>
									<td style="padding: 5px; text-align: center; width: 80px;">Door</td>
									<td style="padding: 5px; text-align: center; width: 80px;">Aan</td>
									<td style="padding: 5px; text-align: center; width: 160px;">Verzonden op</td>
								</tr>';
							while($messagesFetch = DB::Fetch($messagesQuery))
							{
								$notReaded = !($messagesFetch['readed'] == "true");
								echo'	<tr onmouseover="this.style.background = \'#EEEFFF\';" onmouseout="this.style.background = \'#FFFFFF\';">
										<td style="padding: 6px; width: 16px;"><div class="icon email"></div></td>
										<td style="padding: 5px;"><a href="/berichten/bericht?id=' . $messagesFetch['id'] . '">' . (($notReaded) ? '<strong>' : '') . (($messagesFetch['subject'] != "") ? htmlspecialchars($messagesFetch['subject']) : '(Geen onderwerp)') . (($notReaded) ? '</strong>' : '') . '</a></td>
										<td style="padding: 5px; text-align: center; width: 80px;">' . htmlspecialchars(((str_replace(" ", "", $messagesFetch['aDisplayName']) != "") ? $messagesFetch['aDisplayName'] : $messagesFetch['sender'])) . '</td>
										<td style="padding: 5px; text-align: center; width: 80px;">' . htmlspecialchars(((str_replace(" ", "", $messagesFetch['bDisplayName']) != "") ? $messagesFetch['bDisplayName'] : $messagesFetch['receiver'])) . '</td>
										<td style="padding: 5px; text-align: center; width: 160px;">' . Glybe::TimeAgo($messagesFetch['sended_on']) . '</td>
									</tr>';
							}
							echo'	</table>';
						}
						?>
						<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
						<div class="psystem" style="float: left;">
							<?php
							foreach($ps['paginas'] as $key => $value)
							{
								echo'<a href="/berichten/verzonden?p=' . $value[1] . '"><div class="item">' . $value[0] . '</div></a>';
							}
							?>
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
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