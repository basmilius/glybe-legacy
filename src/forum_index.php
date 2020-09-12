<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Forum', 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container epic_large">
			<?php
			if(isset($user) && $user->HasPermissions('forum_report'))
			{
				$rQuery = DB::NumRowsQuery("SELECT * FROM forum_reports WHERE behandeld = 0");
				if($rQuery > 0)
				{
					echo '<a href="/forum/reports"><div class="error_notif error">Er zijn ' . $rQuery . ' onbehandelde reports!</div></a>';
				}
			}
			if((isset($_GET['utm_source']) && $_GET['utm_source'] == 'PostNotExsist'))
			{
				echo '<div class="error_notif error">Deze post bestaat niet (meer)!</div>';
			}
			$catQuery = DB::Query("SELECT * FROM `forum_categories` ORDER BY `id`");
			while($catFetch = DB::Fetch($catQuery))
			{
				if((isset($user) && !$user->HasPermissions($catFetch['min_permission'])) || (!isset($user) && $catFetch['min_permission'] != "see_forum")) continue;
				$foraQuery = DB::Query("SELECT * FROM `forum_foras` WHERE `category_id` = '" . $catFetch['id'] . "' ORDER BY `id`");
				$foraFetch1 = DB::Fetch($foraQuery);
				if((isset($user) && $user->RawData['permission_id'] < $catFetch['min_post_permissions']) || (!isset($user) && $catFetch['min_post_permissions'] != 0))
				{
					
				}
				else
				{
					?>
					<div class="c_box">
						<div class="heading"><?php echo $catFetch['caption']; ?></div>
						<div class="inner">
							<table border="0" cellspacing="0" style="width: 100%;">
								<tr style="background: url(/cache/style_default/images/topic_post_bg.png); #789ABC; color: #FFFFFF; font-weight: bold;">
									<td style="width: 42px;"></td>
									<td style="width: 450px;"><div style="position: relative; padding: 5px 6px 6px 6px;;">Forum</div></td>
									<td style="width: 90px; text-align: center;">Topics</td>
									<td style="width: 90px; text-align: center;">Reacties</td>
									<td style="width: 182px; text-align: center;">Laatste post</td>
								</tr>
								<?php
								$f = 0;
								$foraQuery = DB::Query("SELECT * FROM `forum_foras` WHERE `category_id` = '" . $catFetch['id'] . "' ORDER BY `id`");
								while($foraFetch = DB::Fetch($foraQuery))
								{
									if((isset($user) && !$user->HasPermissions($catFetch['min_permission'])) || (!isset($user) && $catFetch['min_permission'] != "see_forum")) continue;
									if((isset($user) && ($user->RawData['permission_id'] < $catFetch['min_post_permissions']) OR isset($user) && ($user->RawData['permission_id'] < $foraFetch['min_post_permissions'])) || (!isset($user) && $foraFetch['min_post_permissions'] != 0))
									{
																			
									}
									else
									{
										$f++;
										$icon = "unreaded_big.png";
										$topicCount = DB::NumRowsQuery("SELECT 1 FROM `forum_topics` WHERE `state` != 'deleted' AND `forum_id` = '" . $foraFetch['id'] . "'");
										$postCount = DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE `state` != 'deleted' AND `forum_id` = '" . $foraFetch['id'] . "'");
										$readedCount = DB::NumRowsQuery("	SELECT
																			1
																		FROM
																			forum_topics t,
																			forum_readed r
																		WHERE
																			t.id = r.topic_id
																		AND
																			r.timestamp > t.last_post
																		AND
																			t.forum_id = '" . $foraFetch['id'] . "'
																		AND
																			r.user_id = '" . ((isset($user)) ? $user->Id : 0) . "'
																		AND
																			t.state != 'deleted'");
										if($readedCount >= $topicCount)
										{
											$icon = 'readed_big.png';
										}
										
										$lpQuery = DB::Query("SELECT p.*, u.username poster, t.caption topicTitle FROM forum_posts p, forum_topics t, users u WHERE t.id = p.topic_id AND u.id = p.user_id AND p.forum_id = '" . $foraFetch['id'] . "' ORDER BY p.id DESC LIMIT 1");
										$lpFetch = DB::Fetch($lpQuery);
										echo'	<tr>
												<td style="padding: 5px;"><img src="/cache/images/icons/forum/' . $icon . '" /></td>
												<td style="padding: 5px;">
													<a href="/forum/forum?id=' . $foraFetch['id'] . '"><strong>' . $foraFetch['caption'] . '</strong></a><br/>
													<i>' . $foraFetch['description'] . '</i>
												</td>
												<td style="padding: 5px; text-align: center;">' . number_format($topicCount, 0, ",", ".") . '</td>
												<td style="padding: 5px; text-align: center;">' . number_format($postCount, 0, ",", ".") . '</td>
												<td style="padding: 5px; text-align: center;">' . ((DB::NumRows($lpQuery) > 0) ? '<a href="/forum/postredir?pid=' . $lpFetch['id'] . '">In "' . ((strlen($lpFetch['topicTitle']) > 20) ? substr(htmlspecialchars($lpFetch['topicTitle']), 0, 20) . '..' : htmlspecialchars($lpFetch['topicTitle'])) . '" door ' . htmlspecialchars($lpFetch ['poster']) . '</a>' : '<i>Er is nog niks gepost!</i>') . '</td>
											</tr>';
									}
								}
								?>
							</table>
							<?php
							if($f == 0)
							{
								echo'<center style="padding: 5px;"><i>Er zijn geen fora\'s die jij mag zien.</i></center>';
							}
							?>
						</div>
					</div>
				<?php } ?>
			<?php } if(isset($user)) { ?>
			<div class="c_box">
				<div class="inner">
					<input type="button" value="Markeer alles als gelezen" style="float: right;" onclick="window.location = '/forum/gelezen?alles=1';" />
					<div class="clear"></div>
				</div>
			</div>
			<?php } ?>
			<div class="c_box">
				<div class="inner">
					<img src="/cache/images/icons/nuvola-48/cookie.png" style="float: left; position: relative;" />
					<div class="inner" style="float: left; margin-top: 3px; width: 800px;">
						<strong>Vandaag jarig</strong><br/>
						<?php
						$jarArray = Array();
						$jarQuery = DB::Query("	SELECT
												u.*,
												us.displayname uDisplayName
											FROM
												users u,
												users_settings us
											WHERE
												us.birthdate LIKE '" . date("d-m") . "%'
											AND
												us.user_id = u.id
											ORDER BY
												u.username");
						while($jarFetch = DB::Fetch($jarQuery))
						{
							$jarArray[] = $jarFetch;
						}
						if(count($jarArray) > 0)
						{
							foreach($jarArray as $key => $jar)
							{
								echo'	<a href="/profiel/' . strtolower($jar['username']) . '" class="gui-tooltip" tt-data="' . htmlspecialchars($jar['username']) . '">' . htmlspecialchars(((str_replace(" ", "", $jar['uDisplayName']) != "") ? $jar['uDisplayName'] : $jar['username'])) . '</a>' . (((count($jarArray) - 1) == $key) ? '' : (((count($jarArray) - 2) == $key) ? ' en ' : ', '));
							}
						} else {
							echo'	<i>Vandaag is er niemand jarig</i>';
						}
						?>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Forum' => '/forum/index', 'Index' => '/forum/index');
include'content/footer.php';
?>