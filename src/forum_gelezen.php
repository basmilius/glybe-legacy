<?php
include'includes/inc.bootstrap.php';

if(!isset($user)) die();

if(isset($_GET['forum_id']) && is_numeric($_GET['forum_id']))
{
	$forumId = Glybe::Security($_GET['forum_id']);
	
	$topicsQuery = DB::Query("SELECT * FROM `forum_topics` WHERE `forum_id` = '" . $forumId . "'");
	while($topicFetch = DB::Fetch($topicsQuery))
	{
		if(DB::NumRowsQuery("SELECT 1 FROM `forum_readed` WHERE `user_id` = '" . $user->Id . "' AND `topic_id` = '" . $topicFetch['id'] . "'") === 0)
		{
			DB::Query("INSERT INTO `forum_readed` (forum_id, topic_id, user_id, post_id, `timestamp`) VALUES ('" . $forumId . "', '" . $topicFetch['id'] . "', '" . $user->Id . "', (SELECT id FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "' ORDER BY id DESC LIMIT 1), UNIX_TIMESTAMP())");
		} else {
			DB::Query("UPDATE `forum_readed` SET `post_id` = (SELECT id FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "' ORDER BY id DESC LIMIT 1), `timestamp` = UNIX_TIMESTAMP() WHERE `user_id` = '" . $user->Id . "' AND `topic_id` = '" . $topicFetch['id'] . "'");
		}
	}
	
	header("location: /forum/forum?id=" . $forumId);
}else if(isset($_GET['alles']) && is_numeric($_GET['alles']))
{
	$forumsQuery = DB::Query("SELECT * FROM forum_foras");
	while($forumFetch = DB::Fetch($forumsQuery))
	{
		$forumId = Glybe::Security($forumFetch['id']);
		
		$topicsQuery = DB::Query("SELECT * FROM `forum_topics` WHERE `forum_id` = '" . $forumId . "'");
		while($topicFetch = DB::Fetch($topicsQuery))
		{
			if(DB::NumRowsQuery("SELECT 1 FROM `forum_readed` WHERE `user_id` = '" . $user->Id . "' AND `topic_id` = '" . $topicFetch['id'] . "'") === 0)
			{
				DB::Query("INSERT INTO `forum_readed` (forum_id, topic_id, user_id, post_id, `timestamp`) VALUES ('" . $forumId . "', '" . $topicFetch['id'] . "', '" . $user->Id . "', (SELECT id FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "' ORDER BY id DESC LIMIT 1), UNIX_TIMESTAMP())");
			} else {
				DB::Query("UPDATE `forum_readed` SET `post_id` = (SELECT id FROM forum_posts WHERE topic_id = '" . $topicFetch['id'] . "' ORDER BY id DESC LIMIT 1), `timestamp` = UNIX_TIMESTAMP() WHERE `user_id` = '" . $user->Id . "' AND `topic_id` = '" . $topicFetch['id'] . "'");
			}
		}
	}
	header("location: /forum/index");
}
?>