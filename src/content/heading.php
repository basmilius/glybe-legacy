<?php
if(!isset($user))
{
	DB::Query("DELETE FROM `users_online` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `u_a` = '" . DB::Escape($_SERVER['HTTP_USER_AGENT']) . "'");
	DB::Query("INSERT INTO `users_online` (user_id, u_a, ip, last_active, last_page) VALUES (0, '" . DB::Escape($_SERVER['HTTP_USER_AGENT']) . "', '" . $_SERVER['REMOTE_ADDR'] . "', CURRENT_TIMESTAMP(), '" . DB::Escape($_SERVER['REQUEST_URI']) . "')");
}
if(isset($user))
{
	$user->UpdateStatus((isset($topicFetch)) ? $topicFetch['id'] : 0);
	DB::Query("UPDATE users SET last_ip = '".$_SERVER['REMOTE_ADDR']."', page_views = page_views+1 WHERE id = '".$user->Id."'");
	$banQuery = DB::Query("SELECT * FROM `users_bans` WHERE `user_id` = '" . $user->Id . "' AND `ban_expire` > UNIX_TIMESTAMP() ORDER BY id DESC");
	$banQuery2 = DB::Query("SELECT * FROM `users_bans` WHERE `user_ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND ip_ban = 1 AND `ban_expire` > UNIX_TIMESTAMP()");
	if(DB::NumRows($banQuery) > 0)
	{
		$user->KillSession($_COOKIE[$glb_settings['cookie_us']], $_COOKIE[$glb_settings['cookie_us']]);
		header("location: /?utm_source=logout_success");
	}
	elseif(DB::NumRows($banQuery2) > 0)
	{
		$user->KillSession($_COOKIE[$glb_settings['cookie_us']], $_COOKIE[$glb_settings['cookie_us']]);
		header("location: /?utm_source=logout_success");
	}
}
?>
	<div class="tooltip" style="display: none;">
		<div class="left"></div>
		<div class="mid"></div>
		<div class="right"></div>
		<div class="arrow"></div>
	</div>
	<div id="overlay"><div id="overlay_background"></div><div id="overlay_container"><div id="overlay_content"></div></div></div>
	<div class="heading">
		<div id="heading_black" style="position: absolute; display: none; top: 0px; left: 0px; right: 0px; bottom: 30px; background: #000000;"></div>
		<div class="heading_content">
			<div class="logo" id="glb_header_logo" style="cursor: pointer;" onclick="window.location = '/';"></div>
			<div class="youtube_player" style="top: -780px; z-index: 1;">
				<?php if(defined('YT_PLAYER_IN_HEADER')) { ?>
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="100%" height="100%" id="heading_player_obj">
					<param name="movie" value="http://www.youtube.com/v/Y7rR-hzLwJ4?enablejsapi=1&playerapiid=obj&version=3&iv_load_policy=3&autoplay=0&controls=0"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowscriptaccess" value="always"></param>
					<param name="wmode" value="transparent"></param>
					<embed id="heading_player_emb" src="http://www.youtube.com/v/Y7rR-hzLwJ4?enablejsapi=1&playerapiid=emb&version=3&iv_load_policy=3&autoplay=0&controls=0" type="application/x-shockwave-flash" width="100%" height="100%" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed>
				</object>
				<?php } ?>
			</div>
		</div>
		<div class="dots" id="heading_dots" style="z-index: 2; display: none;"></div>
		<div class="menu_black">
			<div class="menu_content">
				<?php if(isset($user)) { ?>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/"><div class="caption"><span class="icon house">&nbsp;&nbsp;&nbsp;&nbsp;</span>Home</div></a>
					<div class="submenu">
						<a href="/"><div class="subitem"><span class="icon house">&nbsp;&nbsp;&nbsp;&nbsp;</span>Home</div></a>
						<a href="/ticket/index"><div class="subitem"><span class="icon help">&nbsp;&nbsp;&nbsp;&nbsp;</span>Help!</div></a>
						<a href="/glybe/online"><div class="subitem"><span class="icon status_online">&nbsp;&nbsp;&nbsp;&nbsp;</span><strong><?php echo count(Glybe::GetOnlineUsersAsArray()); ?></strong>&nbsp;Online&nbsp;Leden</div></a>
						<a href="/glybe/over"><div class="subitem"><span class="icon information">&nbsp;&nbsp;&nbsp;&nbsp;</span>Over&nbsp;Glybe</div></a>
						<a href="/glybe/team"><div class="subitem"><span class="icon user_gray">&nbsp;&nbsp;&nbsp;&nbsp;</span>Het&nbsp;Team</div></a>
						<a href="/glybe/partners" class="last"><div class="subitem"><span class="icon arrow_refresh">&nbsp;&nbsp;&nbsp;&nbsp;</span>Linkpartners</div></a>
					</div>
				</div>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/berichten/index"><div class="caption"><span class="icon email">&nbsp;&nbsp;&nbsp;&nbsp;</span>Berichten<?php $bc = DB::NumRowsQuery("SELECT 1 FROM messages WHERE user_to_id = '" . $user->Id . "' AND state != 'deleted' AND readed = 'false'"); echo(($bc > 0) ? ' <strong>(' . $bc . ')</strong>' : ''); ?></div></a>
					<div class="submenu">
						<a href="/berichten/index"><div class="subitem"><span class="icon email">&nbsp;&nbsp;&nbsp;&nbsp;</span>Postvak&nbsp;IN</div></a>
						<a href="/berichten/verzonden"><div class="subitem"><span class="icon email_go">&nbsp;&nbsp;&nbsp;&nbsp;</span>Verzonden</div></a>
						<a href="/berichten/maak" class="last"><div class="subitem"><span class="icon email_edit">&nbsp;&nbsp;&nbsp;&nbsp;</span>Verstuur&nbsp;bericht</div></a>
					</div>
				</div>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/forum/index"><div class="caption"><span class="icon comments">&nbsp;&nbsp;&nbsp;&nbsp;</span>Het Forum</div></a>
					<div class="submenu">
						<a href="/forum/index"><div class="subitem"><span class="icon comments">&nbsp;&nbsp;&nbsp;&nbsp;</span>Het&nbsp;Forum</div></a>
						<a href="/forum/mijn-posts"><div class="subitem"><span class="icon user_comment">&nbsp;&nbsp;&nbsp;&nbsp;</span>Mijn&nbsp;Topics&nbsp;&amp;&nbsp;Berichten</div></a>
						<a href="/forum/statistieken"><div class="subitem"><span class="icon chart_bar">&nbsp;&nbsp;&nbsp;&nbsp;</span>Forum&nbsp;Statistieken</div></a>
						<a href="/forum/forum?id=1" class="last"><div class="subitem"><span class="icon exclamation">&nbsp;&nbsp;&nbsp;&nbsp;</span>Mededelingen</div></a>
					</div>
				</div>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/profiel"><div class="caption"><span class="icon user_green">&nbsp;&nbsp;&nbsp;&nbsp;</span>Mijn Account</div></a>
					<div class="submenu">
						<a href="/profiel"><div class="subitem"><span class="icon user_green">&nbsp;&nbsp;&nbsp;&nbsp;</span>Mijn&nbsp;Profiel</div></a>
						<a href="/vrienden/index"><div class="subitem"><span class="icon group">&nbsp;&nbsp;&nbsp;&nbsp;</span>Mijn&nbsp;Vrienden</div></a>
						<a href="/account/instellingen"><div class="subitem"><span class="icon user_edit">&nbsp;&nbsp;&nbsp;&nbsp;</span>Account&nbsp;Instellingen</div></a>
						<a href="/account/profiel_instellingen"><div class="subitem"><span class="icon color_wheel">&nbsp;&nbsp;&nbsp;&nbsp;</span>Profiel&nbsp;Instellingen</div></a>
						<a href="/account/kladblok"><div class="subitem"><span class="icon page">&nbsp;&nbsp;&nbsp;&nbsp;</span>Mijn&nbsp;Kladblok</div></a>
						<a href="/account/uitloggen?hash=<?php echo $_COOKIE[$glb_settings['cookie_us']]; ?>" class="last"><div class="subitem"><span class="icon door_open">&nbsp;&nbsp;&nbsp;&nbsp;</span>Uitloggen</div></a>
					</div>
				</div>
				<?php if($user->HasPermissions('is_team')){ ?>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/beheer/index"><div class="caption"><span class="icon server">&nbsp;&nbsp;&nbsp;&nbsp;</span>Beheer</div></a>
				</div>
				<?php } ?>
				<div class="sepator"></div>
				<?php } else { ?>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/"><div class="caption"><span class="icon house">&nbsp;&nbsp;&nbsp;&nbsp;</span>Home</div></a>
					<div class="submenu">
						<a href="/"><div class="subitem"><span class="icon house">&nbsp;&nbsp;&nbsp;&nbsp;</span>Home</div></a>
						<a href="/glybe/online"><div class="subitem"><span class="icon status_online">&nbsp;&nbsp;&nbsp;&nbsp;</span><strong><?php echo count(Glybe::GetOnlineUsersAsArray()); ?></strong>&nbsp;Online&nbsp;Leden</div></a>
						<a href="/glybe/over"><div class="subitem"><span class="icon information">&nbsp;&nbsp;&nbsp;&nbsp;</span>Over&nbsp;Glybe</div></a>
						<a href="/glybe/team"><div class="subitem"><span class="icon user_gray">&nbsp;&nbsp;&nbsp;&nbsp;</span>Het&nbsp;Team</div></a>
						<a href="/glybe/partners" class="last"><div class="subitem"><span class="icon arrow_refresh">&nbsp;&nbsp;&nbsp;&nbsp;</span>Linkpartners</div></a>
					</div>
				</div>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/login"><div class="caption"><span class="icon user_go">&nbsp;&nbsp;&nbsp;&nbsp;</span>Inloggen</div></a>
				</div>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/aanmelden"><div class="caption"><span class="icon user_add">&nbsp;&nbsp;&nbsp;&nbsp;</span>Aanmelden</div></a>
				</div>
				<div class="sepator"></div>
				<div class="menu_item">
					<a href="/forum/index"><div class="caption"><span class="icon comments">&nbsp;&nbsp;&nbsp;&nbsp;</span>Het Forum</div></a>
					<div class="submenu">
						<a href="/forum/index"><div class="subitem"><span class="icon comments">&nbsp;&nbsp;&nbsp;&nbsp;</span>Het&nbsp;Forum</div></a>
						<a href="/forum/statistieken"><div class="subitem"><span class="icon chart_bar">&nbsp;&nbsp;&nbsp;&nbsp;</span>Forum&nbsp;Statistieken</div></a>
						<a href="/forum/forum?id=1" class="last"><div class="subitem"><span class="icon exclamation">&nbsp;&nbsp;&nbsp;&nbsp;</span>Mededelingen</div></a>
					</div>
				</div>
				<div class="sepator"></div>
				<?php } ?>
				
				<div style="position: relative; float: right; display: none;" class="heading_player_controls">
					<div id="hp_previous" style="position: relative; float: left;">
						<div class="sepator"></div>
						<div class="menu_item">
							<a href="javascript:void(0);" onclick="Glybe.WebUI.ToolTip.Destroy();Glybe.Sounds.HeadingPlayer.Previous();" class="gui-tooltip" tt-data="Vorige Video"><div class="caption"><div class="icon control_start_blue" style="margin: -2px -3px;"></div><div class="clear"></div></div></a>
						</div>
					</div>
					<div id="hp_play" style="position: relative; float: left;">
						<div class="sepator"></div>
						<div class="menu_item">
							<a href="javascript:void(0);" onclick="Glybe.WebUI.ToolTip.Destroy();Glybe.Sounds.HeadingPlayer.Play();" class="gui-tooltip" tt-data="Afspelen"><div class="caption"><div class="icon control_play_blue" style="margin: -2px -3px;"></div><div class="clear"></div></div></a>
						</div>
					</div>
					<div id="hp_pause" style="position: relative; float: left;">
						<div class="sepator"></div>
						<div class="menu_item">
							<a href="javascript:void(0);" onclick="Glybe.WebUI.ToolTip.Destroy();Glybe.Sounds.HeadingPlayer.Pause();" class="gui-tooltip" tt-data="Pauzeren"><div class="caption"><div class="icon control_pause_blue" style="margin: -2px -3px;"></div><div class="clear"></div></div></a>
						</div>
					</div>
					<div id="hp_next" style="position: relative; float: left;">
						<div class="sepator"></div>
						<div class="menu_item">
							<a href="javascript:void(0);" onclick="Glybe.WebUI.ToolTip.Destroy();Glybe.Sounds.HeadingPlayer.Next();" class="gui-tooltip" tt-data="Volgende Video"><div class="caption"><div class="icon control_end_blue" style="margin: -2px -3px;"></div><div class="clear"></div></div></a>
						</div>
					</div>
					<div id="hp_large" style="position: relative; float: left;">
						<div class="sepator"></div>
						<div class="menu_item" id="hp_large">
							<a href="javascript:void(0);" onclick="Glybe.WebUI.ToolTip.Destroy();Glybe.Sounds.HeadingPlayer.ResizePlayerLarge();" class="gui-tooltip" tt-data="Player groter maken"><div class="caption"><div class="icon arrow_out" style="margin: -2px -3px;"></div><div class="clear"></div></div></a>
						</div>
					</div>
					<div id="hp_small" style="position: relative; float: left;">
						<div class="sepator"></div>
						<div class="menu_item">
							<a href="javascript:void(0);" onclick="Glybe.WebUI.ToolTip.Destroy();Glybe.Sounds.HeadingPlayer.ResizePlayerSmall();" class="gui-tooltip" tt-data="Player kleiner maken"><div class="caption"><div class="icon arrow_in" style="margin: -2px -3px;"></div><div class="clear"></div></div></a>
						</div>
					</div>
					<div class="sepator"></div>
				</div>
				
			</div>
		</div>
	</div>
