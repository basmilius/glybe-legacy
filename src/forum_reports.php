<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Forum reports', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
if($user->HasPermissions('forum_report'))
{
?>
	<div class="content">
		<div class="container epic_large">
			<div class="c_box">
				<div class="heading"><div class="icon error"></div>Forum reports</div>
				<div class="inner">
					<?php
					if(isset($_GET['id']))
					{
						if(isset($_POST['submit']))
						{
							$time = time();
							DB::Query("UPDATE forum_reports SET behandeld = 1, behandeld_door = '" . DB::Escape($user->Id) . "', behandeld_date = '" . $time . "' WHERE id = '" . DB::Escape($_GET['id']) . "'");
							echo '<div class="error_notif success">Report succesvol behandeld!</div>';
						}
						$query = DB::Query("SELECT * FROM forum_reports WHERE id = '" . DB::Escape($_GET['id']) . "'");
						if(DB::NumRows($query) > 0)
						{						
							$fetch = DB::Fetch($query);
							
							$query3 = DB::Query("SELECT * FROM forum_posts WHERE id = '" . $fetch['post_id'] . "'");
							$fetch3 = DB::Fetch($query3);
							
							$query2 = DB::Query("SELECT * FROM forum_topics WHERE id = '" . $fetch3['topic_id'] . "'");
							$fetch2 = DB::Fetch($query2);
							
							$user2 = new User($fetch['user_from'], false);
							$user3 = new User($fetch3['user_id'], false);
							if($fetch['behandeld'] == 1)
							{
								$user4 = new User($fetch['behandeld_door'], false);
								echo '<h2>Report behandeld door ' . htmlspecialchars($user4->Username) . ' op ' . strftime('%e %B %Y om %H:%M:%S uur', $fetch['behandeld_date']) . '</h2><br />';
							}
							echo '<strong>Report van ' . htmlspecialchars($user2->Username) . ' op ' . strftime('%e %B %Y om %H:%M:%S uur', $fetch['date']) . ' (' . Glybe::TimeAgo($fetch['date']) . ')</strong><br /><br />';
							echo 'Topic: '.htmlspecialchars($fetch2['caption']).'<br />';
							echo 'Autheur: ' . $user3->Username . '<br /><br />';
							echo '<a target="_new" href="/forum/postredir?pid=' . $fetch['post_id'] . '">Link naar post in topic: <strong>'.htmlspecialchars($fetch2['caption']).'</strong></a><br /><br />';
							echo '<strong>Post:</strong><br />';
							echo UBB::Parse($fetch3['message']);
							echo '<br /><br /><strong>Reden:</strong><br />';
							echo UBB::Parse($fetch['reason']);
							echo '<br /><hr /><br />';
							?>
							<form action="" method="POST">
								<input type="submit" name="submit" value="Behandelen" />
							</form>
							<br /><br />
							<a href="/forum/reports">Terug naar overzicht!</a>
							<?php
						}
						else
						{
							echo '<div class="error_notif error">Report niet gevonden!</div>';
						}
					}
					else
					{
						$query = DB::Query("SELECT * FROM forum_reports ORDER BY behandeld ASC, id DESC");
						while($fetch = DB::Fetch($query))
						{
							$user2 = new User($fetch['user_from'], false);
							echo '<a href="/forum/reports?id=' . $fetch['id'] . '">';
							if($fetch['behandeld'] == 0)
							{
								echo '<strong>';
							}
								echo 'Report van ' . htmlspecialchars($user2->Username) . ' - ' . Glybe::TimeAgo($fetch['date']) . '';
							if($fetch['behandeld'] == 0)
							{
								echo '</strong>';
							}
							echo '</a><br />';
						}
					}
					?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
}
else
{
	header('Location: /home');
}
$footerLinks = Array('Forum' => '/forum/index', 'Reports' => '/forum/reports');
include'content/footer.php';
?>