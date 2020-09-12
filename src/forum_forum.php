<?php
include'includes/inc.bootstrap.php';

if(!isset($_GET['id']))
{
	header("location: /forum/index?foraNotExists");
	die();
}
$id = Glybe::Security($_GET['id']);
$foraQuery = DB::Query("SELECT * FROM `forum_foras` WHERE `id` = '" . $id . "'");
if(DB::NumRows($foraQuery) == 0)
{
	header("location: /forum/index?foraNotExists");
	die();
}
$foraFetch = DB::Fetch($foraQuery);

$p = ((isset($_GET['p']) && is_numeric($_GET['p'])) ? round($_GET['p']) : 1);
if($p <= 0) $p = 1;
$ps = Glybe::PaginaSysteem(DB::NumRowsQuery("SELECT t.* FROM forum_topics t WHERE t.forum_id = '" . $foraFetch['id'] . "' AND t.state != 'deleted'"), $p, 15);

$topicsQuery = DB::Query("SELECT t.*, u.username author FROM forum_topics t, users u WHERE u.id = t.user_id AND t.forum_id = '" . $foraFetch['id'] . "' AND t.state != 'deleted' ORDER BY t.sticky DESC, t.last_post DESC LIMIT " . $ps['limit'] . ", 15");

$page = Array('title' => $foraFetch['caption'], 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';
$query_c = DB::Query("SELECT min_post_permissions FROM forum_categories WHERE id = '" . $foraFetch['category_id'] . "'");
$fetch_c = DB::Fetch($query_c);
?>
<div class="content">
		<div class="container epic_large">
			<div class="c_box">
<?php
if((isset($user) && ($user->RawData['permission_id'] < $foraFetch['min_post_permissions']) OR isset($user) && ($user->RawData['permission_id'] < $fetch_c['min_post_permissions'])) || (!isset($user) && ($fetch_c['min_post_permissions'] != 0 || $foraFetch['min_post_permissions'] != 0)))
{
?>
<div class="heading"><a href="/forum/index">Forum</a> &raquo; Geen toegang</div>
<div class="inner">
	<div class="error_notif error">Je hebt geen rechten om deze categorie te bekijken!</div>
</div>
<?php
}
else
{
?>
				<div class="heading"><a href="/forum/index">Forum</a> &raquo; <?php echo $foraFetch['caption']; ?></div>
				<div class="inner">
					<?php if(isset($user)) { ?>
					<input type="button" value="Maak een topic" style="float: right;" onclick="window.location = '/forum/nieuw?id=<?php echo $foraFetch['id']; ?>';" />
					<input type="button" value="Markeer alles als gelezen" style="float: right;" onclick="window.location = '/forum/gelezen?forum_id=<?php echo $foraFetch['id']; ?>';" />
					<?php } ?>
					<div class="psystem" style="float: left;">
						<?php
						foreach($ps['paginas'] as $key => $value)
						{
							echo'<a href="/forum/forum?id=' . $foraFetch['id'] . '&p=' . $value[1] . '"><div class="item">' . $value[0] . '</div></a>';
						}
						?>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
					<?php
					if(DB::NumRows($topicsQuery) === 0)
					{
						echo'<center><i>Er zijn nog geen topics in dit forum</i></center>';
					} else {
						echo'<table border="0" cellspacing="0" style="width: 100%;">';
						echo'	<tr style="background: url(/cache/style_default/images/topic_post_bg.png); #789ABC; color: #FFFFFF; font-weight: bold;">
								<td style="padding: 6px 6px 7px 6px; width: 22px;"></td>
								<td style="padding: 6px 6px 7px 6px; width: 480px;">Topic</td>
								<td style="padding: 6px 6px 7px 6px; width: 120px; text-align: center;">Auteur</td>
								<td style="padding: 6px 6px 7px 6px; width: 90px; text-align: center;">Reacties</td>
								<td style="padding: 6px 6px 7px 6px; width: 250px; text-align: center;">Laatste Post</td>
							</tr>';
						$i = 0;
						while($topicFetch = DB::Fetch($topicsQuery))
						{
							$i++;
							$icon = "folder_blue.png";
							$readed = !(DB::NumRowsQuery("SELECT 1 FROM `forum_readed` WHERE `topic_id` = '" . $topicFetch['id'] . "' AND `user_id` = '" . ((isset($user)) ? $user->Id : 0) . "' AND `timestamp` > '" . $topicFetch['last_post'] . "'") === 0);
							$posted = !(DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE `topic_id` = '" . $topicFetch['id'] . "' AND `user_id` = '" . ((isset($user)) ? $user->Id : 0) . "'") === 0);
							$lpFetch = DB::Fetch(DB::Query("SELECT p.*, u.username author FROM forum_posts p, users u WHERE u.id = p.user_id AND p.state != 'deleted' AND p.topic_id = '" . $topicFetch['id'] . "' ORDER BY p.id DESC LIMIT 1"));
							if($readed) $icon = "folder_grey.png";
							if($topicFetch['state'] == 'closed') $icon = str_replace(".png", "_locked.png", $icon);
							else if($topicFetch['sticky'] != '0') $icon = str_replace(".png", "_sticky.png", $icon);
							if($posted) $icon = str_replace(".png", "_posted.png", $icon);
							echo'	<tr style="background: #' . (($i % 2 == 0) ? 'EEEFFF' : 'FFFFFF') . ';">
									<td style="padding: 2px 5px; width: 22px;"><a href="/forum/postredir?topic=' . $topicFetch['id'] . '&ongelezen"><img src="/cache/images/icons/forum/' . $icon . '" class="gui-tooltip" tt-data="Ga naar de post waar je de vorige keer stopte met lezen" alt="Icoon" /></a></td>
									<td style="padding: 5px; width: 480px;"><a href="' . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], 1) . '">' . htmlspecialchars($topicFetch['caption']) . '</a></td>
									<td style="padding: 5px; width: 120px; text-align: center;"><a href="/profiel/' . strtolower(htmlspecialchars($topicFetch['author'])) . '">' . ucfirst(htmlspecialchars($topicFetch['author'])) . '</a></td>
									<td style="padding: 5px; width: 90px; text-align: center;">' . number_format((DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE `topic_id` = '" . $topicFetch['id'] . "'")), 0, ",", ".") . '</td>
									<td style="padding: 5px; width: 250px; text-align: center;"><a href="/forum/postredir?pid=' . $lpFetch['id'] . '">' . Glybe::TimeAgo($lpFetch['post_timestamp']) . ' door ' . $lpFetch['author'] . '</a></td>
								</tr>';
						}
						echo'</table>';
					}
					?>
					<?php if(isset($user)) { ?>
					<input type="button" value="Maak een topic" style="float: right;" onclick="window.location = '/forum/nieuw?id=<?php echo $foraFetch['id']; ?>';" />
					<input type="button" value="Markeer alles als gelezen" style="float: right;" onclick="window.location = '/forum/gelezen?forum_id=<?php echo $foraFetch['id']; ?>';" />
					<?php } ?>
					<div class="psystem" style="float: left;">
						<?php
						foreach($ps['paginas'] as $key => $value)
						{
							echo'<a href="/forum/forum?id=' . $foraFetch['id'] . '&p=' . $value[1] . '"><div class="item">' . $value[0] . '</div></a>';
						}
						?>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
<?php
}
?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Forum' => '/forum/index', $foraFetch['caption'] => '/forum/forum?id=' . $foraFetch['id']);
include'content/footer.php';
?>