<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Mijn topics & berichten', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon folder"></div>Al jouw Topics</div>
				<div class="inner">
					<?php
					$topicQuery = DB::Query("SELECT * FROM `forum_topics` WHERE `user_id` = '" . $user->Id . "' AND `state` != 'deleted' ORDER BY `id` DESC");
					while($topicFetch = DB::Fetch($topicQuery))
					{
						echo'	<a href="' . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], 1) . '"><div class="nav_link">' . htmlspecialchars($topicFetch['caption']) . '</div></a>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon user_comment"></div>Laatste 20 topics waarin je hebt gereageerd</div>
				<div class="inner">
					<?php
					$postsQuery = DB::Query("	SELECT
												t.id topicId,
												t.caption topicTitle,
												p.id postId
											FROM
												(SELECT * FROM forum_posts ORDER BY id DESC) p,
												forum_topics t
											WHERE
												p.user_id = '" . $user->Id . "'
											AND
												t.id = p.topic_id
											AND
												t.state != 'deleted'
											AND
												p.state != 'deleted'
											GROUP BY
												p.topic_id
											ORDER BY
												p.id DESC
											LIMIT
												20");
					while($postsFetch = DB::Fetch($postsQuery))
					{
						echo'	<a href="/forum/postredir?pid=' . $postsFetch['postId'] . '"><div class="nav_link">' . $postsFetch['topicTitle'] . '</div></a>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Forum' => '/forum/index', 'Mijn topics & berichten' => '/forum/mijn-posts');
include'content/footer.php';
?>