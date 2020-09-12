<?php
define('YT_PLAYER_IN_HEADER', true);
include'includes/inc.bootstrap.php';

if(!isset($_GET['u']) || (isset($_GET['u']) && $_GET['u'] == ''))
{
	if(isset($user))
	{
		$_GET['u'] = strtolower($user->Username);
	}
}
$uName = Glybe::Security($_GET['u']);
$uQuery = DB::Query("SELECT id FROM users WHERE username = '" . $uName . "'");
if(DB::NumRows($uQuery) === 0)
{
	header("location: /error_404.html");
	die();
}
$uFetch = DB::Fetch($uQuery);
if(isset($user) && $uFetch['id'] == $user->Id)
{
	$u = $user;
} else {
	$u = new User($uFetch['id'], false, true, true);
}

$page = Array('css' => Array('/cache/user-defined-style/user-' . $u->Id . '.css'), 'title' => htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname")) != "") ? $u->GetSetting("displayname") : $u->Username)), 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';

if((isset($user) && $u->Id != $user->Id) || !isset($user))
{
	DB::Query("UPDATE users_settings SET profile_views = profile_views + 1 WHERE user_id = '" . $u->Id . "'");
}
if($u->Id == 3)
{
	header("location: /error_404.html");
	die();
}
?>
	<div class="content profile">
		<div class="omslagfoto" style="<?php echo(($u->GetSetting("profile_cover") != "") ? 'background: url(http://grolsch.static-gly.be/static-content-a/user-coverpictures/' . $u->GetSetting("profile_cover") . ');' : ''); ?>">
			<div class="profile_picture">
				<img src="<?php echo str_replace("thumb_", "thumb_149_", $u->Avatar); ?>" height="140" width="140" />
				<div class="shadow"></div>
			</div>
			<div class="effect"></div>
			<div class="light_bar">
				<span><strong class="gui-tooltip" tt-data="<?php echo htmlspecialchars($u->Username); ?>"><?php echo htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname")) != "") ? $u->GetSetting("displayname") : $u->Username)); ?></strong></span>
				<div class="icon_set">
					<div style="position: relative; margin: 0px 5px; float: left;" class="gui-tooltip" tt-data="Aantal keer bekeken"><div class="icon eye"></div><strong><?php echo number_format($u->GetSetting("profile_views"), 0, ",", "."); ?>x</strong></div>
					<div style="position: relative; margin: 0px 5px; float: left;" class="gui-tooltip" tt-data="Aantal punten"><div class="icon coins"></div><strong><?php echo number_format($u->GetSetting("punten"), 2, ",", "."); ?></strong></div>
					<div style="position: relative; margin: 0px 5px; float: left;" class="gui-tooltip" tt-data="Aantal keer gerespecteerd"><div class="icon thumb_up"></div><strong><?php echo number_format($u->GetSetting("respect"), 0, ",", "."); ?></strong></div>
					<?php if(isset($user)) { ?>
						<div class="icon" style="background: transparent;"></div>
						<?php if($user->Id != $u->Id) { ?>
							<?php if($user->IsFriend($u->Id)) { ?>
								<div onclick="Glybe.Overlay.OpenUrlOverlay('/data/vriend_verwijderen.php', { uId: '<?php echo $u->Id; ?>' });" class="icon user_delete gui-tooltip gui-tooltip-static" tt-data="Verwijderen als vriend"></div>
							<?php } else { ?>
								<div onclick="Glybe.Overlay.OpenUrlOverlay('/data/vriend_toevoegen.php', { uId: '<?php echo $u->Id; ?>' });" class="icon user_add gui-tooltip gui-tooltip-static" tt-data="Toevoegen als vriend"></div>
							<?php } ?>
							<div onclick="window.location = '/berichten/maak?users_to=<?php echo strtolower($u->Username); ?>';" class="icon email_go gui-tooltip gui-tooltip-static" tt-data="Priv&eacute;bericht sturen"></div>
						<?php } else { ?>
							<div onclick="window.location = '/account/profiel_instellingen';" class="icon user_edit gui-tooltip gui-tooltip-static" tt-data="Mijn profiel aanpassen"></div>
						<?php } ?>
						<?php if($user->HasPermissions("is_team")) { ?>
							<div onclick="window.location = '/warn?id=<?php echo $u->Id; ?>';" class="icon error gui-tooltip gui-tooltip-static" tt-data="Waarschuwen (team)"></div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
			<?php /*<div class="motto">
				<div class="top"></div>
				<div class="mid">
					<div class="text">Dit is mijn motto! :')</div>
				</div>
				<div class="bot"></div>
			</div>*/ ?>
		</div>
		<div class="container" style="width: 380px;">
			<div class="c_box">
				<div class="heading"><div class="icon group"></div>Vrienden</div>
				<div class="inner" id="myFriends">
					<i>Vrienden worden geladen...</i>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon user_comment"></div>Laatst gereageerd in..</div>
				<div class="inner">
					<table class="top_list" border="0" cellspacing="1" style="width: 100%;">
						<?php
						$ltQuery = DB::Query("	SELECT
												t.caption tName,
												p.id pId,
												p.post_timestamp postTimestamp
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
											AND
												p.user_id = '" . $u->Id . "'
											GROUP BY
												p.topic_id
											ORDER BY
												p.post_timestamp DESC
											LIMIT
												5");
						while($ltFetch = DB::Fetch($ltQuery))
						{
							echo'	<tr>
									<td style="width: 20px;" align="center"><div class="icon folder_go"></div></td>
									<td><a href="/forum/postredir?pid=' . $ltFetch['pId'] . '">' . htmlspecialchars($ltFetch['tName']) . '</a></td>
									<td>' . Glybe::TimeAgo($ltFetch['postTimestamp']) . '</td>
								</tr>';
						}
						?>
					</table>
				</div>
			</div>
		</div>
		<div class="container" style="width: 500px; margin-top: -45px;">
			<div class="c_box">
				<div class="inner">
					<?php echo ((str_replace(" ", "", $u->GetSetting("profile_story")) != "") ? UBB::Parse($u->GetSetting("profile_story"), $u->HasPermissions("is_team")) : '<center><i>' . ucfirst($u->Username) . ' heeft nog geen verhaal geschreven.</i></center>'); ?>
				</div>
			</div>
			<div class="c_box" style="margin-top: 13px;">
				<div class="heading"><div class="icon comments"></div>Gastenboek</div>
				<div class="inner" id="guestbook_goes_here">
					<i>Gastenboek berichten laden..</i>
				</div>
				<?php if(isset($user)) { ?>
				<div class="inner">
					<div class="error_notif error" style="display: none;" id="gb_post_error">Lol :D</div>
					<strong>Plaats een bericht</strong><br/>
					<textarea id="gb_post_txt" style="width: 460px; height: 80px;"></textarea><br/>
					<input type="button" value="Posten" onclick="Glybe.Profile.PostGuestbook(<?php echo $u->Id; ?>, document.getElementById('gb_post_txt').value);document.getElementById('gb_post_txt').value='';" />
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		Glybe.Profile.GetGuestbook(<?php echo $u->Id; ?>, 1);
		Glybe.Profile.GetFriends(<?php echo $u->Id; ?>, 1);
	});
	</script>
	<?php
	$mArray = Array();
	$mQuery = DB::Query("SELECT * FROM profile_music WHERE user_id = '" . $u->Id . "' ORDER BY RAND()");
	while($mFetch = DB::Fetch($mQuery)) $mArray[] = $mFetch;
	if(count($mArray) > 0)
	{
		$ids = "";
		foreach($mArray as $key => $value)
		{
			$ids .= $value['yt_id'] . ",";
		}
		echo'<script type="text/javascript">function onHeadingPlayerIsReady() { Glybe.Sounds.HeadingPlayer.LoadVideosById(\'' . substr($ids, 0, -1) . '\'); }</script>';
	}
	?>
<?php
$footerLinks = Array('Profiel' => '/glybe/online', ucfirst($u->Username) => '/profiel/' . strtolower($u->Username));
include'content/footer.php';
?>