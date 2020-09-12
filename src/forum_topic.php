<?php
include'includes/inc.bootstrap.php';

if(!isset($_GET['id']))
{
	header("location: /forum/index?topicNotExists");
	die();
}
$id = Glybe::Security($_GET['id']);
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
	header("location: /forum/topic?id=" . $topicFetch['id']);
}

$foraQuery = DB::Query("SELECT * FROM `forum_foras` WHERE `id` = '" . $topicFetch['forum_id'] . "'");
$foraFetch = DB::Fetch($foraQuery);

$p = ((isset($_GET['p']) && is_numeric($_GET['p'])) ? round($_GET['p']) : 1);
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
$_SESSION['lastTopicCheck'] = time();
$_SESSION['lastPostCounter'] = 0;

$page = Array('title' => $topicFetch['caption'], 'access' => Array(true, true));
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
			<?php
			}
			else
			{
			?>
			<div class="c_box">	
				<div class="heading"><a href="/forum/index">Forum</a> &raquo; <a href="/forum/forum?id=<?php echo $foraFetch['id']; ?>"><?php echo htmlspecialchars($foraFetch['caption']); ?></a> &raquo; <?php echo htmlspecialchars($topicFetch['caption']); ?></div>
				<div class="inner">
					<?php
					if(isset($user) && $user->HasPermissions('move_topic'))
					{
						$catoQuery = DB::Query("
												SELECT
													caption,
													id
												FROM
													forum_categories
												ORDER BY
													id
												ASC
												");
						if(isset($_POST['categorie']))
						{
							DB::Query("UPDATE forum_topics SET forum_id = '".DB::Escape($_POST['categorie'])."' WHERE id = '".DB::Escape($topicFetch['id'])."'");
							DB::Query("UPDATE forum_posts SET forum_id = '".DB::Escape($_POST['categorie'])."' WHERE topic_id = '".DB::Escape($topicFetch['id'])."'");
							header('Location: ' . $_SERVER['REQUEST_URI']);
						}
						?>
						<div style="float: left;">
							<form action="" method="POST">
								<strong>Verplaats topic:</strong> 						
								<select name="categorie" onChange="form.submit();">
									<?php
									while($catoFetch = DB::Fetch($catoQuery))
									{
										echo '<optgroup label="'.htmlspecialchars($catoFetch['caption']).'">';
										$foraQuery = DB::Query("SELECT * FROM forum_foras WHERE category_id = '".$catoFetch['id']."'");
										while($foraFetch2 = DB::Fetch($foraQuery))
										{
											if($topicFetch['forum_id'] == $foraFetch2['id'])
											{
												echo '<option value="'.$foraFetch2['id'].'" selected>'.htmlspecialchars($foraFetch2['caption']).'</option>';
											}
											else
											{
												echo '<option value="'.$foraFetch2['id'].'">'.htmlspecialchars($foraFetch2['caption']).'</option>';
											}
										}
									}
									?>
								</select>					
							</form>
						</div>
						<br/>
						<br/>
					<?php } ?>
					<?php if(isset($user)) { ?><input type="button" value="<?php echo(($topicFetch['state'] == 'open') ? 'Reageren' : 'Dit topic is gesloten" disabled="true'); ?>" style="float: right;" onclick="document.getElementById('topic_post_txt').focus();" /><?php } ?>
					<div class="psystem" style="float: left;">
						<?php
						foreach($ps['paginas'] as $key => $value)
						{
							echo'<a href="' . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], $value[1]) . '"><div class="item">' . $value[0] . '</div></a>';
							//echo'<a href="/forum/topic?id=' . $topicFetch['id'] . '&p=' . $value[1] . '"><div class="item">' . $value[0] . '</div></a>';
						}
						?>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
					<?php
					$start = $ps['limit'];
					$lastId = 0;
					while($postFetch = DB::Fetch($postsQuery))
					{
						$poster = new User($postFetch['user_id'], false, true, true);
						$start++;
						$_SESSION['lastPostCounter'] = $start;
						$postIdString = sha1($postFetch['id']);
						
						$hoeActiefBenIk = '<div class="gui-tooltip" tt-data="' . jesseRound($poster->Active, 5) . '% Actief op Glybe" style="position: relative; padding: 3px 7px; margin: 0px 17px;">%S1%%S2%%S3%%S4%%S5%<div class="clear"></div>';
						
						for($i = 1; $i <= 5; $i++)
						{
							$b = ($i *20);
							$s = '<div class="icon %COD%"></div>';
							
							if(jesseRound($poster->Active, 5) >= $b)
							{
								$s = str_replace('%COD%', 'star', $s);
							}elseif(jesseRound($poster->Active, 5) >= ($b - 5) && jesseRound($poster->Active, 5) <= $b)
							{
								$s = str_replace('%COD%', 'star2', $s);
							}else{
								$s = str_replace('%COD%', 'star_grey', $s);
							}
							
							$hoeActiefBenIk = str_replace("%S" . $i . "%", $s, $hoeActiefBenIk);
						}
						
						if(!empty($postFetch['last_edit']))
						{
							$uEdit = new User($postFetch['last_edit_by'], false, false, true);
							$lastEdit = '<br/><br/><i>Laatst bewerkt door <a href="/profiel/' . strtolower($uEdit->Username) . '">' . htmlspecialchars(((str_replace(" ", "", $uEdit->GetSetting("displayname")) != "") ? $uEdit->GetSetting("displayname") : $uEdit->Username)) . '</a> op ' . strftime('%e %B %Y om %H:%M:%S uur', $postFetch['last_edit']) . '</i>';
						}
						
						echo'	<table class="postTable" id="' . $postIdString . '" border="0" cellspacing="0" style="width: 100%; border: 1px solid #006D9C; border-top: none; border-bottom: none;">
								<tr style="background: url(/cache/style_default/images/topic_post_bg.png); #789ABC; color: #FFFFFF; font-weight: bold;">
									<td style="padding: 6px; width: 160px;"><a href="/forum/postredir?pid=' . $postFetch['id'] . '" style="color: #FFFFFF;">Post #' . number_format($start, 0, ",", ".") . '</a></td>
									<td style="padding: 6px; position: relative;" valign="center">
										<a style="position: absolute; top: -30px;" name="' . $postFetch['id'] . '"></a>
										' . strftime('%e %B %Y om %H:%M:%S uur', $postFetch['post_timestamp']) . ' gepost door ' . ucfirst($poster->Username) . '
										' . ((isset($user)) ? '<a onclick="Glybe.Overlay.Report(' . $postFetch['id'] . ');" style="cursor: pointer; float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Deze post aangeven"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/error.png" /></a>' : '') . '
										' . ((isset($user)) ? '<a href="#reageren" onclick="Glybe.Forum.GetUbbMessage(' . $postFetch['id'] . ');" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Deze post Quoten"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/user_comment.png" /></a>' : '') . '
										' . ((isset($user) && $user->Id != $postFetch['user_id']) ? '<a href="javascript:void();" onclick="Glybe.Forum.GiveRespect(' . $postFetch['user_id'] . ');" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Geef Respect!"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/thumb_up.png" /></a>' : '') . '
										' . ((isset($user) && $user->Id != $postFetch['user_id']) ? '<a href="/berichten/maak?users_to=' . $poster->Username . '" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Stuur priv&eacute;bericht"><img src="/cache/style_default/images/icons/famfamfam/email_go.png" /></a>' : '') . '
										' . ((isset($user) && $user->Id != $postFetch['user_id'] && !$user->IsFriend($postFetch['user_id'])) ? '<a href="javascript:void();" onclick="Glybe.Overlay.OpenUrlOverlay(\'/data/vriend_toevoegen.php\', { uId: ' . $postFetch['user_id'] . ' });" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Toevoegen als vriend"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/user_add.png" /></a>' : '') . '
										' . ((isset($user) && $user->Id != $postFetch['user_id'] && $user->IsFriend($postFetch['user_id'])) ? '<a href="javascript:void();" onclick="Glybe.Overlay.OpenUrlOverlay(\'/data/vriend_verwijderen.php\', { uId: ' . $postFetch['user_id'] . ' });" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Verwijderen uit vriendenlijst"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/user_delete.png" /></a>' : '') . '
										' . ((isset($user) && $user->HasPermissions("forum_topic_lock") && $start == 1) ? '<a href="/forum/topic?id=' . $topicFetch['id'] . '&action=' . (($topicFetch['state'] == 'closed') ? 'unlock' : 'lock') . '" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Topic sluiten of openen"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/lock_' . (($topicFetch['state'] == 'closed') ? 'delete' : 'add') . '.png" /></a>' : '') . '
										' . ((isset($user) && $user->HasPermissions("forum_topic_delete") && $start == 1) ? '<a href="/forum/topic?id=' . $topicFetch['id'] . '&action=' . (($topicFetch['state'] == 'deleted') ? 'undel' : 'del') . '" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Topic verwijderen of terugzetten"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/' . (($topicFetch['state'] == 'deleted') ? 'arrow_refresh' : 'bin') . '.png" /></a>' : '') . '
										' . ((isset($user) && $user->HasPermissions("forum_topic_sticky") && $start == 1) ? '<a href="/forum/topic?id=' . $topicFetch['id'] . '&action=' . (($topicFetch['sticky'] == '1') ? 'unsticky' : 'sticky') . '" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Topic sticky/non-sticky maken"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/' . (($topicFetch['sticky'] == '1') ? 'error_delete' : 'error_add') . '.png" /></a>' : '') . '
										' . ((isset($user) && $user->Id == $postFetch['user_id'] || isset($user) && $user->HasPermissions("forum_topic_edit")) ? '<a href="javascript:void(0);" onclick="Glybe.Forum.PostEdit(' . $postFetch['id'] . ', \'' . $postIdString . '\', \''.$start.'\');" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Bericht bewerken"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/pencil.png" /></a>': '') . '
										' . ((isset($user) && $user->HasPermissions("forum_post_delete") && $start != 1) ? '<a href="javascript:void(0);" onclick="Glybe.Forum.PostDel' . (($postFetch['state'] == 'deleted') ? 'Back' : '') . '(' . $postFetch['id'] . ');" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="Bericht ' . (($postFetch['state'] == 'deleted') ? 'Terughalen' : 'Verwijderen') . '"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/' . (($postFetch['state'] == 'deleted') ? 'accept' : 'delete') . '.png" /></a>' : '') . '
										' . ((isset($user) && $user->HasPermissions("forum_first_post") && $start != 1) ? '<a href="javascript:void(0);" onclick="Glybe.Forum.FirstPost' . (($postFetch['first_post'] == '1') ? '2' : '') . '(' . $postFetch['id'] . ');" style="float: right; margin: -2px 3px;" class="gui-tooltip gui-tooltip-static" tt-data="' . (($postFetch['first_post'] == '1') ? 'Verwijder' : 'Maak') . ' FirstPost"><img height="16" width="16" src="/cache/style_default/images/icons/famfamfam/arrow_' . (($postFetch['first_post'] == '1') ? 'down' : 'up') . '.png" /></a>' : '') . '
									</td>
								</tr>
								<tr class="postSide">
									<td valign="top" style="padding: 5px; border-right: 1px solid #006D9C;">
										<div class="icon ' . (($poster->IsOnline || $poster->Id == 3) ? 'bullet_green' : 'bullet_red') . '" style="float: right; margin: -3px -4px;"></div>
										' . (($poster->Id != 3) ? '<a href="/profiel/' . strtolower($poster->Username) . '" class="gui-tooltip" tt-data="Ga naar profiel"><strong>' . htmlspecialchars(((str_replace(" ", "", $poster->GetSetting("displayname")) != "") ? $poster->GetSetting("displayname") : $poster->Username)) . '</strong></a>' : '<strong>Glybe</strong>') . '<br/>
										' . (($poster->Id != 3) ? '<i>' . $poster->PermissionName . '</i>' : '<i>Glybe-bot</i>') . '<br/>
										' . (($poster->Id != 3) ? '<div style="margin: 3px auto;">' . $poster->GetAvatar(160) . '</div>' : '') . '
										' . (($poster->Id != 3) ? '<div class="nav_link static"><div class="icon email_go"></div><strong>Berichten:</strong> ' . number_format(DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE `user_id` = '" . $poster->Id . "'"), 0, ",", ".") . '</div>' : '') . '
										' . (($poster->Id != 3) ? '<div class="nav_link static"><div class="icon coins"></div><strong>Punten:</strong> ' . number_format($poster->GetSetting("punten"), 2, ",", ".") . '</div>' : '') . '
										' . (($poster->Id != 3) ? '<div class="nav_link static"><div class="icon thumb_up"></div><strong>Respect:</strong> ' . number_format($poster->GetSetting("respect"), 0, ",", ".") . '</div>' : '') . '
										' . (($poster->Id != 3) ? '<div class="nav_link static"><div class="icon asterisk_orange"></div><strong>Lid sinds:</strong> ' . strftime('%e %B \'%y', $poster->RawData['reg_date']) . '</div>' : '') . '
										' . (($poster->Id != 1 && $poster->Id != 8 && $poster->Id != 3) ? $hoeActiefBenIk : '') . '
									</td>
									<td class="postText" valign="top" style="padding: 5px; max-width: 680px; overflow: auto;">
										' . (($topicFetch['state'] == 'deleted' && isset($user) && $user->HasPermissions("forum_topic_delete") && $start == 1) ? '<div class="error_notif error">Dit topic is verwijderd</div>' : '') . '										
										<div id="postDel_'.$postFetch['id'].'"></div>
										' . (($postFetch['state'] == 'deleted') ? '<div id="del_'.$postFetch['id'].'" class="error_notif error">Dit bericht is verborgen!</div>' : '') . '
										<div class="post_edit"></div>
										<div class="post_text">
											<div style="position: relative; padding: 4px;" id="postContent_' . $postFetch['id'] . '">																			
											' . UBB::Parse($postFetch['message'], $poster->HasPermissions("is_team")) . '
											' . ((isset($lastEdit)) ? $lastEdit : '') . '
											</div>
											' . (($poster->GetSetting("onderschrift") != "") ? '<div style="margin: 5px; position: relative; border-top: 1px solid #789ABC;"></div><div style="position: relative; padding: 4px;">' . UBB::Parse($poster->GetSetting("onderschrift")) . '</div>' : '') . '
										</div>
									</td>
								</tr>
							</table>
							<input type="hidden" class="topicLastId" value="' . $postFetch['id'] . '" />';
						$lastId = $postFetch['id'];
						unset($lastEdit);
					}
					echo'<div style="position: relative; border-top: 1px solid #006D9C;"></div>';
					$lpId = DB::Fetch(DB::Query("SELECT id FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "' ORDER BY id DESC LIMIT 1"));
					$_SESSION['lastPostId'] = $lpId['id'];
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
					<div class="error_notif information" id="topic_new_messages_inf" style="display: none; cursor: pointer;" onclick="jQuery('div.topic_hidden_new_messages').slideDown(); jQuery(this).fadeOut(0);">Er zijn nieuwe berichten gepost terwijl je aan het typen was, klik hier om ze te laden</div>
					<div class="psystem">
						<?php
						foreach($ps['paginas'] as $key => $value)
						{
							echo'<a href="' . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], $value[1]) . '"><div class="item">' . $value[0] . '</div></a>';
							//echo'<a href="/forum/topic?id=' . $topicFetch['id'] . '&p=' . $value[1] . '"><div class="item">' . $value[0] . '</div></a>';
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
								<div>
									<input type="button" style="background: transparent url('/cache/images/smilies/smile.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/biggrin.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':D');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/emo.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':\')');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/redface.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':o');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/yummie.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':p');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/sadley.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':(');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/confused.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':s');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/schater.gif') no-repeat; border:1px solid #FFFFFF;padding:15px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':F');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/cry.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':\'(');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/bloos.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':$');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/clown.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':+');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/frown.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':@');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/coool.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(h)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/hypocrite.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(a)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/santabaard.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(aa)');" />
								
									<input type="button" style="background: transparent url('/cache/images/smilies/bye.gif') no-repeat; border:1px solid #FFFFFF;padding:15px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(zwaai)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/shutup.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':x');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/wink.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(';)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/workshippy.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('_o_');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/we.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(tss)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/nerd.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('8-|');" />
																	
									<input type="button" style="background: transparent url('/cache/images/smilies/nooo.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(n)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/yes_new.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(j)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/xd.png') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('xd');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/swhip.gif') no-repeat; border:1px solid #FFFFFF;padding:30px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('~o');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/yawnee.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(gaap)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/we.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(tss)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_dubbelpuntdrie.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':3');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_puntkommaoo.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(';oo');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_shahiemsmilie.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':G');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':\'O');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_nouhouw.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/icon_kirakira.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('*.*');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_dance.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('*dance*');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_jop.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':jop:');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_woot.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(woot)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_oehh.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(';;');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/vork.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(vork)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/thumbs_up.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(y)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/spinsmile.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(spin)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/sm_eerie.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('-_-');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/slotje.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(slotje)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/sleephappy.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(zzz)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/sleepey.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(zzz2)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/sint.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(sint)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/shiney.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':*');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/sbatje.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(slaan)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/rc5.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(koe)');" />
								
									<input type="button" style="background: transparent url('/cache/images/smilies/push.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(push)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/puckey.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('+o(');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/puh2.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(puh)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/pompom.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('*o*');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/peace.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(peace)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/party.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(party)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/paashaas.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(paashaas)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/nosmile.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat(':l');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/nopompom.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('-o-');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/michel.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(michel)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/marrysmile.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(marry)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/loveys.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(love)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/loveit.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(cigar)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/kwijl.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(kwijl)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/kutslotje.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(kutslot)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/kerst.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(kerst)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/iagree.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(agree)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/wls_heart.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(L)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/handsup.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(handsup)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/frusty.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(pok)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/focus2.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(focus)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/devil.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(6)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/develisch.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(duivel)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/chicksmiley.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(kip)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/blabla.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(bla)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/bier.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(bier)');" />
									<input type="button" style="background: transparent url('/cache/images/smilies/banpleace.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(ban)');" />
									
									<input type="button" style="background: transparent url('/cache/images/smilies/amen.gif') no-repeat; border:1px solid #FFFFFF;padding:10px;" 
									onclick="document.getElementById('topic_post_txt').value=document.getElementById('topic_post_txt').value.concat('(amen)');" />
								</div>
							</td>
							<td valign="top" style="width: 530px;">
								<textarea <?php if($topicFetch['id'] == 139) { ?>onkeyup="var e = window.event; if(e.keyCode == 13) { Glybe.Forum.Post(<?php echo $topicFetch['id']; ?>, '<?php echo sha1($topicFetch['id']); ?>', '<?php echo sha1($user->Id); ?>', document.getElementById('topic_post_txt').value, document.getElementById('bot').value, <?php echo $p; ?>); Glybe.Forum.TextAreaFocused = false; this.value = ''; }"<?php } ?> id="topic_post_txt" placeholder="Typ hier je reactie op dit topic" style="width: 510px; height: 160px;" onfocus="Glybe.Forum.TextAreaFocused = true;" onblur="Glybe.Forum.TextAreaFocused = false;"></textarea>
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
			<?php }} ?>
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
	jQuery(window).ready(function(){
		<?php if($ps['max'] == $p) { ?>window.setInterval(function(){Glybe.Forum.GetNewMessages(<?php echo $topicFetch['id']; ?>, <?php echo $p; ?>);}, 6000);<?php } ?>
	});
	</script>
<?php
$footerLinks = Array('Forum' => '/forum/index', $foraFetch['caption'] => '/forum/forum?id=' . $foraFetch['id'], htmlspecialchars($topicFetch['caption']) => '/forum/topic?id=' . $topicFetch['id']);
include'content/footer.php';
?>