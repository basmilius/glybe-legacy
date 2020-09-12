<?php
include'includes/inc.bootstrap.php';

if(!isset($_GET['id']))
{
	header("location: /forum/index?topicNotExists");
	die();
}
$id = Glybe::Security($_GET['id']);
$p = ((isset($_GET['p']) && is_numeric($_GET['p'])) ? round($_GET['p']) : 1);
$topicQuery = DB::Query("SELECT * FROM `forum_topics` WHERE `id` = '" . $id . "'");
if(DB::NumRows($topicQuery) == 0)
{
	header("location: /forum/index?topicNotExists");
	die();
}
$topicFetch = DB::Fetch($topicQuery);
if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case "sticky":
			if(!$user->HasPermissions("forum_topic_sticky")) die();
			DB::Query("UPDATE `forum_topics` SET `sticky` = '1' WHERE `id` = '" . $topicFetch['id'] . "'");
			break;
		case "unsticky":
			if(!$user->HasPermissions("forum_topic_sticky")) die();
			DB::Query("UPDATE `forum_topics` SET `sticky` = '0' WHERE `id` = '" . $topicFetch['id'] . "'");
			break;
		case "del":
			if(!$user->HasPermissions("forum_topic_delete")) die();
			DB::Query("UPDATE `forum_topics` SET `state` = 'deleted' WHERE `id` = '" . $topicFetch['id'] . "'");
			break;
		case "undel":
			if(!$user->HasPermissions("forum_topic_delete")) die();
			DB::Query("UPDATE `forum_topics` SET `state` = 'open' WHERE `id` = '" . $topicFetch['id'] . "'");
			break;
		case "lock":
			if(!$user->HasPermissions("forum_topic_lock")) die();
			DB::Query("UPDATE `forum_topics` SET `state` = 'closed' WHERE `id` = '" . $topicFetch['id'] . "'");
			break;
		case "unlock":
			if(!$user->HasPermissions("forum_topic_lock")) die();
			DB::Query("UPDATE `forum_topics` SET `state` = 'open' WHERE `id` = '" . $topicFetch['id'] . "'");
			break;
	}
	header("location: " . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], $p));
}

$foraQuery = DB::Query("SELECT * FROM `forum_foras` WHERE `id` = '" . $topicFetch['forum_id'] . "'");
$foraFetch = DB::Fetch($foraQuery);

