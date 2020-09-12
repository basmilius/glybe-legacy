<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Online Glybers', 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container big">
			<div class="c_box">
				<div class="heading"><div class="icon group"></div>Online Glybers</div>
				<div class="inner">
					<table class="top_list" cellspacing="1" border="0" style="width: 100%;">
						<tr>
							<td style="font-weight: bold; border-bottom: 1px solid #B6B6B6; width: 32px;"></td>
							<td style="font-weight: bold; border-bottom: 1px solid #B6B6B6; width: 170px;">Gebruiker</td>
							<td style="font-weight: bold; border-bottom: 1px solid #B6B6B6; width: 240px;">Laatst gezien</td>
							<td style="font-weight: bold; border-bottom: 1px solid #B6B6B6; width: 140px;">Profiel</td>
						</tr>
						<?php
						foreach(Glybe::GetOnlineUsersAsArray(true, true) as $key => $oUser)
						{
							$rUser = new User((($oUser['id'] > 0) ? $oUser['id'] : 3), false, false, true);
							$mob = Glybe::CheckMobile($oUser['oUserAgent']);
							echo'	<tr>
									<td>' . $rUser->GetAvatar(32) . '</td>
									<td>
										<strong>' . (($oUser['id'] != 0) ? '<a href="/profiel/' . strtolower($rUser->Username) . '" class="gui-tooltip" tt-data="' . htmlspecialchars($rUser->Username) . '">' . htmlspecialchars(((str_replace(" ", "", $rUser->GetSetting("displayname")) != "") ? $rUser->GetSetting("displayname") : $rUser->Username)) . '</a>' : Glybe::GetComputedName($oUser['oUserAgent'])) . '</strong>
										' . (($mob != false) ? '<br/><span style="font-style: italic; font-size: 9px;">Ingelogd op mijn ' . $mob[0] . '</span>' : '') . '
									</td>
									<td>' . Glybe::TimeAgo($oUser['uLastActive']) . '</td>
									<td>' . (($oUser['id'] > 0) ? '<a href="/profiel/' . strtolower($rUser->Username) . '">Ga naar profiel &raquo;</a>' : '') . '</td>
								</tr>';
						}
						?>
					</table>
				</div>
			</div>
		</div>
		<div class="container small">
			<div class="c_box">
				<div class="heading"><div class="icon user_comment"></div>Statistieken</div>
				<div class="inner">
					Er zijn in totaal <strong><?php echo $glb['users']['count']; ?></strong> leden waarvan er nu <strong><?php echo count(Glybe::GetOnlineUsersAsArray()); ?></strong> online zijn.<br /><br />
					Het grootste aantal leden online was <strong><?php echo $glb['users']['most_online']; ?></strong> op <?php echo $glb['users']['most_online_date']; ?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Glybe' => '/glybe/over', 'Online Leden' => '/glybe/online');
include'content/footer.php';
?>