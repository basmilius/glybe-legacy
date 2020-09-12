<?php
include'includes/inc.bootstrap.php';

if(isset($_GET['pid']) && is_numeric($_GET['pid']))
{
	$postId = Glybe::Security($_GET['pid']);
	if(isset($user) && $user->HasPermissions('is_team'))
	{
		$postsQuery = DB::Query("SELECT * FROM `forum_posts` WHERE `id` = '" . $postId . "'");
	}
	else
	{
		$postsQuery = DB::Query("SELECT * FROM `forum_posts` WHERE `state` != 'deleted' AND `id` = '" . $postId . "'");
	}
	if(DB::NumRows($postsQuery) === 0)
	{
		header('Location: /forum/index?utm_source=PostNotExsist');
	}
	else
	{
		$postsFetch = DB::Fetch($postsQuery);
		$topicQuery = DB::Query("SELECT * FROM `forum_topics` WHERE `id` = '" . $postsFetch['topic_id'] . "'");
		$topicFetch = DB::Fetch($topicQuery);
		$allpostQuery = DB::Query("SELECT id FROM `forum_posts` WHERE `topic_id` = '" . $topicFetch['id'] . "'" . ((isset($user) && $user->HasPermissions('is_team')) ? '' : " AND `state` != 'deleted'") . " ORDER BY `id`");
		$postCount = 0;
		while($allpostFetch = DB::Fetch($allpostQuery))
		{
			$postCount = ($postCount + 1);
			if($allpostFetch['id'] == $postId) break;
		}
		$page = ceil($postCount / 20);
		
		//header("location: /forum/topic?id=" . $topicFetch['id'] . "&p=" . $page . "#" . $postId);
		header("location: " . Glybe::TopicUrl($topicFetch['id'], $topicFetch['caption'], $page) . "#" . $postId);
	}
} else if(isset($_GET['topic']) && is_numeric($_GET['topic']) && isset($_GET['ongelezen']))
{
	$topicId = Glybe::Security($_GET['topic']);
	$gelQuery = DB::Query("SELECT * FROM `forum_readed` WHERE `user_id` = '" . ((isset($user)) ? $user->Id : 0) . "' AND `topic_id` = '" . $topicId . "'");
	if(DB::NumRows($gelQuery) === 0)
	{
		header("location: /forum/topic?id=" . $topicId);
	} else {
		$gelFetch = DB::Fetch($gelQuery);
		header("location: /forum/postredir?pid=" . $gelFetch['post_id']);
	}
} else if(isset($_GET['topic']) && is_numeric($_GET['topic']) && isset($_GET['laatste']))
{
	$topicId = Glybe::Security($_GET['topic']);
	$gelQuery = DB::Query("SELECT * FROM `forum_posts` WHERE `topic_id` = '" . $topicId . "' ORDER BY `id` DESC");
	if(DB::NumRows($gelQuery) === 0)
	{
		header("location: /forum/topic?id=" . $topicId);
	} else {
		$gelFetch = DB::Fetch($gelQuery);
		header("location: /forum/postredir?pid=" . $gelFetch['id']);
	}
}
?>