if($p <= 0 || $p > DB::NumRowsQuery("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicFetch['id'] . "'" . ((isset($user) && $user->HasPermissions("is_team")) ? '' : " AND p.state != 'deleted'"))) $p = 1;
if(isset($user) && $user->HasPermissions('is_team'))
{
	$ps = Glybe::PaginaSysteem(DB::NumRowsQuery("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicFetch['id'] . "'"), $p, 20);
	$postsQuery = DB::Query("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicFetch['id'] . "' ORDER BY p.first_post DESC, p.id LIMIT " . $ps['limit'] . ", 20");
}
else
{
	$ps = Glybe::PaginaSysteem(DB::NumRowsQuery("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicFetch['id'] . "' AND p.state != 'deleted'"), $p, 20);
	$postsQuery = DB::Query("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicFetch['id'] . "' AND p.state != 'deleted' ORDER BY p.first_post DESC, p.id LIMIT " . $ps['limit'] . ", 20");
}

$page = Array('title' => htmlspecialchars($topicFetch['caption']), 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';

$query_c = DB::Query("SELECT min_post_permissions FROM forum_categories WHERE id = '" . $foraFetch['category_id'] . "'");
$fetch_c = DB::Fetch($query_c);
?>
	<div class="content">
		<div class="container epic_large">
			<?php
			if((isset($user) && ($user->RawData['permission_id'] < $foraFetch['min_post_permissions']) OR isset($user) && ($user->RawData['permission_id'] < $fetch_c['min_post_permissions'])) || (!isset($user) && ($foraFetch['min_post_permissions'] != 0 || $fetch_c['min_post_permissions'] != 0)))
			{
			?>
				<div class="c_box">
					<div class="heading"><a href="/forum/index">Forum</a> &raquo; Geen toegang</div>
					<div class="inner">
						<div class="error_notif error">Je hebt geen rechten om dit topic te bekijken!</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="c_box">	
					<div class="heading"><a href="/forum/index">Forum</a> &raquo; <a href="/forum/forum?id=<?php echo $foraFetch['id']; ?>"><?php echo htmlspecialchars($foraFetch['caption']); ?></a> &raquo; <?php echo htmlspecialchars($topicFetch['caption']); ?></div>
					<div class="inner">
						<?php if(isset($user)) { ?><input type="button" value="<?php echo(($topicFetch['state'] == 'open') ? 'Reageren' : 'Dit topic is gesloten" disabled="true'); ?>" style="float: right;" onclick="document.getElementById('topic_post_txt').focus();" /><?php } ?>
						<div class="psystem" style="float: left;">
							<?php
							foreach($ps['paginas'] as $key => $value)
							{
								echo'<a href="' . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], $value[1]) . '"><div class="item">' . $value[0] . '</div></a>';
							}
							?>
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
						<div style="position: relative; margin: 5px; border-top: 1px solid #C6C6C6; border-bottom: 1px solid #E9E9E9;"></div>
						<?php
						$start = $ps['limit'];
						while($postFetch = DB::Fetch($postsQuery))
						{
							$poster = new User($postFetch['user_id'], false, true, true);
							$start++;
							$postIdString = sha1($postFetch['id']);
							
							echo'	<div class="topic_msg" id="post_' . $postIdString . '">
									<div class="user_tile">
										<div class="profile_picture">
											<img src="' . str_replace("thumb_", "thumb_149_", $poster->Avatar) . '" alt="Profiel-foto" height="149" width="149" />
											<div class="username"><a href="/profiel/' . strtolower($poster->Username) . '">' . htmlspecialchars((($poster->GetSetting("displayname") != "") ? $poster->GetSetting("displayname") : $poster->Username)) . '</a></div>
										</div>
										<div class="user_info">
											<strong>Rang</strong><br/>
											' . $poster->PermissionName . '<br/>
											<strong>Aantal Posts</strong><br/>
											' . number_format(DB::NumRowsQuery("SELECT 1 FROM forum_posts WHERE user_id = '" . $poster->Id . "'"), 0, ",", ".") . '<br/>
											<strong>Lid Sinds</strong><br/>
											' . strftime('%e %B \'%y', $poster->RawData['reg_date']) . '
											<div class="arrow"></div>
										</div>
									</div>
									<div class="msg_balloon">
										<div class="top" style="z-index: 1;">
											' . strftime('%e %B %Y om %H:%M:%S uur', $postFetch['post_timestamp']) . ' gepost door ' . ucfirst($poster->Username) . '
											<div class="post_options">
												<a style="position: absolute; top: -30px;" name="' . $postFetch['id'] . '"></a>
												<div class="selectbox" style="font-weight: normal;">
													<div class="icon user_gray gui-tooltip gui-tooltip-static" tt-data="Moderatie"></div>
													<div class="dropdown" style="top: 16px; right: 0px;">
														<div class="item static">Opties&nbsp;voor&nbsp;het&nbsp;topic</div>
														<a href="/forum/topic?id=' . $id . '&action=unlock&p=' . $p . '"><div class="item">Topic&nbsp;openen</div></a>
														<a href="/forum/topic?id=' . $id . '&action=lock&p=' . $p . '"><div class="item">Topic&nbsp;sluiten</div></a>
														<a href="/forum/topic?id=' . $id . '&action=sticky&p=' . $p . '"><div class="item">Sticky&nbsp;maken</div></a>
														<a href="/forum/topic?id=' . $id . '&action=unsticky&p=' . $p . '"><div class="item">Sticky&nbsp;verwijderen</div></a>
														<a href="/forum/topic?id=' . $id . '&action=del&p=' . $p . '"><div class="item">Topic&nbsp;verwijderen</div></a>
														<a href="/forum/topic?id=' . $id . '&action=undel&p=' . $p . '"><div class="item">Topic&nbsp;terughalen</div></a>
														<div class="item static">Opties&nbsp;voor&nbsp;deze&nbsp;post</div>
														<div class="item">Post&nbsp;verwijderen</div>
														<div class="item">Post&nbsp;terughalen</div>
													</div>
												</div>
											</div>
										</div>
										<div class="mid">
											' . (($topicFetch['state'] == 'deleted' && isset($user) && $user->HasPermissions("forum_topic_delete") && $start == 1) ? '<div class="error_notif error">Dit topic is verwijderd</div>' : '') . '										
											<div id="postDel_'.$postFetch['id'].'"></div>
											' . (($postFetch['state'] == 'deleted') ? '<div id="del_'.$postFetch['id'].'" class="error_notif error">Dit bericht is verborgen!</div>' : '') . '
											<div class="post_edit"></div>
											<div class="post_message" id="post_text">
												' . UBB::Parse($postFetch['message'], $poster->HasPermissions("is_team")) . '
											</div>
											<div class="onderschrift">
												' . (($poster->GetSetting("onderschrift") != "") ? '<div style="position: relative; margin: 5px 0px; border-top: 1px solid #C6C6C6; border-bottom: 1px solid #E9E9E9;"></div><div style="position: relative; padding: 4px;">' . UBB::Parse($poster->GetSetting("onderschrift")) . '</div>' : '') . '
											</div>
										</div>
										<div class="arrow"></div>
									</div>
									<div class="clear"></div>
								</div>
								<input type="hidden" class="topicLastId" value="' . $postFetch['id'] . '" />';
						}
						if(isset($user))
						{
							if(DB::NumRowsQuery("SELECT 1 FROM `forum_readed` WHERE `user_id` = '" . $user->Id . "' AND `topic_id` = '" . $topicFetch['id'] . "'") === 0)
							{
								DB::Query("INSERT INTO `forum_readed` (forum_id, topic_id, user_id, post_id, `timestamp`) VALUES ('" . $foraFetch['id'] . "', '" . $topicFetch['id'] . "', '" . $user->Id . "', (SELECT id FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "' ORDER BY id DESC LIMIT 1), UNIX_TIMESTAMP())");
							} else {
								DB::Query("UPDATE `forum_readed` SET `post_id` = (SELECT id FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "' ORDER BY id DESC LIMIT 1), `timestamp` = UNIX_TIMESTAMP() WHERE `user_id` = '" . $user->Id . "' AND `topic_id` = '" . $topicFetch['id'] . "'");
							}
						}
						if($topicFetch['state'] != 'closed' && DB::NumRowsQuery("SELECT 1 FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "'") >= 9900)
						{
							echo'<div class="error_notif error">Dit topic heeft nu bijna het maximum aantal posts bereikt en word bij 10.000 posts automatisch gesloten<br/>Diegene die als laatste heeft gepost mag een nieuw topic aanmaken, als dat na 5 minuten niet gedaan is mag je zelf een topic maken</div>';
						}
						?>
						<div style="position: relative; margin: 5px; border-top: 1px solid #C6C6C6; border-bottom: 1px solid #E9E9E9;"></div>
						<div class="error_notif information" id="topic_new_messages_inf" style="display: none; cursor: pointer;" onclick="jQuery('div.topic_hidden_new_messages').slideDown(); jQuery(this).fadeOut(0);">Er zijn nieuwe berichten gepost terwijl je aan het typen was, klik hier om ze te laden</div>
						<div class="psystem">
							<?php
							foreach($ps['paginas'] as $key => $value)
							{
								echo'<a href="' . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], $value[1]) . '"><div class="item">' . $value[0] . '</div></a>';
							}
							?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<a name="reageren"></a>
				<?php
				if(!isset($user)) {
					echo '<div class="error_notif error">Je moet ingelogd zijn om te kunnen posten!</div>';
				} else if($topicFetch['state'] != "open") {
					echo '<div class="error_notif error">Dit topic is gesloten, je kan daarom niet meer reageren.</div>';
				} else {
				?>
				<div class="c_box">
					<div class="heading">Reageren | <a href="/forum/index">Forum</a> &raquo; <a href="/forum/forum?id=<?php echo $foraFetch['id']; ?>"><?php echo htmlspecialchars($foraFetch['caption']); ?></a> &raquo; <?php echo htmlspecialchars($topicFetch['caption']); ?> <span style="float: right;"><a href="#top">Naar boven</a></span></div>
					<div class="inner">
						<div id="topic_post_error" class="error_notif error" style="display: none;"></div>
						<div id="topic_post_info" class="error_notif information" style="display: none;"></div>
						<div id="topic_post_success" class="error_notif success" style="display: none;"></div>
						<table border="0" cellspacing="0" style="width: 100%;">
							<tr>
								<td valign="top" style="width: 340px;">
									<div>
										<input type="button" onclick="Glybe.Forum.AddUBB('[b]', '[/b]');" value="b" style="font-weight: bold;" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[i]', '[/i]');" value="i" style="font-style: italic;" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[u]', '[/u]');" value="u" style="text-decoration: underline;" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[s]', '[/s]');" value="s" style="text-decoration: line-through;" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[url]', '[/url]');" value="URL" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[quote]', '[/quote]');" value="Quote" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[code]', '[/code]');" value="Code" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[youtube]', '[/youtube]');" value="YouTube" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[spoiler]', '[/spoiler]');" value="Spoiler" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[img]', '[/img]');" value="img" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[ignore]', '[/ignore]');" value="ignore" class="ubb-button" />
										<input type="button" onclick="Glybe.Forum.AddUBB('[color]', '[/color]');" value="kleur" class="ubb-button" />
									</div>
								</td>
								<td valign="top" style="width: 530px;">
									<textarea id="topic_post_txt" placeholder="Typ hier je reactie op dit topic" style="width: 510px; height: 160px;" onfocus="Glybe.Forum.TextAreaFocused = true;" onblur="Glybe.Forum.TextAreaFocused = false;"></textarea>
								</td>
							</tr>
							<?php
							if($user->HasPermissions('is_admin'))
							{
							?>
							<tr>
								<td>
									<label for="title">Glybe bot</label>
								</td>
								<td>
									<select id="bot" name="bot">
										<option value="0">Post als <?= $user->Username; ?></option>
										<option value="1">Post als Glybe bot</option>
									</select>
								</td>									
							</tr>
							<?php
							}
							else
							{
								echo '<input type="hidden" value="0" id="bot" />';
							}
							?>
							<tr>
								<td></td>
								<td>
									<input type="button" value="Posten" onclick="Glybe.Forum.Post(<?php echo $topicFetch['id']; ?>, '<?php echo sha1($topicFetch['id']); ?>', '<?php echo sha1($user->Id); ?>', document.getElementById('topic_post_txt').value, document.getElementById('bot').value, <?php echo $p; ?>);" />
								</td>
							</tr>
						</table>
					</div>
				</div>
				<?php } ?>
			<?php } ?>
			<div class="c_box">
				<div class="heading"><div class="icon group"></div>Wie bekijkt het topic</div>
				<div class="inner">
					<?php
					$wbdtArray = Array();
					$wbdtQuery = DB::Query("	SELECT
											u.*,
											us.displayname uDisplayName
										FROM
											users_online o,
											users u,
											users_settings us
										WHERE
											o.last_active > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
										AND
											o.user_id != '0'
										AND
											o.last_topic = '" . $topicFetch['id'] . "'
										AND
											u.id = o.user_id
										AND
											us.user_id = u.id
										ORDER BY
											u.username");
					while($wbdtFetch = DB::Fetch($wbdtQuery))
					{
						$wbdtArray[] = $wbdtFetch;
					}
					if(count($wbdtArray) > 0)
					{
						foreach($wbdtArray as $key => $wbdt)
						{
							echo'	<a href="/profiel/' . strtolower($wbdt['username']) . '" class="gui-tooltip" tt-data="' . htmlspecialchars($wbdt['username']) . '">' . htmlspecialchars(((str_replace(" ", "", $wbdt['uDisplayName']) != "") ? $wbdt['uDisplayName'] : $wbdt['username'])) . '</a>' . (((count($wbdtArray) - 1) == $key) ? '' : (((count($wbdtArray) - 2) == $key) ? ' en ' : ', '));
						}
					} else {
						echo'	<center><i>Niemand bekijkt dit topic..</i></center>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<script type="text/javascript">
	jQuery(window).ready(function(){return;
		<?php if($ps['max'] == $p) { ?>window.setInterval(function(){Glybe.Forum.GetNewMessages(<?php echo $topicFetch['id']; ?>, <?php echo $p; ?>);}, 6000);<?php } ?>
	});
	</script>
<?php
$footerLinks = Array('Forum' => '/forum/index', $foraFetch['caption'] => '/forum/forum?id=' . $foraFetch['id'], htmlspecialchars($topicFetch['caption']) => '/forum/topic?id=' . $topicFetch['id']);
include'content/footer.php';
?>