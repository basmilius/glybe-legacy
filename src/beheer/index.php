<?php
include'../includes/inc.bootstrap.php';
$page = Array('title' => 'Beheer', 'access' => Array(true, false, 'is_team'));
include'../content/header.php';
include'../content/heading.php';
?>
	<div class="content">
		<div class="container" style="width: 600px;">
			<div class="c_box">
				<div class="heading"><div class="icon house"></div>Welkom terug, <?php echo $user->Realname; ?></div>
				<div class="inner">
					<center>Er zijn momenteel <strong>0</strong> niet-behandelde tickets en <strong>0</strong> niet-behandelde reports!</center>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<div class="error_notif warning">Aan dit beheerders paneel word nog gewerkt, er kunnen dus nog heel wat foutjes zijn of dingen niet gemaakt zijn.</div>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon user_comment"></div>Laatste berichten</div>
				<div class="inner">
					<?php
					$pQuery = DB::Query("SELECT p.*, t.caption topic, u.username uName FROM forum_posts p, forum_topics t, users u WHERE t.id = p.topic_id AND u.id = p.user_id AND p.user_id != '" . $user->Id . "' ORDER BY id DESC LIMIT 15");
					if(DB::NumRows($pQuery) === 0)
					{
						echo'<center><i>Er zijn nog geen posts geplaatst!</i></center>';
					} else {
						echo'	<table class="top_list" border="0" cellspacing="1" style="width: 100%;">';
						echo'	<tr>
								<td style="padding: 2px 5px; width: 22px;"></td>
								<td style="width: 280px; padding: 6px;"><strong>Topic</strong></td>
								<td style="width: 100px; padding: 6px;"><strong>Poster</strong></td>
								<td style="width: 100px; padding: 6px;"><strong>Ga</strong></td>
							</tr>';
						$i = 0;
						while($pFetch = DB::Fetch($pQuery))
						{
							$i++;
							$icon = "folder_blue.png";
							$last = DB::GetRow("SELECT post_timestamp FROM `forum_posts` WHERE `topic_id` = '" . $pFetch['topic_id'] . "' AND `user_id` != '" . ((isset($user)) ? $user->Id : 0) . "' ORDER BY post_timestamp DESC LIMIT 1");
							
							$readed = !(DB::NumRowsQuery("SELECT 1 FROM `forum_readed` WHERE `topic_id` = '" . $pFetch['topic_id'] . "' AND `user_id` = '" . ((isset($user)) ? $user->Id : 0) . "' AND `timestamp` > '" . $last['post_timestamp'] . "'") === 0);
							if($readed) $icon = "folder_grey.png";
							
							echo'	<tr style="background: #' . (($i % 2 == 0) ? 'EEEFFF' : 'FFFFFF') . ';">
									<td style="padding: 2px 5px; width: 22px;"><a href="/forum/postredir?topic=' . $pFetch['topic_id'] . '&ongelezen"><img src="/cache/images/icons/forum/' . $icon . '" class="gui-tooltip" tt-data="Ga naar de post waar je de vorige keer stopte met lezen" alt="Icoon" /></a></td>
									<td style="width: 280px; padding: 6px;">' . $pFetch['topic'] . '</td>
									<td style="width: 100px; padding: 6px;"><a href="/profiel/' . strtolower($pFetch['uName']) . '">' . $pFetch['uName'] . '</a></td>
									<td style="width: 100px; padding: 6px;"><a href="/forum/postredir?pid=' . $pFetch['id'] . '">Ga naar post &raquo;</a></td>
								</tr>';
						}
						echo'	</table>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="container" style="width: 280px;">
			<?php include'_menu.php'; ?>
		</div>
		<div class="clear"></div>
	</div>
<?php
include'../content/footer.php';
?>