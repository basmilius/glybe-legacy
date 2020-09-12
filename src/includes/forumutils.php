<?php
include'inc.bootstrap.php';

if(!isset($_POST['_act']))
{
	header("HTTP/1.1 500 Internal Server Error");
	die();
}

$_act = Glybe::Security($_POST['_act']);

switch($_act)
{
	case "post_message":
		{
			$topicId = Glybe::Security($_POST['topic_id']);
			$stokenA = Glybe::Security($_POST['stoken_a']);
			$stokenB = Glybe::Security($_POST['stoken_b']);
			$message = DB::Escape($_POST['message']);
			$page = Glybe::Security($_POST['pageNum']);
			$msgLength = strlen(str_replace(" ", "", $message));
			$topicQuery = DB::Query("SELECT * FROM `forum_topics` WHERE `id` = '" . $topicId . "'");
			
			if($msgLength < 2 || $msgLength > 50000) die("5");
			if(sha1($topicId) != $stokenA) die("2");
			if(!isset($user)) die("0");
			if(DB::NumRows($topicQuery) === 0) die("3");
			$topicFetch = DB::Fetch($topicQuery);
			if($topicFetch['state'] == "deleted") die("3");
			if($topicFetch['state'] == "closed") die("4");
			
			$dubbelCheck = DB::Query("SELECT user_id, message FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "' ORDER BY post_timestamp DESC LIMIT 1");
			$dubbelCheckFetch = DB::Fetch($dubbelCheck);
						
			if($dubbelCheckFetch['user_id'] == $user->Id && strtolower($dubbelCheckFetch['message']) == strtolower($message)){
				die("2");
			}
			
			$user->UpdateStatus($topicId, "/forum/topic?id=" . $topicId . "&p=" . $page);
			
			if(isset($_SESSION['lastPost']) && (time() - $_SESSION['lastPost']) < 6) die("1");
			$_SESSION['lastPost'] = time();
			
			if($_POST['bot'] == 1)
			{
				$bot = 3;
			}
			else
			{
				$bot = $user->Id;
			}			
			DB::Query("INSERT INTO `forum_posts` (ip, user_id,forum_id,topic_id,message,post_timestamp) VALUES ('".$_SERVER['REMOTE_ADDR']."', '" . $bot . "', '" . $topicFetch['forum_id'] . "', '" . $topicFetch['id'] . "', '" . $message . "', UNIX_TIMESTAMP())");
			DB::Query("UPDATE `forum_topics` SET `last_post` = (UNIX_TIMESTAMP() - 1) WHERE `id` = '" . $topicFetch['id'] . "'");
			$totalPosts = DB::NumRowsQuery("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicFetch['id'] . "' " . ((!$user->HasPermissions("forum_topic_delete")) ? "AND p.state != 'deleted'" : "") . "");
			if(DB::NumRowsQuery("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicFetch['id'] . "'") >= 10000)
			{
				DB::Query("UPDATE forum_topics SET state = 'closed' WHERE id = '" . $topicFetch['id'] . "'");
				die("7");
			}
			if(ceil($totalPosts / 20) > $page)
				die("7");
			die("6");
		}
	
	case "get_messages":
		{
			$sinceId = ((isset($_POST['since_id']) && is_numeric($_POST['since_id'])) ? Glybe::Security($_POST['since_id']) : 0);
			$topicId = Glybe::Security($_POST['topic_id']);
			$p = Glybe::Security($_POST['p']);
			$topicQuery = DB::Query("SELECT * FROM `forum_topics` WHERE `id` = '" . $topicId . "'");
			$topicFetch = DB::Fetch($topicQuery);
			$foraQuery = DB::Query("SELECT * FROM `forum_foras` WHERE `id` = '" . $topicFetch['forum_id'] . "'");
			$foraFetch = DB::Fetch($foraQuery);
			//$postsQuery = DB::Query("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicId . "' AND p.state != 'deleted' AND p.post_timestamp >= '" . $_SESSION['lastTopicCheck'] . "' ORDER BY p.id");
			if(isset($user) && $user->HasPermissions('is_team'))
			{
				$postsQuery = DB::Query("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicId . "' AND p.id > '" . (($sinceId != 0) ? $sinceId : $_SESSION['lastPostId']) . "' ORDER BY p.first_post DESC, p.id");
			}
			else
			{
				$postsQuery = DB::Query("SELECT p.* FROM forum_posts p WHERE p.topic_id = '" . $topicId . "' AND p.state != 'deleted' AND p.id > '" . (($sinceId != 0) ? $sinceId : $_SESSION['lastPostId']) . "' ORDER BY p.first_post DESC, p.id");
			}
			$_SESSION['lastTopicCheck'] = time();
			$lastId = 0;
			
					$start = $_SESSION['lastPostCounter'];
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
						
						echo'	<a name="' . $postFetch['id'] . '"></a>';						
						echo'	<table class="postTable" id="' . $postIdString . '" border="0" cellspacing="0" style="width: 100%; border: 1px solid #006D9C; border-top: none; border-bottom: none;">
								<tr style="background: url(/cache/style_default/images/topic_post_bg.png); #789ABC; color: #FFFFFF; font-weight: bold;">
									<td style="padding: 6px; width: 160px;"><a href="/forum/postredir?pid=' . $postFetch['id'] . '" style="color: #FFFFFF;">Post #' . number_format($start, 0, ",", ".") . '</a></td>
									<td style="padding: 6px;" valign="center">
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
						unset($lastEdit);
						$lastId = $postFetch['id'];
						$_SESSION['lastPostId'] = $lastId;
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
			
			die();
		}
	case "get_ubb_message":
		{
			$postId = Glybe::Security($_POST['pid']);
			$postQuery = DB::Query("SELECT p.*, u.username author FROM forum_posts p, users u WHERE p.id = '" . $postId . "' AND u.id = p.user_id");
			$postFetch = DB::Fetch($postQuery);
			echo '[quote="' . $postFetch['author'] . '" id=' . $postFetch['id'] . '] ' . str_replace("	", "    ", html_entity_decode($postFetch['message'])) . '[/quote]';
			break;
		}
	case "PostEdit":
		{
			$query = DB::Query("SELECT * FROM forum_posts WHERE id = '".DB::Escape($_POST['pid'])."'");
			if(DB::NumRows($query) > 0)
			{
				$fetch = DB::fetch($query);				
				if(isset($user) && $fetch['user_id'] != $user->Id && !$user->HasPermissions("forum_topic_edit")) die();
				
				$query2 = DB::Query("SELECT * FROM forum_topics WHERE id = '".$fetch['topic_id']."'");
				$fetch2 = DB::Fetch($query2);
				if($fetch2['state'] == 'closed' || $fetch2['state'] == 'deleted')
				{
					echo '<div class="error_notif error">Je kan geen berichten bewerken als het topic gesloten is.</div>';
				} else if($_POST['start'] != 1)
				{					
					echo '<textarea style="height: 150px; width: 95%;" id="msg_' . $fetch['id'] . '">' . $fetch['message'] . '</textarea><br /><input type="submit" onClick="Glybe.Forum.PostEditSubmit(\'' . $fetch['id'] . '\', \'' . $_POST['fid'] . '\', document.getElementById(\'msg_' . $fetch['id'] .  '\').value, \'0\');" value="Opslaan" />&nbsp;&nbsp;<input type="submit" onClick="Glybe.Forum.PostEditCancel(\'' . $fetch['id'] . '\', \'' . $_POST['fid'] . '\', \'\');" value="Annuleren" />';
				}
				else
				{
					echo '<input type="text" style="width: 95%;" id="caption" value="'.htmlspecialchars($fetch2['caption']).'" /><br /><textarea style="height: 150px; width: 95%;" id="msg_' . $fetch['id'] . '">' . $fetch['message'] . '</textarea><br /><input type="submit" onClick="Glybe.Forum.PostEditSubmit(\'' . $fetch['id'] . '\', \'' . $_POST['fid'] . '\', document.getElementById(\'msg_' . $fetch['id'] .  '\').value, document.getElementById(\'caption\').value, \'1\');" value="Opslaan" />&nbsp;&nbsp;<input type="submit" onClick="Glybe.Forum.PostEditCancel(\'' . $fetch['id'] . '\', \'' . $_POST['fid'] . '\');" value="Annuleren" />';
				}
			}
			break;
		}
	case "PostEditCancel":
		{
			$query = DB::Query("SELECT * FROM forum_posts WHERE id = '".DB::Escape($_POST['pid'])."'");
			if(DB::NumRows($query) > 0)
			{
				$fetch = DB::fetch($query);
				echo UBB::Parse($fetch['message']);
				if(!empty($fetch['last_edit_by']))
				{
					$userLE = new User($fetch['last_edit_by'], false);
					echo '<br /><br />';
					echo 'Laatst bewerkt door <a href="/profiel/'.strtolower($userLE->Username).'">'.$userLE->Username.'</a> op '.strftime('%e %B %Y om %H:%M:%S uur', $fetch['last_edit']).'';
					if(isset($user) && $user->HasPermissions('is_team'))
					{
						echo '<a href="javascript:void(0);" onclick="Glybe.Forum.Edits(' . $fetch['id'] . ');"><br />Laat bewerk geschiedenis zien!</a>';
					}
				}
			}
			break;
		}
	case "PostEditSubmit":
		{
			if(isset($_POST['message']))
			{
				$query = DB::Query("SELECT * FROM forum_posts WHERE id = '".DB::Escape($_POST['pid'])."'");
				if(DB::NumRows($query) > 0)
				{
					$fetch = DB::fetch($query);
					if(isset($user) && $fetch['user_id'] != $user->Id && !$user->HasPermissions("forum_topic_edit") || !isset($user))
					{
						echo'<div class="error_notif error">Dit bericht kan jij niet aanpassen!</div>';
						echo UBB::Parse($fetch['message'], true);
						die();
					}
					$time = time();
					if(!empty($_POST['caption']))
					{
						DB::Query("UPDATE forum_topics SET caption = '".DB::Escape($_POST['caption'])."' WHERE id = '".DB::Escape($fetch['topic_id'])."'");
					}
					DB::Query("INSERT INTO forum_posts_old (user_id,forum_id,topic_id,message,post_timestamp, post_id) VALUES ('" . $user->Id . "', '" . $fetch['forum_id'] . "', '" . $fetch['id'] . "', '" . DB::Escape($fetch['message']) . "', UNIX_TIMESTAMP(), '".$fetch['id']."')");
					DB::Query("UPDATE forum_posts SET message = '" . DB::Escape($_POST['message']) . "', last_edit = '".$time."', last_edit_by = '".$user->Id."' WHERE id = '".DB::Escape($_POST['pid'])."'");
					echo UBB::Parse($_POST['message'], $user->HasPermissions("is_team"));
					echo '<br /><br />';
					echo '<em>Laatst bewerkt door <a href="/profiel/'.strtolower($user->Username).'">'.$user->Username.'</a> op '.strftime('%e %B %Y om %H:%M:%S uur', $time).'</em>';
					if($user->HasPermissions('is_team'))
					{
						echo '<a href="javascript:void(0);" onclick="Glybe.Forum.Edits(' . $_POST['pid'] . ');"><br />Laat bewerk geschiedenis zien!</a>';
					}
				}
			}
			break;
		}
	case "PostDel":
		{
			if($user->HasPermissions('forum_post_delete'))
			{
				DB::Query("UPDATE forum_posts SET state = 'deleted' WHERE id = '".DB::Escape($_POST['pid'])."'");
				echo '<div class="error_notif error">Dit bericht is verborgen!</div>';
			}
			break;
		}
	case "PostDelBack":
		{
			if($user->HasPermissions('forum_post_delete'))
			{
				DB::Query("UPDATE forum_posts SET state = 'normal' WHERE id = '".DB::Escape($_POST['pid'])."'");
				echo '<div class="error_notif success">Bericht is teruggehaald!</div>';
			}
			break;
		}
	case "FirstPost":
		{
			if($user->HasPermissions('forum_first_post'))
			{
				DB::Query("UPDATE forum_posts SET first_post = 1 WHERE id = '".DB::Escape($_POST['pid'])."'");
			}
			break;
		}
	case "FirstPost2":
		{
			if($user->HasPermissions('forum_first_post'))
			{
				DB::Query("UPDATE forum_posts SET first_post = 0 WHERE id = '".DB::Escape($_POST['pid'])."'");
			}
			break;
		}
	case "give_respect":
		{
			$uId = DB::Escape($_POST['ui']);
			if(is_numeric($uId) && isset($user) && $user->Id != $uId)
			{
				if(DB::NumRowsQuery("SELECT 1 FROM users WHERE id = '" . $uId . "'") > 0)
				{
					if(DB::NumRowsQuery("SELECT 1 FROM users_respectgiven WHERE user_id = '" . $user->Id . "' AND user_to_id = '" . $uId . "' AND `time` = '" . date("d-m-Y") . "'") === 0)
					{
						DB::Query("UPDATE users_settings SET respect = respect + 1, punten = punten + 0.5 WHERE user_id = '" . $uId . "'");
						DB::Query("INSERT INTO `notifications` (`user_id`, `user_from_id`, `icon`, `title`, `message`, `n_ts`) VALUES ('" . $uId . "', '" . $user->Id . "', 'star', 'Respect!', '<strong>" . DB::Escape(htmlspecialchars(((str_replace(" ", "", $user->GetSetting("displayname")) != "") ? $user->GetSetting("displayname") : $user->Username))) . "</strong><br/>Ik heb je net respect gegeven!', UNIX_TIMESTAMP())");
						DB::Query("INSERT INTO users_respectgiven (user_id, user_to_id, `time`) VALUES ('" . $user->Id . "', '" . $uId . "', '" . date("d-m-Y") . "')");
						echo'ok';
					} else {
						echo'Je hebt deze gebruiker vandaag al respect gegeven!';
					}
				} else {
					echo'Ongeldig verzoek';
				}
			} else {
				echo'Ongeldig verzoek';
			}
			break;
		}
}
?>