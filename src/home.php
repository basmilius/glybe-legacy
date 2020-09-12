<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Home', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<?php echo((isset($_GET['utm_source']) && $_GET['utm_source'] == "login_success") ? '<div class="error_notif success">Heuhj ' . ucfirst($user->Username) . '! Je bent nu ingelogd, veel plezier op Glybe!</div>' : ''); ?>
		<div class="container" style="width: 340px;">
			<div class="c_box">
				<div class="heading"><div class="icon house"></div>Welkom, <?php echo ucfirst($user->Username); ?>!</div>
				<div class="inner" style="float: left; width: 116px;">
					<?php echo $user->GetAvatar(100); ?>
					<center><small><a href="/account/instellingen">Profiel foto wijzigen</a></small></center>
				</div>
				<div class="inner" style="float: left; width: 190px;">
					<a style="text-decoration: none;" href="/profiel/<?php echo strtolower($user->Username); ?>"><div class="nav_link"><div class="icon user_green"></div>Ga naar jouw profiel</div></a>
					<a style="text-decoration: none;" href="/glybe/shop"><div class="nav_link"><div class="icon coins"></div><?php echo number_format($user->GetSetting("punten"), 2, ",", "."); ?> Punten</div></a>					
					<div style="border-top: 1px solid #C6C6C6; margin: 1px;"></div>
					<a style="text-decoration: none;" href="/berichten/index"><div class="nav_link"><div class="icon email"></div><strong><?php echo DB::NumRowsQuery("SELECT 1 FROM messages WHERE user_to_id = '" . $user->Id . "' AND state != 'deleted' AND readed = 'false'"); ?></strong> Nieuwe berichten</div></a>
					<a style="text-decoration: none;" href="/vrienden/requests"><div class="nav_link"><div class="icon user_add"></div><strong><?= count($user->FriendsReq); ?></strong> Nieuwe uitnodigingen</div></a>
					<a style="text-decoration: none;" href="/waarschuwingen"><div class="nav_link"><div class="icon exclamation"></div><strong><?= $user->Warn; ?>%</strong> waarschuwings perc.</div></a>
				</div>
				<div class="clear"></div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon heart"></div>Online Vrienden</div>
				<div class="inner">
					<?php
					$friends = Array();
					foreach(Glybe::GetOnlineUsersAsArray(false) as $usr)
					{
						if(!$user->IsFriend($usr['id'])) continue;
						$friends[] = new User($usr['id'], false, false, true);
					}
					if(count($friends) === 0)
					{
						echo'	<div class="error_notif warning">Er zijn geen vrienden online..</div>';
					} else {
						echo'	<table class="top_list" cellspacing="1" border="0" width="100%">';
						foreach($friends as $friend)
						{
							echo'	<tr>
									<td style="height: 24px; width: 24px;"><img src="' . str_replace("thumb_", "thumb_24_", $friend->Avatar) . '" width="24" height="24" /></td>
									<td>' . htmlspecialchars(((str_replace(" ", "", $friend->GetSetting("displayname")) != "") ? $friend->GetSetting("displayname") : $friend->Username)) . '</td>
									<td style="width: 44px;"><a href="/profiel/' . strtolower($friend->Username) . '">Profiel</a></td>
								</tr>';
						}
						echo'	</table>';
					}
					?>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon group_add"></div>Nieuwste Glybers</div>
				<div class="inner">
					<?php
					$sQuery = DB::Query("	SELECT
											u.username uName,
											us.displayname uDisplayName
										FROM
											users u,
											users_settings us
										WHERE
											us.user_id = u.id
										AND
											u.permission_id > 0
										ORDER BY
											u.id DESC
										LIMIT
											5");
					if(DB::NumRows($sQuery) === 0)
					{
						echo'	<center><i>Geen nieuwe leden ;o!</i></center>';
					} else {
						$p = 0;
						echo'	<table class="top_list" border="0" cellspacing="1" style="width: 100%;">';
						while($sFetch = DB::Fetch($sQuery))
						{
							$p++;
							echo'	<tr>
									<td><a href="/profiel/' . strtolower($sFetch['uName']) . '">' . htmlspecialchars(((str_replace(" ", "", $sFetch['uDisplayName']) != "") ? $sFetch['uDisplayName'] : $sFetch['uName'])) . '</a></td>
									<td style="width: 80px;"><a href="/profiel/' . strtolower($sFetch['uName']) . '">Naar Profiel</a></td>
								</tr>';
						}
						echo'	</table>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="container" style="width: 540px;">
			<div class="c_box">
				<div class="heading"><div class="icon user_comment"></div>Laatst gereageerd in..</div>
				<div class="inner">
					<table class="top_list" border="0" cellspacing="1" style="width: 100%;">
						<?php
						$ltQuery = DB::Query("	SELECT
												t.caption tName,
												t.id tId,
												t.state state,
												t.sticky sticky,
												p.id pId,
												p.post_timestamp postTimestamp,
												p.user_id
											FROM
												(SELECT * FROM forum_posts ORDER BY id DESC) AS p,
												forum_topics t,
												forum_categories c,
												forum_foras f
											WHERE
												t.id = p.topic_id
											AND
												f.id = t.forum_id
											AND
												c.id = f.category_id
											AND
												t.state != 'deleted'
											AND
												c.min_post_permissions <= '" . ((isset($user)) ? $user->RawData["permission_id"] : 0) . "'
											AND
												f.min_post_permissions <= '" . ((isset($user)) ? $user->RawData["permission_id"] : 0) . "'
											AND
												p.state != 'deleted'
											GROUP BY
												p.topic_id
											ORDER BY
												t.last_post DESC
											LIMIT
												10");
						while($ltFetch = DB::Fetch($ltQuery))
						{
							$icon = "folder_blue.png";
							$readed = !(DB::NumRowsQuery("SELECT 1 FROM `forum_readed` WHERE `topic_id` = '" . $ltFetch['tId'] . "' AND `user_id` = '" . ((isset($user)) ? $user->Id : 0) . "' AND `timestamp` >= '" . $ltFetch['postTimestamp'] . "'") === 0);
							$posted = !(DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE `topic_id` = '" . $ltFetch['tId'] . "' AND `user_id` = '" . ((isset($user)) ? $user->Id : 0) . "'") === 0);
							
							if($readed) $icon = "folder_grey.png";
							if($ltFetch['state'] == 'closed') $icon = str_replace(".png", "_locked.png", $icon);
							else if($ltFetch['sticky'] != '0') $icon = str_replace(".png", "_sticky.png", $icon);
							if($posted) $icon = str_replace(".png", "_posted.png", $icon);
							
							$us = new User($ltFetch['user_id'], false, false, true);
							echo'	<tr>
									<td style="width: 16px;" align="center"><a href="/forum/postredir?topic=' . $ltFetch['tId'] . '&ongelezen"><div style="margin-right: 0px; background: transparent;" class="icon gui-tooltip gui-tooltip-static" tt-data="Ga naar het laatst gelezen bericht"><img src="/cache/images/icons/forum/' . $icon . '" alt="Icoon" style="margin: -8px;" width="30" height="30" /></div></a></td>
									<td>
										<a href="/forum/postredir?pid=' . $ltFetch['pId'] . '">' . htmlspecialchars($ltFetch['tName']) . '</a><br/>
										<i style="color: Grey;">' . ((date("d-m-Y") == date("d-m-Y", $ltFetch['postTimestamp'])) ? 'Laatste reactie was om ' . date("H:i:s", $ltFetch['postTimestamp']) : 'Laatste reactie was op ' . date("d-m-Y, H:i:s", $ltFetch['postTimestamp'])) . '</i>
									</td>
									<td><span>Door <a href="/profiel/' . strtolower($us->Username) . '">' . htmlspecialchars(((str_replace(" ", "", $us->GetSetting("displayname")) != "") ? $us->GetSetting("displayname") : $us->Username)) . '</a></span></td>
								</tr>';
						}
						?>
					</table>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<script type="text/javascript">
	jQuery(window).ready(function(){
		var urls = {
			"Settings": "'/account/instellingen'"
		};
		<?php if($user->GetSetting("birthdate") == "") { echo'Glybe.Overlay.OpenUrlOverlay(\'/data/messagebox.php\', { mt:\'Geboortedatum invullen\', mc: \'Je hebt nog niet je geboortedatum ingevuld! Je geboortedatum hebben we nodig om bijvoorbeeld te weten wanneer je jarig bent, je kan je geboortedatum invullen onderin de instellingen pagina.\', ma: \'window.location = \' + urls.Settings + \';\' });'; } ?>
	});
	</script>
<?php
$footerLinks = Array('Home' => '/home');
include'content/footer.php';
?>