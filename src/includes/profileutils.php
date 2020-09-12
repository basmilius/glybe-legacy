<?php
include'inc.bootstrap.php';

if(!isset($_POST['_act'])) die();

switch($_POST['_act'])
{
	case "get_guestbook":
		{
			$uid = Glybe::Security($_POST['u']);
			$pid = Glybe::Security($_POST['p']);
			$gbp = ((is_numeric($pid)) ? $pid : 1);
			if($gbp <= 0) $gbp = 1;
			$gbps = Glybe::PaginaSysteem(DB::NumRowsQuery("SELECT * FROM `profile_guestbook` WHERE `profile_id` = '" . $uid . "' AND `deleted` = 'false'"), $gbp, 5);
			
			$gbQuery = DB::Query("SELECT * FROM `profile_guestbook` WHERE `profile_id` = '" . $uid . "' AND `deleted` = 'false' ORDER BY `id` DESC LIMIT " . $gbps['limit'] . ",5");
			if(DB::NumRows($gbQuery) === 0)
			{
				echo'	<center><i>Er is nog niks in dit gastenboek gepost.</i></center>';
			} else {
				echo'	<div class="psystem">';
					foreach($gbps['paginas'] as $key => $value)
					{
						echo'<a href="javascript:void(0);" onclick="Glybe.Profile.GetGuestbook(' . $uid . ', ' . $value[1] . ');"><div class="item">' . $value[0] . '</div></a>';
					}
					echo'	<div class="clear"></div>
				</div>';
				echo'	<table border="0" cellspacing="1" class="gb_table" style="width: 100%;">';
				$i = 0;
				while($gbFetch = DB::Fetch($gbQuery))
				{
					$i = ($i + 1);
					$poster = new User($gbFetch['user_id'], false, false, true);
					$randomId = sha1($poster->Id . rand(10000, 99999) . $gbFetch['id'] . rand(1000, 9999));
					echo'	<tr onmouseover="jQuery(\'div.icon.delete#' . $randomId . '\').show(0);" onmouseout="jQuery(\'div.icon.delete#' . $randomId . '\').hide(0);">
							<td valign="top" style="padding: 4px; width: 48px;">' . $poster->GetAvatar(48) . '</td>
							<td valign="top" style="padding: 4px 2px;">
								<div class="gb_balloon">
									<div class="top"></div>
									<div class="mid">
										<div class="inner_mid">
											<a href="/profiel/' . strtolower($poster->Username) . '"><strong>' . htmlspecialchars((str_replace(" ", "", $poster->GetSetting("displayname")) != "") ? $poster->GetSetting("displayname") : ucfirst($poster->Username)) . '</strong></a><br/>
											' . UBB::Parse($gbFetch['message']) . '<br/>
											<span style="font-size: 9px;" class="gb_time">Geplaatst op ' . strftime('%e %B %Y om %H:%M:%S uur', $gbFetch['post_timestamp']) . '</span>
										</div>
									</div>
									<div class="bot"></div>
									<div class="arrow"></div>
									' . ((isset($user) && $uid == $user->Id) ? '<a href="javascript:void(0);" onclick="Glybe.Overlay.OpenUrlOverlay(\'/data/profile_gbdelete.php\', { pId:' . $uid . ', gId:' . $gbFetch['id'] . ', qId:' . $pid . ' });"><div id="' . $randomId . '" class="icon delete" style="display: none; top: -2px; right: -12px; position: absolute;"></div></a>' : '') . '
								</div>
							</td>
						</tr>';
				}
				echo'	</table>
					<div class="psystem">';
					foreach($gbps['paginas'] as $key => $value)
					{
						echo'<a href="javascript:void(0);" onclick="Glybe.Profile.GetGuestbook(' . $uid . ', ' . $value[1] . ');"><div class="item">' . $value[0] . '</div></a>';
					}
					echo'	<div class="clear"></div>
				</div>';
			}
			die();
		}
	case "get_friends":
		{
			$uid = Glybe::Security($_POST['u']);
			$pid = Glybe::Security($_POST['p']);
			$u = new User($uid, false);
			$gbp = ((is_numeric($pid)) ? $pid : 1);
			if($gbp <= 0) $gbp = 1;
			$gbps = Glybe::PaginaSysteem(DB::NumRowsQuery("SELECT * FROM users_friends WHERE (user_one_id = '" . $u->Id . "' OR user_two_id = '" . $u->Id . "')"), $gbp, 9);
			$friendsY = Array();
			$fQuery = DB::Query("SELECT * FROM users_friends WHERE (user_one_id = '" . $u->Id . "' OR user_two_id = '" . $u->Id . "') ORDER BY id LIMIT " . $gbps['limit'] . ", 9");
			if(DB::NumRows($fQuery) > 0)
			{
				while($fFetch = DB::Fetch($fQuery))
				{
					if($fFetch['user_one_id'] == $u->Id)
					{
						$friendsY[] = $fFetch['user_two_id'];
					}
					else
					{
						$friendsY[] = $fFetch['user_one_id'];
					}
				}		
				echo'	<div class="psystem">';
					foreach($gbps['paginas'] as $key => $value)
					{
						echo'<a href="javascript:void(0);" onclick="Glybe.Profile.GetFriends(' . $uid . ', ' . $value[1] . ');"><div class="item">' . $value[0] . '</div></a>';
					}
					echo'	<div class="clear"></div>
					</div>';
				echo'	<div class="friends">';
					$i = 0;					
					foreach($friendsY as $key => $fUser)
					{
						$myFriends = new User($fUser, false, false, true);
						echo '<a href="/profiel/'.strtolower($myFriends->Username).'">';
						echo '<div class="friend" style="margin-left: 5px; float: left; min-height: 140px; max-width: 114px;">
							' . $myFriends->getAvatar(110);
							echo '<div style="font-weight: bold; width: 100%; word-wrap: break-word; text-align: center;" title="' . htmlentities($myFriends->Username) . '">' . ((str_replace(" ", "", $myFriends->GetSetting("displayname")) != "") ? htmlspecialchars($myFriends->GetSetting("displayname")) : htmlspecialchars(ucfirst($myFriends->Username))) . '</div>';						
						echo '</div>';
						echo '</a>';
						$i++;
					}
					echo '<div style="clear: both;"></div>';			
				echo'	</div>
					<div class="psystem">';
					foreach($gbps['paginas'] as $key => $value)
					{
						echo'<a href="javascript:void(0);" onclick="Glybe.Profile.GetFriends(' . $uid . ', ' . $value[1] . ');"><div class="item">' . $value[0] . '</div></a>';
					}
					echo'	<div class="clear"></div>
				</div>';
			}
			else
			{
				echo '	<center><i>Deze gebruiker heeft nog geen vrienden :(</i></center>';
			}
			die();
		}
		
	case "post_guestbook":
		{
			if(!isset($user)) die();
			$uid = Glybe::Security($_POST['u']);
			$txt = DB::Escape($_POST['t']);
			
			if(strlen(str_replace(" ", "", $txt)) < 2) die();
			if(isset($_SESSION['lastGbPost']) && (time() - $_SESSION['lastGbPost']) < 6) die();
			$_SESSION['lastGbPost'] = time();
			
			if(DB::NumRowsQuery("SELECT 1 FROM users WHERE id = '" . $uid . "'") > 0)
			{
				DB::Query("INSERT INTO `profile_guestbook` (profile_id, user_id, post_timestamp, message) VALUES ('" . $uid . "', '" . $user->Id . "', UNIX_TIMESTAMP(), '" . $txt . "')");
				if($uid != $user->Id)
				{
					$u = new User($uid, false, false, true);
					DB::Query("INSERT INTO `notifications` (`user_id`, `user_from_id`, `icon`, `title`, `message`, `url`, `n_ts`) VALUES ('" . $u->Id . "', '" . $user->Id . "', 'book_add', 'Gastenboek', '<strong>" . DB::Escape(htmlspecialchars(((str_replace(" ", "", $user->GetSetting("displayname")) != "") ? $user->GetSetting("displayname") : $user->Username))) . "</strong><br/>Ik heb iets in je gastenboek geplaatst!', '/profiel/" . strtolower($u->Username) . "', UNIX_TIMESTAMP())");
					if($u->GetSetting("send_pb_on_gb_post") == 'true')
					{
						$msg = "Hoi,\r\nIk heb net iets gepost in het gastenboek op je profiel!\r\nJe kan mijn bericht zien als je naar [url=http://www.glybe.nl/profiel/" . strtolower($u->Username) . "]deze link gaat[/url].\r\n\r\n" . ucfirst($user->Username);
						DB::Query("INSERT INTO `messages` (user_from_id, user_to_id, sended_on, readed_on, subject, message) VALUES ('" . $user->Id . "', '" . $uid . "', UNIX_TIMESTAMP(), '0', 'Ik heb iets geplaatst in je gastenboek!', '" . $msg . "')");
					}
				}
			}
		}
}
?>